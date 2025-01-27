<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use App\Models\{Subscription, Pincode, Customer, Pricing, Booking, Delivery, CustomerOffice, Consignment, Branch};
use Illuminate\Support\Facades\{Mail, Log, DB};
use App\Mail\ConsignmentBooked;
use Exception;

class BookingController extends Controller
{
    public function booking(Request $request)
    {
        try {
            DB::beginTransaction();

            // Validate consignment number
            if ($this->isConsignmentExists($request->consg_number)) {
                return $this->errorResponse("Consignment Number Already Exists");
            }

            // Validate branch
            $isValidBranch = $this->validateBranch(
                $request->consg_number,
                $request->origin_office_type,
                $request->origin_office_id
            );
            if ($isValidBranch !== true) {
                return $this->errorResponse($isValidBranch);
            }

            // Create customer if needed
            $customerId = $request->customer_id;
            if ($request->customer_create) {
                $customer = $this->createCustomer($request);
                $customerId = $customer->id;
            }

            // Get destination branch based on delivery pincode
            $destBranch = Branch::where('pincode_id', $request->receiver_pincode_id)->first();
            if (!$destBranch) {
                throw new Exception('No destination branch found for given pincode');
            }

            // Create booking
            $booking = new Booking();
            $booking->fill([
                'consg_number' => $request->consg_number,
                'consg_type' => $request->consg_type,
                'subscription_id' => $request->subscription_id,
                'customer_id' => $customerId,
                'customer_name' => $request->sender_name,
                'mobile_number' => $request->sender_mobile_number,
                'pincode_id' => $request->sender_pincode_id,
                'weight' => $request->captured_weight,
                'booking_user_id' => $request->booking_user_id,
                'country_id' => $request->country_id,
                'booked_amount' => $request->booked_amount,
                'origin_office_type' => $request->origin_office_type,
                'origin_office_id' => $request->origin_office_id,
                'dest_branch_id' => $destBranch->id,
                'status' => "Booked & Dispatched",
                'vol_weight' => $request->vol_weight,
                'length' => $request->length,
                'breadth' => $request->breadth,
                'height' => $request->height,
                'email' => $request->sender_email
            ]);
            $booking->save();

            // Create delivery
            $delivery = new Delivery();
            $delivery->fill([
                'booking_id' => $booking->id,
                'receiver_name' => $request->receiver_name,
                'add_line_1' => $request->receiver_address,
                'pincode_id' => $request->receiver_pincode_id,
                'mobile_number' => $request->receiver_mobile_number,
                'country_id' => $request->country_id,
                'email' => $request->receiver_email
            ]);
            $delivery->save();

            // Send notifications
            $this->sendNotifications($booking, $delivery, $request);

            DB::commit();
            return $this->successResponse("Booking created successfully");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Booking creation failed: ' . $e->getMessage());
            return $this->errorResponse($e->getMessage());
        }
    }

    private function createCustomer($request)
    {
        $customer = Customer::create([
            'code' => $request->customer_id,
            'customer_name' => $request->sender_name,
            'add_line_1' => $request->sender_address,
            'pincode_id' => $request->sender_pincode_id,
            'country_id' => $request->country_id,
            'mobile_number' => $request->sender_mobile_number
        ]);

        CustomerOffice::create([
            'customer_id' => $customer->id,
            'office_type' => $request->origin_office_type,
            'office_id' => $request->origin_office_id
        ]);

        return $customer;
    }

    private function sendNotifications($booking, $delivery, $request)
    {
        if ($request->sms_to_sender) {
            AppHelper::sendTrackingMessage(
                $request->sender_name,
                $request->sender_mobile_number,
                $request->consg_number
            );
        }

        if ($request->sms_to_receiver) {
            AppHelper::sendTrackingMessage(
                $request->receiver_name,
                $request->receiver_mobile_number,
                $request->consg_number
            );
        }

        if ($booking->email) {
            Mail::to($booking->email)->send(
                new ConsignmentBooked($booking, $delivery, 'sender')
            );
        }

        if ($delivery->email) {
            Mail::to($delivery->email)->send(
                new ConsignmentBooked($booking, $delivery, 'receiver')
            );
        }
    }

    private function isConsignmentExists($consgNumber)
    {
        return Booking::where('consg_number', $consgNumber)->exists();
    }

    private function validateBranch($consgNumber, $officeType, $officeId)
    {
        $consignment = Consignment::where('consg_number', $consgNumber)->first();

        if (!$consignment) {
            return 'Invalid Consignment Number';
        }

        if ($officeType != $consignment->office_type ||
            $officeId != $consignment->office_id) {
            return "Cannot book consignment {$consgNumber}. Generated for different office.";
        }

        return true;
    }

    private function successResponse($message, $data = null)
    {
        return response()->json([
            'status' => 1,
            'message' => $message,
            'data' => $data
        ], 200);
    }

    private function errorResponse($message)
    {
        return response()->json([
            'status' => 0,
            'message' => $message
        ], 200);
    }
}
