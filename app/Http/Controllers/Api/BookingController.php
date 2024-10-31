<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use App\Subscription;
use App\Pincode;
use App\Customer;
use App\Pricing;
use App\Booking;
use App\Delivery;
use App\CustomerOffice;
use App\Consignment;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConsignmentBooked;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * getPlans.
     */
    public function getPlans()
    {
        //
        $plans = Subscription::get();
        if($plans){
            return response()->json( [
                'status' => 1,
                'message' => 'success',
                'data'   => $plans
            ] );
        }
        return response()->json( [
            'status' => 0,
            'message' => 'Plans Not Found',
        ],403);
    }

    /**
     * getPincodes.
     */
    public function getPincodes()
    {
        //
        $pincodes = Pincode::where('serviceable','=','1')->get();
        if($pincodes){
            return response()->json( [
                'status' => 1,
                'message' => 'success',
                'data'   => $pincodes
            ],200);
        }
        return response()->json( [
            'status' => 0,
            'message' => 'Pincode Not Found',
        ],403);


       
    }

    public function getCustomer(Request $request){

            $customerCode = $request->input('customerCode');
            $officeType = $request->input('officeType');
            $officeId = $request->input('officeId');
            $customerId[] = 0;

            $cust = CustomerOffice::where('office_type', '=', $officeType)
            ->where('office_id', '=', $officeId)->get();
            if($cust){
                foreach($cust as $customer){
                    $customerId[]=$customer->customer_id;
                }
            }
       
            $customer = Customer::when($customerCode, function($q) use ($customerCode, $customerId){
                    return $q->where('code', 'LIKE', '%'.$customerCode.'%')
                             ->whereIn('id' , $customerId);
            })->get();
            if(!count($customer)){
                return response()->json( [
                    'status' => 0,
                    'message' => 'Customer Not Found',
                ],403);
            }
           
            if($customer){
                $i = 0;
                foreach($customer as  $cust){
                    $pincode  = Pincode::where('id', '=', $cust->pincode_id)->first();
                    $customer[$i]['pincode'] = $pincode;
                 $i++;  
                }
                return response()->json( [
                    'status' => 1,
                    'message' => 'success',
                    'data'   => $customer
                    
                ],200);
            }
           

    }

    public function pricingDetails(Request $request) {
        $this->validate($request, [
            'weight' => 'nullable',
            'subId' => 'nullable',
            'consgType' => 'nullable'
        ]);

        $weight = $request->input('weight');
        $docType = $request->input('consgType');
        $subscription = Subscription::where('id', $request->input('subId'))->first();

        $pricing = Pricing::select(['from_weight_kgs', 'to_weight_kgs', 'price', 'addl_weight', 'addl_price'])->orWhere('consg_type', $docType)->orWhere(function ($q) use ($weight) {
            $q->where('from_weight_kgs', '<=', $weight);
            $q->where('to_weight_kgs', '>=', $weight);
        })->first();

        $extraWeight = $weight - $pricing->to_weight_kgs;
        if ($extraWeight < 0) {
            $totalPrice = $pricing->price + $subscription->price;
        } elseif ($pricing->addl_weight) {
            $extWeightMultiple = ceil($extraWeight / $pricing->addl_weight);
            $addPrice = $pricing->addl_price * $extWeightMultiple;
            $totalPrice = $pricing->price + $subscription->price + $addPrice;
        }

        return response()->json([
            'status' => 1,
            'pricing' => $pricing,
            'weights' => $extraWeight,
            'totalPrice' => $totalPrice ?? ''
        ], 200);
    }

    public function booking(Request $request){
          
        $customer_create = $request->input('customer_create');
        $customer_id = $request->input('customer_id');
        $sms_to_sender =  $request->input('sms_to_sender');
        $sms_to_receiver =  $request->input('sms_to_receiver');

        $consg_number = $request->input('consg_number');
        $sender_mobile_number = $request->input('sender_mobile_number');
        $sender_name = $request->input('sender_name');
       
        $receiver_name = $request->input('receiver_name');
        $receiver_mobile_number = $request->input('receiver_mobile_number');
        
        $consgNumberExist = Booking::where('consg_number', '=', $request->input('consg_number'))->first();
        if($consgNumberExist){
            return response()->json([
                'status' => 0,
                'message' => "Consignment Number Already Exist",
                
            ], 200);
        }
        $isConsignmentBelongsToBranch = $this->isConsingmentBelongsToBranch($consg_number,$request->input('origin_office_type'),$request->input('origin_office_id'));
      
        if($isConsignmentBelongsToBranch != 'true'){
            return response()->json([
                'status' => 0,
                'message' =>$isConsignmentBelongsToBranch,
                
            ], 200);
        }

       
        if ($customer_create == true) {
           /// echo 'comes';exit;
            $customer = new Customer();
            $customer->code = $request->input('customer_id');
            $customer->customer_name = $request->input('sender_name');
            $customer->add_line_1 = $request->input('sender_address');
            $customer->pincode_id = $request->input('sender_pincode_id');
            $customer->country_id = $request->input('country_id');
            $customer->mobile_number = $sender_mobile_number;
            $customer->save();
            $customer_id = $customer->id;
            CustomerOffice::create(
                [
                'customer_id' => $customer->id,
                'office_type' => $request->input('origin_office_type'),
                'office_id' => $request->input('origin_office_id')
                ]
            );


        }

       

        $booking = new Booking();
        //sender details
        $booking->consg_number = $consg_number;
        $booking->consg_type = $request->input('consg_type');
        $booking->subscription_id = $request->input('subscription_id');
        $booking->customer_id =  $customer_id;
        $booking->customer_name = $sender_name;
        $booking->mobile_number = $request->input('sender_mobile_number');
        $booking->pincode_id = $request->input('sender_pincode_id');

        $booking->weight = $request->input('captured_weight');
        $booking->booking_user_id = $request->input('booking_user_id');
        $booking->country_id = $request->input('country_id');
        $booking->booked_amount = $request->input('booked_amount');
        $booking->origin_office_type =  $request->input('origin_office_type');
        $booking->origin_office_id = $request->input('origin_office_id');
        $booking->status = "shipment-booked";
        $booking->vol_weight = $request->input('vol_weight');
        $booking->length = $request->input('length');
        $booking->breadth = $request->input('breadth');
        $booking->height = $request->input('height');
        $booking->email = $request->input('sender_email');
        $booking->save();

         //delivery details
         $delivery = new Delivery();
         $delivery->booking_id = $booking->id;
         $delivery->receiver_name = $receiver_name;
         $delivery->add_line_1 = $request->input('receiver_address');
         $delivery->pincode_id = $request->input('receiver_pincode_id');
         $delivery->mobile_number = $receiver_mobile_number;
         $delivery->country_id = $request->input('country_id'); 
         $delivery->email = $request->input('receiver_email'); 
         $delivery->save();

         if ($sms_to_sender == true) {
        //// echo 'comes';exit; 
            AppHelper::sendTrackingMessage($sender_name, $sender_mobile_number, $consg_number);
        }

        if ($sms_to_receiver == true ) { 
            AppHelper::sendTrackingMessage($receiver_name, $receiver_mobile_number, $consg_number);
        }
        if($booking->email){
            Mail::to($booking->email)->send(new ConsignmentBooked($booking, $delivery,'sender'));
        }
        if($delivery->email){
            Mail::to($delivery->email)->send(new ConsignmentBooked($booking, $delivery,'receiver'));
        }

        return response()->json([
            'status' => 1,
            'message' => "Success",
            
        ], 200);
       
    }

    public function isConsingmentBelongsToBranch($consg_number, $origin_office_type, $origin_office_id){

       
        $consignment = Consignment::where('consg_number',$consg_number)->first();
        if(!$consignment){
            return 'Invalid Consignment Number';
        }
        if($consignment){
            if($origin_office_type == $consignment->office_type && $origin_office_id == $consignment->office_id){
                return true;
            }else{
                 return 'Can not book this '.$consg_number . '. It was generated to different Office';
            }
        }

    }

}
