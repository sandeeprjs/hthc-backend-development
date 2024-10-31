<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Booking;
use App\Delivery;
use App\Manifest;
use App\Reason;
use App\ConsignmentReturn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\AppHelper;
use App\Mail\ConsignmentDelivered;
use Illuminate\Support\Facades\Mail;

class DeliveryController extends Controller
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
        $consg_number = $request->input('consg_number');
        $manifest_id = $request->input('manifest_id');
        $status = $request->input('status');
        $delivery_user_id = $request->input('delivery_user_id');
        $tookstatus = $request->input('tookstatus');
        $receiver_name = $request->input('rec_name');
        $this->validate( $request, [
            'consg_number' => 'required',
            'status' => 'required',
            'manifest_id' => 'required'
        ] );

        if($status == 'Delivered'){
            if($request->file('receiver_photo') == null && $request->file('receiver_sign') == null && $request->file('receiver_voice') == null){
                return response()->json( [
                    'status' => 0,
                    'message' => 'Customer Photo or Sign or voice is required',
                ],403);
            }
        }
        
        $isDelivered =  $this->isDelivered($consg_number);
        if($isDelivered){
            return response()->json( [
                'status' => 0,
                'message' => 'This Consignment Already Delivered',
            ],403);
        }
        
        $booking  = Booking::where('consg_number', '=', $consg_number)->first();
        if(!$booking){
            return response()->json( [
                'status' => 0,
                'message' => 'No data found for this consignment number',
            ],403);

        }
        Booking::find($booking->id)->update(array('status' => $status));
        $delivery =  Delivery::where('booking_id', '=', $booking->id)->first();
        $delivery->delivery_status = $status;
        $delivery->delivery_user_id = $delivery_user_id;
        $delivery->delivery_datetime = $booking->updated_at;
        $delivery->tookstatus = $tookstatus;
        $delivery->rec_name = $receiver_name;
        if($status == 'Cancelled' || $status == 'Returned'){
            $delivery->no_of_attempts = $delivery->no_of_attempts+1;
        }
        $delivery->save(); 
        if ($delivery->mobile_number || $booking->mobile_number) {
            if($booking->sms_to_sender == 1 && $booking->mobile_number ){
                  AppHelper::sendDeliveryMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
            }
            if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
                  AppHelper::sendDeliveryMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
            }
           
            
        }
      
     
        //Manifest::find($manifest_id)->update(array('status' => $status));
        $file_photo =null;
        $file_sign =null;
        $file_voice =null;
        
        if($request->file('receiver_photo')){
            $name = $delivery->id.'_'.$request->file('receiver_photo')->getClientOriginalName();
            $ext  = $request->file('receiver_photo')->extension();
            $url  = 'delivery/photo/'.$name;
            $request->receiver_photo->move(base_path('public/delivery/photo/'), $name);
           
            $file_photo = $delivery->files()->create( [
                'name'    => $name,
                'url'     => $url,
                'ext'     => $ext,
                'type'     => 'receiver_photo',
                'alt' => $request->input( 'alt_text' )
                
            ]);//->fillable(['type' => 'receiver_photo']);
        }
        if($request->file('receiver_sign')){
            $name = $delivery->id.'_'.$request->file('receiver_sign')->getClientOriginalName();
            $ext  = $request->file('receiver_sign')->extension();
            $url  = 'delivery/sign/'.$name;
            $request->receiver_sign->move(base_path('public/delivery/sign/'), $name);

            $file_sign = $delivery->files()->create( [
                'name'    => $name,
                'url'     => $url,
                'ext'     => $ext,
                'type'     => 'receiver_sign',
                'alt' => $request->input( 'alt_text' )
               
            ] );
        }
        if($request->file('receiver_voice')){
            $name = $delivery->id.'_'.$request->file('receiver_voice')->getClientOriginalName();
            $ext  = $request->file('receiver_voice')->extension();
            $url  = 'delivery/voice/'.$name;
            $request->receiver_voice->move(base_path('public/delivery/voice/'), $name);

            $file_voice = $delivery->files()->create( [
                'name'    => $name,
                'url'     => $url,
                'ext'     => $ext,
                'type'     => 'receiver_voice',
                'alt' => $request->input( 'alt_text' )
               
            ] );
        }

        if($booking->email){
            Mail::to($booking->email)->send(new ConsignmentDelivered($booking, $delivery,'sender'));
        }
        if($delivery->email){
            Mail::to($delivery->email)->send(new ConsignmentDelivered($booking, $delivery,'receiver'));
        }

        return response()->json( [
                'status' => 1,
                'message' => 'success',
                'receiver_photo'   => $file_photo,
                'receiver_sign'    => $file_sign,
                'receiver_voice'   => $file_voice
		] );
        
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
     * This is to check delivered or not
     */
    public function isDelivered($consg_number){
         $delivered = Booking::where('consg_number', '=', $consg_number)
                       ->where('status', '=', 'Delivered')->first();
        if($delivered){
            return true;
        }

        return false;
    }

    /**
     * Return Consignments
     */
    public function consignmentReturn(Request $request){

        $consg_number = $request->input('consg_number');
        $reasonId = $request->input('reason_id');
        $returnMode = $request->input('return_mode');
        $userId = $request->input('user_id');
        
        $booking  = Booking::where('consg_number', '=', $consg_number)->first();
        if(!$booking){
            return response()->json( [
                'status' => 0,
                'message' => 'No data found for this consignment number',
            ],403);

        }
        if($booking){
            if($booking->status == 'Returned'){
                return response()->json( [
                    'status' => 0,
                    'message' => 'Already Returned this consignment number',
                ],403);
            }
        }
        $status = "Returned";
        if($returnMode == 'Cancelled'){
            $status = "Cancelled";
        }
        
        $return = ConsignmentReturn::create([
            'consg_number' => $consg_number,
            'reason_id' => $reasonId,
            'return_mode' => $returnMode,
            'user_id' => $userId
        ]);

        $manifest = Manifest::where('last_mile_delivery', '=', 1)
                    ->where('manifest_type', '=', 'D')
                    ->where('status', '=', 'Out for Delivery')
                    ->where('manifest_number', '=', $consg_number)->first();

        if($return){
            Booking::find($booking->id)->update(array('status' => $status));
            $delivery = Delivery::where('booking_id','=',$booking->id)->first();
            if($delivery->no_of_attempts == 'NULL'){
                $no_of_attempts = 1;
            }else{
                $no_of_attempts = $delivery->no_of_attempts+1;
            }
           
            Delivery::where('booking_id','=',$booking->id)->update(array('no_of_attempts' => $no_of_attempts,'delivery_user_id' => $userId));
            // $delivery->increment('no_of_attempts');


            if($manifest){
                $customer_view =1;
                    $outForDelivery = Manifest::create([
                        'manifest_type' => 'R',
                        'manifest_number' => $manifest->manifest_number,
                        'origin_branch_id' => $manifest->origin_branch_id,
                        // 'origin_pincode_id' => $data['origin_pincode_id'][$key],
                        'dest_branch_id' => $manifest->dest_branch_id,
                        // 'dest_pincode_id' => $data['dest_pincode_id'][$key],
                        'sender_id' => $manifest->sender_id,
                        'receiver_id' => $manifest->receiver_id,
                        'sender_type' => $manifest->sender_type,
                        'receiver_type' => $manifest->receiver_type,
                        'consg_number_id' => 0,
                        'last_mile_delivery' => $manifest->last_mile_delivery,
                        'delivery_user_id' => $userId,
                        'customer_view' => $customer_view,
                        'status' => 'Return to Destination Hub',
                        'user_id'=>$userId,
                        'office_type'=>$manifest->receiver_type,
                        'office_id'=>$manifest->office_id,
                    // 'remarks' => $data['remarks']
                    'remarks' =>''
                    ]);
            }

            return response()->json( [
                'status' => 1,
                'message' => 'success',
            ] );
        }
    }
    
    public function getReasons(Request $request){
           
        $type = $request->input('status');
        $reasonType = "return";
        if($type == "Returned"){
            $reasonType = "return";
        }
        if($type == "Cancelled"){
            $reasonType = "cancel";
        }
        $reasons = Reason::where('type' , '=', $reasonType)->get();
        if($reasons){
            return response()->json( [
                'status' => 1,
                'message' => 'success',
                'data'   => $reasons
            ] );
        }
        return response()->json( [
            'status' => 0,
            'message' => 'Reason Not Found',
        ],403);
           
    }

    public function getBranchFranchisee(){

        $franchisees = DB::table("franchisees")->select("franchisees.id","franchisees.code");
        $branches = DB::table("branches")->select("branches.id" ,"branches.code")
            ->union($franchisees)
            ->whereNull('deleted_at')
            ->get();

        if($branches){
            return response()->json( [
                    'status' => 1,
                    'message' => 'success',
                    'data'   => $branches
                ] );
        }
        return response()->json( [
            'status' => 0,
            'message' => 'Branch Not Found',
        ],403);        

    }
}
