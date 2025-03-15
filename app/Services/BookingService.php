<?php

namespace App\Services;

use App\Booking;
use App\Consignment;
use App\Customer;
use App\CustomerOffice;
use App\Delivery;
use App\Http\Helpers\AppHelper;
use App\StatusHistory;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use PDF;

class BookingService
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
     * Get bookings with filters and permission checks
     *
     * @param Request $request
     * @return array
     */
    public function getBookings(Request $request)
    {
        // Process date inputs
        $startDate = null;
        $endDate = null;
        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()->format('Y-m-d H:i:s');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');
        }

        $consgNumber = $request->input('consg_number');
        $customerId = $request->input('customer_id');
        $subscriptionId = $request->input('subscription_id');
        $status = $request->input('status');
        $frCode = $request->input('fr_id');

        $user = Auth::user();

        // Generate a cache key based on all query parameters and user
        $cacheKey = "bookings_" . md5(json_encode([
                'user_id' => $user->id,
                'is_admin' => $user->isAdmin(),
                'office_type' => $user->office_type,
                'office_id' => $user->office_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'consg_number' => $consgNumber,
                'customer_id' => $customerId,
                'subscription_id' => $subscriptionId,
                'status' => $status,
                'fr_id' => $frCode,
                'page' => $request->input('page', 1)
            ]));

        // Add to the cache keys tracking list
        $cacheKeys = Cache::get('booking_cache_keys', []);
        $cacheKeys[] = $cacheKey;
        Cache::put('booking_cache_keys', array_unique($cacheKeys), 60 * 24); // 1 day

        // Try to get from cache first
        $bookings = Cache::remember($cacheKey, 60, function() use ($user, $consgNumber, $startDate, $endDate, $customerId, $subscriptionId, $status, $frCode) {
            $query = Booking::query();

            // Restrict non-admin users to only see bookings from their office
            if (!$user->isAdmin()) {
                $query->where('origin_office_type', $user->office_type)
                    ->where('origin_office_id', $user->office_id);
            }

            // Apply filters
            if ($consgNumber) {
                $query->where('consg_number', $consgNumber);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            if ($customerId) {
                $query->where('customer_id', $customerId);
            }

            if ($subscriptionId) {
                $query->where('subscription_id', $subscriptionId);
            }

            if ($status) {
                $query->where('status', $status);
            }

            if ($frCode) {
                $query->where('origin_office_type', 'FR')
                    ->where('origin_office_id', $frCode);
            }

            // Use select to only get needed columns, optimizing memory use
            return $query->select([
                'id', 'consg_number', 'customer_id', 'customer_name',
                'subscription_id', 'origin_office_type', 'origin_office_id',
                'status', 'created_at', 'updated_at', 'booked_amount'
            ])
                ->with([
                    'subscription:id,name',
                    'customer:id,code,customer_name'
                ])
                ->latest('id')
                ->paginate(self::PAGINATION_LIMIT);
        });

        // Get supporting data
        $subscriptions = $this->cacheService->getSubscriptionsList();

        $customer = null;
        if ($customerId) {
            $customer = Customer::select(['id', 'code'])->find($customerId);
        }

        $franchisee = null;
        if ($frCode) {
            $franchisee = $this->cacheService->getFranchisee($frCode);
        }

        $bookingStatuses = $this->cacheService->getBookingStatuses();

        return compact('bookings', 'customer', 'subscriptions', 'bookingStatuses', 'franchisee');
    }

    /**
     * Get data for create booking form
     *
     * @return array
     */
    public function getCreateFormData()
    {
        $subscriptions = $this->cacheService->getSubscriptionsWithPrice();

        $subscriptionLists = [];
        foreach ($subscriptions as $subscription) {
            $subscriptionLists[] = '<option value="'.$subscription->id.'">'.$subscription->name.'</option>';
        }

        $user = Auth::user();
        $user = $user->only(['id', 'username', 'first_name', 'last_name']);

        $countryList = $this->cacheService->getCountriesList();
        $pincodes = $this->cacheService->getPincodesList();
        $customers = $this->cacheService->getCustomersList();

        return compact('subscriptionLists', 'countryList', 'user', 'pincodes', 'customers', 'subscriptions');
    }

    /**
     * Validate booking data
     *
     * @param Request $request
     * @return void
     */
    public function validateBookingData(Request $request)
    {
        $rules = [
            'consg_number' => 'required|unique:bookings,consg_number,NULL,id,deleted_at,NULL',
            'consg_type' => 'required|in:dox,non-dox',
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
            'receiver_name' => 'required|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_area' => 'nullable|string',
            'receiver_pincode_id' => 'required|exists:pincodes,id',
            'receiver_city' => 'nullable|string',
            'receiver_district' => 'nullable|string',
            'receiver_state' => 'nullable|string',
            'receiver_country' => 'nullable|exists:countries,id',
            'receiver_sms' => 'nullable|boolean',
            'receiver_mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'receiver_email' => 'nullable|email',
            'length' => 'required_if:consg_type,non-dox|numeric',
            'breadth' => 'required_if:consg_type,non-dox|numeric',
            'height' => 'required_if:consg_type,non-dox|numeric',
            'captured_weight' => 'required_if:consg_type,dox|numeric',
            'vol_weight' => 'required_if:consg_type,non-dox|numeric',
            'booking_user_id' => 'required|exists:users,id',
            'booked_amount' => 'required|numeric',
            'risk_covered' => 'nullable|boolean',
            'declared_consg_value' => 'required_if:risk_covered,1|numeric',
            'remarks' => 'nullable|string',
        ];

        Validator::make($request->all(), $rules)->validate();

        // Validate consignment number belongs to current office
        Validator::make($request->all(), [
            'consg_number' => [function($attribute, $val, $fail) use ($request) {
                $user = auth()->user();
                $consignment = Consignment::where('consg_number', $request->input('consg_number'))->first();

                if (!$consignment) {
                    return $fail('Invalid Consignment Number');
                }

                if ($user->office_type != $consignment->office_type || $user->office_id != $consignment->office_id) {
                    return $fail('Cannot book this consignment. It was generated for a different office.');
                }
            }]
        ])->validate();
    }

    /**
     * Create a new booking
     *
     * @param Request $request
     * @return array
     */
    public function createBooking(Request $request)
    {
        try {
            DB::beginTransaction();

            $user = Auth::user();

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
                    'office_type' => $user->office_type,
                    'office_id' => $user->office_id
                ]);
            }

            // Create booking
            $booking = new Booking();

            // Sender details
            $booking->consg_number = $request->input('consg_number');
            $booking->consg_type = $request->input('consg_type');
            $booking->subscription_id = $request->input('subscription_id');
            $booking->customer_id = $customer->id;
            $booking->customer_name = $request->input('sender_name');
            $booking->mobile_number = $request->input('sender_mobile_number');
            $booking->phone_number = $request->input('sender_phone_number');
            $booking->email = $request->input('sender_email');
            $booking->add_line_1 = $request->input('sender_address');
            $booking->add_line_2 = $request->input('sender_area');
            $booking->district = $request->input('sender_district');
            $booking->landmark = $request->input('sender_landmark');
            $booking->pincode_id = $request->input('sender_pincode_id');
            $booking->city = $request->input('sender_city');
            $booking->state = $request->input('sender_state');
            $booking->country_id = $request->input('sender_country');

            // Consignment details
            $booking->weight = $request->input('captured_weight');
            $booking->vol_weight = $request->input('vol_weight');
            $booking->length = $request->input('length');
            $booking->breadth = $request->input('breadth');
            $booking->height = $request->input('height');
            $booking->booked_amount = $request->input('booked_amount');
            $booking->amount_due = $request->input('amount_due');
            $booking->payment_mode = $request->input('payment_mode');
            $booking->payment_id = $request->input('payment_id');
            $booking->insured = $request->input('insured');
            $booking->declared_consg_value = $request->input('declared_consg_value');

            // Office details
            $booking->origin_office_type = $user->office_type;
            $booking->origin_office_id = $user->office_id;
            $booking->dest_branch_id = $request->input('dest_branch_id');
            $booking->booking_user_id = $user->id;
            $booking->sms_to_sender = $request->input('sender_sms');
            $booking->sms_to_receiver = $request->input('receiver_sms');
            $booking->status = 'Booked & Dispatched';
            $booking->remarks = $request->input('remarks');
            $booking->save();

            // Delivery details
            $delivery = new Delivery();
            $delivery->booking_id = $booking->id;
            $delivery->receiver_name = $request->input('receiver_name');
            $delivery->add_line_1 = $request->input('receiver_address');
            $delivery->add_line_2 = $request->input('receiver_area');
            $delivery->city = $request->input('receiver_city');
            $delivery->district = $request->input('receiver_district');
            $delivery->state = $request->input('receiver_state');
            $delivery->country_id = $request->input('receiver_country');
            $delivery->pincode_id = $request->input('receiver_pincode_id');
            $delivery->mobile_number = $request->input('receiver_mobile_number');
            $delivery->phone_number = $request->input('receiver_phone_number');
            $delivery->email = $request->input('receiver_email');
            $delivery->office_type = $request->input('office_type');
            $delivery->office_id = $request->input('office_id');
            $delivery->delivery_datetime = $request->input('delivery_datetime');
            $delivery->delivery_status = $request->input('delivery_status');
            $delivery->delivery_user_id = $request->input('delivery_user_id');
            $delivery->no_of_attempts = $request->input('no_of_attempts');
            $delivery->no_of_pieces = $request->input('no_of_pieces');
            $delivery->penalty = $request->input('penalty');
            $delivery->actual_delivery_charge = $request->input('actual_delivery_charge');
            $delivery->remarks = $request->input('remarks');
            $delivery->save();

            DB::commit();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            // Send notifications
            $this->sendBookingNotifications($booking, $delivery);

            return [
                'success' => true,
                'message' => 'Booking created successfully!',
                'booking_id' => $booking->id
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to create booking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get data for edit booking form
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function getEditFormData(Request $request, $id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only edit bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->find($id);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not have permission to edit it.'
            ];
        }

        $subscriptions = Subscription::select(['id', 'name', 'price'])
            ->where('consg_type', $booking->consg_type)
            ->get();

        $subscriptionLists = [];
        foreach ($subscriptions as $subscription) {
            $selected = $booking->subscription_id == $subscription->id ? 'selected' : '';
            $subscriptionLists[] = '<option value="'.$subscription->id.'"'.$selected.'>'.$subscription->name.'</option>';
        }

        $bookingUser = User::select(['id', 'username', 'first_name', 'last_name'])
            ->where('id', $booking->booking_user_id)
            ->first();

        if (!$bookingUser) {
            $bookingUser = [
                'id' => '',
                'username' => 'Unknown',
                'first_name' => '',
                'last_name' => ''
            ];
        }

        $delivery = Delivery::where('booking_id', $booking->id)->first();

        $senderCountryList = $this->cacheService->getCountriesList($booking->country_id);
        $receiverCountryList = [];

        if ($delivery) {
            $receiverCountryList = $this->cacheService->getCountriesList($delivery->country_id);
        }

        // Check delete permission
        $loggedInUser = Auth::user();
        $segment = $request->segment(2);
        $module = \App\Module::where('name', $segment)->first();
        $deletePermission = null;

        if ($module) {
            foreach ($loggedInUser->roles as $role) {
                if ($role->hasDeletePermission($module->id)) {
                    $deletePermission = 1;
                }
            }
        }

        return [
            'success' => true,
            'data' => compact(
                'delivery', 'booking', 'subscriptionLists',
                'senderCountryList', 'receiverCountryList', 'bookingUser',
                'deletePermission'
            )
        ];
    }

    /**
     * Update an existing booking
     *
     * @param Request $request
     * @param int $id
     * @return array
     */
    public function updateBooking(Request $request, $id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only update bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->find($id);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not have permission to update it.'
            ];
        }

        $rules = [
            'consg_type' => 'required|in:dox,non-dox',
            'sender_name' => 'required|string|max:255',
            'sender_address' => 'nullable|string',
            'sender_area' => 'nullable|string',
            'sender_pincode_id' => 'required|exists:pincodes,id',
            'sender_city' => 'nullable|string',
            'sender_district' => 'nullable|string',
            'sender_state' => 'nullable|string',
            'sender_country' => 'nullable|exists:countries,id',
            'sender_sms' => 'nullable|boolean',
            'sender_mobile_number' => 'nullable|required_if:sender_sms,1|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_email' => 'nullable|email',
            'receiver_name' => 'required|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_area' => 'nullable|string',
            'receiver_pincode_id' => 'required|exists:pincodes,id',
            'receiver_city' => 'nullable|string',
            'receiver_district' => 'nullable|string',
            'receiver_state' => 'nullable|string',
            'receiver_country' => 'nullable|exists:countries,id',
            'receiver_sms' => 'nullable|boolean',
            'receiver_mobile_number' => 'nullable|required_if:receiver_sms,1|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_email' => 'nullable|email',
            'length' => 'required_if:consg_type,non-dox|numeric',
            'breadth' => 'required_if:consg_type,non-dox|numeric',
            'height' => 'required_if:consg_type,non-dox|numeric',
            'captured_weight' => 'required_if:consg_type,dox|numeric',
            'vol_weight' => 'required_if:consg_type,non-dox|numeric',
            'booking_user_id' => 'required|exists:users,id',
            'booked_amount' => 'nullable|numeric',
            'risk_covered' => 'nullable|boolean',
            'declared_consg_value' => 'required_if:risk_covered,1|numeric',
            'remarks' => 'nullable|string'
        ];

        // Check if consignment number is being changed
        if ($booking->consg_number != $request->input('consg_number')) {
            $rules['consg_number'] = 'required|unique:bookings,consg_number,NULL,id,deleted_at,NULL';

            // Additional validation for consignment number
            Validator::make($request->all(), [
                'consg_number' => [function($attribute, $val, $fail) use ($request) {
                    $consignment = Consignment::where('consg_number', $request->input('consg_number'))->first();

                    if (!$consignment) {
                        return $fail('Invalid Consignment Number');
                    }
                }]
            ])->validate();
        }

        Validator::make($request->all(), $rules)->validate();

        try {
            DB::beginTransaction();

            // Update booking
            $booking->consg_number = $request->input('consg_number');
            $booking->consg_type = $request->input('consg_type');
            $booking->subscription_id = $request->input('subscription_id');
            $booking->customer_id = $request->input('customer_id');
            $booking->customer_name = $request->input('sender_name');
            $booking->mobile_number = $request->input('sender_mobile_number');
            $booking->phone_number = $request->input('sender_phone_number');
            $booking->email = $request->input('sender_email');
            $booking->add_line_1 = $request->input('sender_address');
            $booking->add_line_2 = $request->input('sender_area');
            $booking->landmark = $request->input('sender_landmark');
            $booking->pincode_id = $request->input('sender_pincode_id');
            $booking->city = $request->input('sender_city');
            $booking->state = $request->input('sender_state');
            $booking->country_id = $request->input('sender_country');

            // Consignment details
            $booking->vol_weight = $request->input('vol_weight');
            $booking->final_length = $request->input('final_length');
            $booking->final_breadth = $request->input('final_breadth');
            $booking->final_height = $request->input('final_height');
            $booking->final_amount = $request->input('final_amount');
            $booking->final_weight = $request->input('final_weight');
            $booking->amount_due = $request->input('amount_due');
            $booking->payment_mode = $request->input('payment_mode');
            $booking->payment_id = $request->input('payment_id');
            $booking->insured = $request->input('insured');
            $booking->declared_consg_value = $request->input('declared_consg_value');

            // Other details
            $booking->dest_branch_id = $request->input('dest_branch_id');
            $booking->sms_to_sender = $request->input('sender_sms');
            $booking->sms_to_receiver = $request->input('receiver_sms');
            $booking->remarks = $request->input('remarks');
            $booking->save();

            // Update delivery
            $delivery = Delivery::where('booking_id', $booking->id)->first();
            if ($delivery) {
                $delivery->receiver_name = $request->input('receiver_name');
                $delivery->add_line_1 = $request->input('receiver_address');
                $delivery->add_line_2 = $request->input('receiver_area');
                $delivery->city = $request->input('receiver_city');
                $delivery->state = $request->input('receiver_state');
                $delivery->country_id = $request->input('receiver_country');
                $delivery->pincode_id = $request->input('receiver_pincode_id');
                $delivery->mobile_number = $request->input('receiver_mobile_number');
                $delivery->phone_number = $request->input('receiver_phone_number');
                $delivery->email = $request->input('receiver_email');
                $delivery->office_type = $request->input('office_type');
                $delivery->office_id = $request->input('office_id');
                $delivery->delivery_datetime = $request->input('delivery_datetime');
                $delivery->delivery_status = $request->input('delivery_status');
                $delivery->delivery_user_id = $request->input('delivery_user_id');
                $delivery->no_of_attempts = $request->input('no_of_attempts');
                $delivery->no_of_pieces = $request->input('no_of_pieces');
                $delivery->penalty = $request->input('penalty');
                $delivery->actual_delivery_charge = $request->input('actual_delivery_charge');
                $delivery->remarks = $request->input('remarks');
                $delivery->save();
            }

            DB::commit();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            // Send notifications if needed
            $this->sendUpdateNotifications($booking, $delivery);

            return [
                'success' => true,
                'message' => 'Booking updated successfully!'
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'success' => false,
                'message' => 'Failed to update booking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Delete a booking
     *
     * @param int $id
     * @return array
     */
    public function deleteBooking($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only delete bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->find($id);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not have permission to delete it.'
            ];
        }

        try {
            $booking->delete();

            // Clear related cache entries
            $this->cacheService->clearBookingCache();

            return [
                'success' => true,
                'message' => 'Booking deleted successfully!'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to delete booking: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get booking details
     *
     * @param int $id
     * @return array
     */
    public function getBookingDetails($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only view bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->with([
            'delivery',
            'office',
            'delivery.receiverImageUrl',
            'delivery.receiverSignUrl',
            'delivery.user',
            'delivery.deliveryBranch'
        ])
            ->find($id);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not have permission to view it.'
            ];
        }

        return [
            'success' => true,
            'booking' => $booking
        ];
    }

    /**
     * Export bookings to Excel
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportBookings(Request $request)
    {
        $consgNumber = $request->input('consg_number');
        $customerId = $request->input('customer_id');
        $startDate = null;
        $endDate = null;
        $status = $request->input('status');

        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }

        return Excel::download(
            new BookingsExport($consgNumber, $customerId, $startDate, $endDate, $status),
            'bookings_' . Carbon::now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Calculate volumetric weight
     *
     * @param float $length
     * @param float $width
     * @param float $height
     * @return float
     */
    public function calculateVolumetricWeight($length, $width, $height)
    {
        return ($length * $width * $height) / 5000;
    }

    /**
     * Get booking history with status timeline
     *
     * @param int $id
     * @return array
     */
    public function getBookingHistory($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only access bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->with(['statusHistory' => function($query) {
            $query->orderBy('created_at', 'asc');
        }])->find($id);

        if (!$booking) {
            return [
                'success' => false,
                'message' => 'Booking not found or you do not have permission to access it.'
            ];
        }

        return [
            'success' => true,
            'data' => [
                'booking' => [
                    'id' => $booking->id,
                    'consg_number' => $booking->consg_number,
                    'customer_name' => $booking->customer_name,
                    'created_at' => $booking->created_at->format('Y-m-d H:i:s'),
                    'current_status' => $booking->status
                ],
                'history' => $booking->statusHistory
            ]
        ];
    }

    /**
     * Get dashboard statistics
     *
     * @param Request $request
     * @return array
     */
    public function getDashboardStats(Request $request)
    {
        $user = Auth::user();

        // Default to last 30 days if not specified
        $startDate = $request->input('start_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        // Build base query with permission check
        $query = Booking::query();

        if (!$user->isAdmin()) {
            $query->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);

        // Cache the dashboard statistics for 15 minutes
        $cacheKey = 'dashboard_stats_' . md5($user->id . $startDate . $endDate);

        return Cache::remember($cacheKey, 15, function() use ($query, $startDate, $endDate) {
            // Total bookings
            $totalBookings = $query->count();

            // Total revenue
            $totalRevenue = $query->sum('booked_amount');

            // Status breakdown
            $statusBreakdown = $query->select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray();

            // Daily booking trend
            $dailyTrend = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as bookings'),
                DB::raw('sum(booked_amount) as revenue')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->mapWithKeys(function ($item) {
                    return [
                        $item->date => [
                            'bookings' => $item->bookings,
                            'revenue' => $item->revenue
                        ]
                    ];
                })
                ->toArray();

            // Top customers
            $topCustomers = $query->select('customer_id', 'customer_name', DB::raw('count(*) as bookings'), DB::raw('sum(booked_amount) as revenue'))
                ->groupBy('customer_id', 'customer_name')
                ->orderByDesc('bookings')
                ->limit(5)
                ->get()
                ->toArray();

            return [
                'total_bookings' => $totalBookings,
                'total_revenue' => $totalRevenue,
                'status_breakdown' => $statusBreakdown,
                'daily_trend' => $dailyTrend,
                'top_customers' => $topCustomers,
                'date_range' => [
                    'start' => $startDate->format('Y-m-d'),
                    'end' => $endDate->format('Y-m-d')
                ]
            ];
        });
    }

    /**
     * Generate booking acknowledgement PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateAcknowledgementPdf($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only access bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->with([
            'delivery',
            'subscription',
            'customer',
            'senderPincode',
            'delivery.receiverPincode'
        ])->find($id);

        if (!$booking) {
            return redirect(route('bookings.index'))->withError('Booking not found or you do not have permission to access it.');
        }

        // Generate PDF
        $pdf = PDF::loadView('bookings.acknowledgement', compact('booking'));
        return $pdf->download('booking_' . $booking->consg_number . '.pdf');
    }

    /**
     * Send notifications for new bookings
     *
     * @param Booking $booking
     * @param Delivery $delivery
     * @return void
     */
    protected function sendBookingNotifications($booking, $delivery)
    {
        try {
            // Send SMS notifications
            if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
                AppHelper::sendTrackingMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
            }

            if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
                AppHelper::sendTrackingMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
            }

            if ($booking->mobile_number) {
                AppHelper::sendShipperCopy($booking->customer_name, $booking->mobile_number, $booking->consg_number);
            }

            // Email notifications can be sent via queue for better performance
            // This is now handled by the notification service
            $this->notificationService->sendBookingEmails($booking, $delivery);
        } catch (\Exception $e) {
            // Log notification errors but don't disrupt the booking process
            \Log::error('Failed to send booking notifications: ' . $e->getMessage());
        }
    }

    /**
     * Send notifications for updated bookings
     *
     * @param Booking $booking
     * @param Delivery $delivery
     * @return void
     */
    protected function sendUpdateNotifications($booking, $delivery)
    {
        try {
            // Send SMS notifications
            if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
                AppHelper::sendTrackingMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
            }

            if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
                AppHelper::sendTrackingMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
            }

            if ($booking->mobile_number) {
                AppHelper::sendShipperCopy($booking->customer_name, $booking->mobile_number, $booking->consg_number);
            }

            // Email notifications for updates
            $this->notificationService->sendStatusUpdateEmails($booking, $delivery);
        } catch (\Exception $e) {
            // Log notification errors but don't disrupt the booking process
            \Log::error('Failed to send update notifications: ' . $e->getMessage());
        }
    }
}
