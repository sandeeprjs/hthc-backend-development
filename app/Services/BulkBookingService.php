<?php

namespace App\Services;

use App\Booking;
use App\BulkBooking;
use App\Consignment;
use App\Customer;
use App\CustomerOffice;
use App\Delivery;
use App\Http\Helpers\AppHelper;
use App\Imports\BookingImport;
use App\StatusHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BulkBookingService
{
    /**
     * Cache service instance
     */
    protected $cacheService;

    /**
     * Notification service instance
     */
    protected $notificationService;

    /**
     * Default pagination limit
     */
    const PAGINATION_LIMIT = 20;

    /**
     * Create a new service instance.
     *
     * @param CacheService $cacheService
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(CacheService $cacheService, NotificationService $notificationService)
    {
        $this->cacheService = $cacheService;
        $this->notificationService = $notificationService;
    }

    /**
     * Validate import data
     *
     * @param Request $request
     * @return void
     */
    public function validateImportData(Request $request)
    {
        $rules = [
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'nullable|string',
            'sender_area' => 'nullable|string',
            'sender_pincode_id' => 'required|exists:pincodes,id',
            'sender_city' => 'nullable|string',
            'sender_district' => 'nullable|string',
            'sender_state' => 'nullable|string',
            'sender_country' => 'nullable|exists:countries,id',
            'sender_sms' => 'nullable|boolean',
            'sender_mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'sender_email' => 'nullable|email',
            'captured_weight' => 'required_if:consg_type,dox|numeric',
            'booking_user_id' => 'required|exists:users,id',
            'booked_amount' => 'required|numeric',
            'risk_covered' => 'nullable|boolean',
            'declared_consg_value' => 'required_if:risk_covered,1|numeric',
            'remarks' => 'nullable|string',
            'book_for_partner' => 'nullable|boolean',
            'fr_id' => 'required_if:book_for_partner,1|exists:franchisees,id',
            'excel' => 'required|file|mimes:xlsx,xls,csv'
        ];

        Validator::make($request->all(), $rules)->validate();
    }

    /**
     * Import bookings from Excel
     *
     * @param Request $request
     * @return array
     */
    public function importBookings(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Determine office type and ID
            $partnersBookings = $request->input('book_for_partner');
            if ($partnersBookings) {
                $officeType = 'FR';
                $officeId = $request->input('fr_id');
                $request->request->add(['origin_office_type' => 'FR']);
            } else {
                $officeType = $user->office_type;
                $officeId = $user->office_id;
                $request->request->add(['origin_office_type' => $officeType]);
            }

            // Find or create customer
            $customer = Customer::where('id', $request->input('customer_id'))->first();

            if (!$customer) {
                $customer = new Customer();
                $customer->code = $request->input('customer_id');
                $customer->customer_name = $request->input('sender_name');
                $customer->city = $request->input('sender_city');
                $customer->state = $request->input('sender_state');
                $customer->add_line_1 = $request->input('sender_address');
                $customer->add_line_2 = $request->input('sender_area');
                $customer->district = $request->input('sender_district');
                $customer->pincode_id = $request->input('sender_pincode_id');
                $customer->country_id = $request->input('sender_country');
                $customer->mobile_number = $request->input('sender_mobile_number');
                $customer->email = $request->input('sender_email');
                $customer->save();

                CustomerOffice::create([
                    'customer_id' => $customer->id,
                    'office_type' => $officeType,
                    'office_id' => $officeId
                ]);

                $customerId = $customer->id;
            } else {
                $customerId = $request->input('customer_id');
            }

            // Import bookings from Excel
            $import = new BookingImport($request->except('_token', 'excel'), $customerId);

            Excel::import($import, $request->file('excel'));
            DB::commit();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            return [
                'success' => true,
                'message' => 'Data uploaded successfully. Please review the uploaded data.',
                'batch_id' => $import->getBatchId()
            ];
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            DB::rollBack();
            $failures = $e->failures();
            $errorMessage = 'Error in Excel import: ';

            foreach ($failures as $failure) {
                $errorMessage .= 'Row ' . $failure->row() . ': ' . implode(', ', $failure->errors()) . '; ';
            }

            return [
                'success' => false,
                'message' => $errorMessage
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to import: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get validation data
     *
     * @param Request $request
     * @param int $batchId
     * @return array
     */
    public function getValidationData(Request $request, $batchId)
    {
        $hasError = $request->input('has_error', 0);

        $bookings = BulkBooking::where('batch_id', $batchId)
            ->where('has_error', $hasError)
            ->orderBy('updated_at', 'desc')
            ->paginate(self::PAGINATION_LIMIT);

        return compact('bookings', 'batchId');
    }

    /**
     * Create bulk bookings from validated data
     *
     * @param int $batchId
     * @return array
     */
    public function createBulkBookings($batchId)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();
            $bulkBookingIds = BulkBooking::where('batch_id', $batchId)->pluck('id');
            $bulkBookings = BulkBooking::where('batch_id', $batchId)
                ->where('has_error', 0)
                ->get();

            if ($bulkBookings->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No valid bookings found to process'
                ];
            }

            $count = 0;
            $customerName = null;
            $customerEmail = null;

            foreach ($bulkBookings as $bulkBooking) {
                // Generate new consignment number
                $consignment = AppHelper::generateSingleConsignment(
                    $user->office_type,
                    $user->office_id,
                    $batchId
                );

                if (!$consignment) {
                    throw new \Exception('Failed to generate consignment number');
                }

                // Create booking
                $booking = new Booking();

                // Sender details
                $customerEmail = $bulkBooking->email;
                $customerName = $bulkBooking->customer_name;

                $booking->consg_number = $consignment->consg_number;
                $booking->consg_type = $bulkBooking->consg_type;
                $booking->subscription_id = $bulkBooking->subscription_id;
                $booking->customer_id = $bulkBooking->customer_id;
                $booking->customer_name = $bulkBooking->customer_name;
                $booking->gender = $bulkBooking->gender ?? null;
                $booking->mobile_number = $bulkBooking->mobile_number;
                $booking->phone_number = $bulkBooking->phone_number;
                $booking->email = $bulkBooking->email;
                $booking->add_line_1 = $bulkBooking->add_line_1;
                $booking->add_line_2 = $bulkBooking->add_line_2;
                $booking->district = $bulkBooking->district ?? $bulkBooking->add_line_2;
                $booking->landmark = $bulkBooking->landmark;
                $booking->pincode_id = $bulkBooking->pincode_id;
                $booking->city = $bulkBooking->city;
                $booking->state = $bulkBooking->state;
                $booking->country_id = $bulkBooking->country_id;
                $booking->batch_id = $bulkBooking->batch_id;

                // Consignment details
                $booking->weight = $bulkBooking->weight;
                $booking->booked_amount = $bulkBooking->booked_amount;
                $booking->amount_due = $bulkBooking->amount_due;
                $booking->payment_mode = $bulkBooking->payment_mode;
                $booking->payment_id = $bulkBooking->payment_id;
                $booking->insured = $bulkBooking->insured;
                $booking->declared_consg_value = $bulkBooking->declared_consg_value;

                // Office details
                $booking->origin_office_type = $bulkBooking->origin_office_type;
                $booking->origin_office_id = $bulkBooking->origin_office_id;
                $booking->booking_user_id = $user->id;
                $booking->sms_to_sender = $bulkBooking->sms_to_sender;
                $booking->sms_to_receiver = $bulkBooking->sms_to_receiver;
                $booking->status = 'Booked & Dispatched';
                $booking->save();

                // Delivery details
                $delivery = new Delivery();
                $delivery->booking_id = $booking->id;
                $delivery->receiver_name = $bulkBooking->receiver_name;
                $delivery->add_line_1 = $bulkBooking->receiver_add_line_1;
                $delivery->add_line_2 = $bulkBooking->receiver_add_line_2;
                $delivery->city = $bulkBooking->receiver_city;
                $delivery->district = $bulkBooking->receiver_district;
                $delivery->state = $bulkBooking->receiver_state;
                $delivery->country_id = $bulkBooking->receiver_country_id;
                $delivery->pincode_id = $bulkBooking->receiver_pincode_id;
                $delivery->mobile_number = $bulkBooking->receiver_mobile_number;
                $delivery->phone_number = $bulkBooking->receiver_phone_number;
                $delivery->email = $bulkBooking->receiver_email;
                $delivery->save();

                $count++;
            }

            // Get customer info for success message
            $lastBooking = Booking::where('batch_id', $batchId)->latest('id')->first();
            $bookingIds = Booking::where('batch_id', $batchId)->pluck('id');

            $customer = Customer::select('id', 'code', 'customer_name')
                ->where('id', $lastBooking->customer_id)
                ->first();

            $deliveries = Delivery::select('id', 'booking_id', 'receiver_name', 'add_line_1', 'add_line_2', 'city', 'mobile_number', 'pincode_id')
                ->whereIn('booking_id', $bookingIds)
                ->with([
                    'booking:id,consg_number',
                    'pincode:id,pincode'
                ])
                ->get();

            // Clean up bulk booking data
            BulkBooking::destroy($bulkBookingIds);

            $encryptedBatchId = $batchId * env('ENC_KEY', 1);

            DB::commit();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            // Send notifications (can be moved to queue/background job for performance)
            if ($lastBooking->sms_to_sender == 1 && $lastBooking->mobile_number) {
                AppHelper::sendBulkTrackingMessage($lastBooking->customer_name, $lastBooking->mobile_number, $encryptedBatchId);
            }

            // Send email notifications
            $this->notificationService->sendBulkBookingEmail($encryptedBatchId, $customerName, $customerEmail);

            $success_message = '<b>'.$count.'</b> bookings have been successfully created for <b>'.$lastBooking->customer_name.'</b>.';

            return [
                'success' => true,
                'data' => [
                    'bulk-success' => $success_message,
                    'batch_id' => $batchId,
                    'customer' => $customer,
                    'deliveries' => $deliveries
                ]
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to process bulk booking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get consignment details for a batch
     *
     * @param int $batchId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getConsignmentDetails($batchId)
    {
        return Consignment::where('batch_id', $batchId)->get();
    }

    /**
     * Get bulk booking details
     *
     * @param int $id
     * @return \App\BulkBooking|null
     */
    public function getBookingDetails($id)
    {
        return BulkBooking::where('id', $id)
            ->select(['receiver_name', 'receiver_add_line_1', 'receiver_add_line_2', 'wrong_pincode', 'receiver_mobile_number', 'receiver_pincode_id'])
            ->with('pincode')
            ->first();
    }

    /**
     * Update booking sheet data
     *
     * @param Request $request
     * @return array
     */
    public function updateBookingSheet(Request $request)
    {
        try {
            $id = $request->input('bulk_booking_id');
            $booking = BulkBooking::find($id);

            if (!$booking) {
                return [
                    'message' => 'Booking not found',
                    'status_code' => 404
                ];
            }

            $booking->receiver_name = $request->input('receiver_name');
            $booking->receiver_add_line_1 = $request->input('receiver_address');
            $booking->receiver_add_line_2 = $request->input('receiver_area');
            $booking->receiver_pincode_id = $request->input('receiver_pincode_id');
            $booking->receiver_mobile_number = $request->input('receiver_mobile');
            $booking->has_error = 0;
            $booking->save();

            return [
                'message' => 'Booking updated successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Failed to update booking: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Delete booking row
     *
     * @param int $id
     * @return array
     */
    public function deleteBookingRow($id)
    {
        try {
            $booking = BulkBooking::find($id);

            if (!$booking) {
                return [
                    'message' => 'Booking not found',
                    'status_code' => 404
                ];
            }

            $booking->delete();

            return [
                'message' => 'Booking deleted successfully',
                'status_code' => 200
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Failed to delete booking: ' . $e->getMessage(),
                'status_code' => 500
            ];
        }
    }

    /**
     * Download booking sample file
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadSampleFile()
    {
        $file = public_path().'/files/sample_bulk_booking.xlsx';
        return Response::download($file, 'bulk_booking_sample.xlsx');
    }

    /**
     * Download manifest sample file
     *
     * @return \Illuminate\Http\Response
     */
    public function downloadManifestSample()
    {
        $file = public_path().'/files/sample_manifest.xlsx';
        return Response::download($file, 'manifest_sample.xlsx');
    }

    /**
     * Update booking statuses in bulk
     *
     * @param array $bookingIds
     * @param string $status
     * @param string|null $remarks
     * @return array
     */
    public function updateBookingStatuses($bookingIds, $status, $remarks = null)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

            // Get the bookings with permission check
            $query = Booking::whereIn('id', $bookingIds);

            if (!$user->isAdmin()) {
                $query->where('origin_office_type', $user->office_type)
                    ->where('origin_office_id', $user->office_id);
            }

            $bookings = $query->get();

            if ($bookings->count() != count($bookingIds)) {
                throw new \Exception('Some bookings were not found or you do not have permission to update them.');
            }

            // Update all bookings
            foreach ($bookings as $booking) {
                // Keep track of previous status for logging
                $previousStatus = $booking->status;

                // Update the booking
                $booking->status = $status;
                $booking->remarks = $remarks;
                $booking->save();

                // Log the status change in status history
                StatusHistory::create([
                    'booking_id' => $booking->id,
                    'previous_status' => $previousStatus,
                    'new_status' => $status,
                    'user_id' => $user->id,
                    'remarks' => $remarks
                ]);

                // Send status update notification
                $delivery = Delivery::where('booking_id', $booking->id)->first();
                if ($delivery) {
                    $this->notificationService->sendStatusUpdateNotifications($booking, $delivery);
                }
            }

            DB::commit();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            return [
                'status' => 'success',
                'message' => count($bookings) . ' bookings have been updated to "' . $status . '" status',
                'updated_count' => count($bookings)
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }
}
