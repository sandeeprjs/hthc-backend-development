<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Manifest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use App\Booking;
use App\Delivery;
use App\Branch;
use App\Franchisee;
use App\User;


class ReturnsController extends Controller
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
     * Return Incoming
     */
    public function incoming(){

        $user = auth()->user();
        $manifests = Manifest::where('receiver_id','=', $user->office_id)
                   ->where('receiver_type', '=', $user->office_type)
                   ->where('manifest_type', '=', 'I')->latest()->paginate('10');
      
        return view('returns.incoming',compact('manifests'));
    }

    /**
     * Create Return Incoming 
     */
    public function incomingCreate(){
        $user = auth()->user();
        $loggedOffice = $this->loggedInOffice();
        $branchFranchisees = $this->branchFranchisees();
        return view('returns.incomingCreate',compact('user','loggedOffice', 'branchFranchisees'));
    }

    /**
     * Create Return Outgoing
     */
    public function outgoingCreate(){
        $user = auth()->user();
        $loggedOffice = $this->loggedInOffice();
        //echo '<pre>';
      ///  print_r($loggedOffice->id);
        $branchFranchisees = $this->branchFranchisees();
        $employees  =  $this->getEmployees($user->office_type, $user->office_id);
        return view('returns.outgoingCreate',compact('user','loggedOffice', 'branchFranchisees','employees'));
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
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

        $data = $request->all();
        $manifest_numbers = $data['manifest_number'];
      
        // echo '<pre>';
        // print_r($data);
        // exit;
        $manifest_type = $data['manifest_type'];
        if($manifest_numbers){ 
            
        foreach($manifest_numbers as $key => $manifest_number){

        $delivery_user_id = null;
        $last_mile_delivery = 0;
        $customer_view = 0;
       

        $status = $this->getManifestStatus($manifest_number,$data['manifest_type']);
        // if($request->input('last_mile_delivery')){
        //    $last_mile_delivery =  $request->input('last_mile_delivery');
        //    $data['receiver_id'] =  $request->input('sender_id');
        //    $delivery_user_id = $data['delivery_user_id'];
        //    $status = 'Arrived to Destination Hub';
        // }
        $user = auth()->user();

        if($request->input('customer_view')){
            $customer_view = $request->input('customer_view');
        }
        if($manifest_type == 'RI'){

            $destBranchId = $this->getDestBranchId($manifest_number);
            $receiver_id = $user->office_id;
            $receiver_type = $user->office_type;
            $officeDetails = $this->getOfficeDetails($data['sender_id']);
            if($officeDetails){
                $sender_id   = $officeDetails->id;
                $sender_type = $officeDetails->office_type;
            }
            if($destBranchId == $receiver_id){
               // if($status != "Booked & Dispatched"){
                     $status = 'Retrurn to Destination Hub';
                //}
            }
            $customer_view = 1;
        }
        if($manifest_type == 'RO'){

            $officeDetails = $this->getOfficeDetails($data['receiver_id']);

            $sender_id = $user->office_id;;
            $sender_type = $user->office_type;
            if($officeDetails){
                $receiver_id = $officeDetails->id;
                $receiver_type = $officeDetails->office_type;
            }
            if($sender_id == $receiver_id && $sender_type == $receiver_type){
                $status = 'Return to Origin Hub';
                $delivery_user_id = 1;
                $last_mile_delivery = 1 ;
                $customer_view = 1;
                $this->updateStatusOfIncomingManifest($manifest_number,$sender_id);
            }
        }

        $rules = [
                    'manifest_type' => 'required',
                    'manifest_number' => 'required',
                    'origin_branch_id' => 'required',
                    'dest_branch_id' => 'required',
                    'sender_id' => 'required',
                    'receiver_id' => 'required_if:last_mile_delivery,0',
                    'sender_type'=>'nullable',
                    'receiver_type'=>'nullable',
        ];
        $messages = [
            'active_url' => 'The selected :attribute is invalid.',
        ];
        if(!$officeDetails && $manifest_type == 'I'){
            $rules = [
                'sender_id' => 'active_url'
            ];
        }
        $validator = Validator::make($request->all(),$rules, $messages);
        $validator->validate();
      /// echo $data['origin_pincode_id'][$key];exit;
        $manifest = Manifest::create([
            'manifest_type' => $data['manifest_type'],
            'manifest_number' => $manifest_number,
            'origin_branch_id' => $data['origin_branch_id'][$key],
            // 'origin_pincode_id' => $data['origin_pincode_id'][$key],
            'dest_branch_id' => $data['dest_branch_id'][$key],
            // 'dest_pincode_id' => $data['dest_pincode_id'][$key],
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'sender_type' => $sender_type,
            'receiver_type' => $receiver_type,
            'consg_number_id' => 0,
            'last_mile_delivery' => $last_mile_delivery,
            'delivery_user_id' => $delivery_user_id,
            'customer_view' => $customer_view,
            'status' => $status,
            'user_id' => $user->id,
            'office_id' => $user->office_id,
            'office_type' => $user->office_type,
           // 'remarks' => $data['remarks']
           'remarks' =>''
        ]);
      

            if($customer_view == 0){
                Manifest::where('id', '=', $manifest->id)->update(array('customer_view' => $customer_view));
            }
            if($manifest){

                $status = 'Return to Hub ';
                if($delivery_user_id != null){
                    $status = 'Return to Origin Hub';
                }
                Booking::where('consg_number', $manifest_number)->update(array('status' => $status));
            }
        }
        if($manifest_type == 'RI'){ 
            return redirect()->route('returns.incoming.create')->with('success', 'Manifest has been Return successfully');
        }
        if($manifest_type == 'RO'){
            return redirect()->route('returns.outgoing.create')->with('success', 'Manifest has been Return successfully');
        }
    }
    if($manifest_type == 'RI'){
        return redirect()->route('returns.incoming.create');
    }
    if($manifest_type == 'RO'){
        return redirect()->route('returns.outgoing.create');
    }
} 

    /**
     * Display the specified resource.
     *
     * @param  \App\Manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function show(Manifest $manifest)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function edit(Manifest $manifest)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Manifest $manifest)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Manifest  $manifest
     * @return \Illuminate\Http\Response
     */
    public function destroy(Manifest $manifest)
    {
        //
    }

    //TO update status 
    public function updateStatusOfIncomingManifest($manifest_number, $receiver_id){

        $manifest =  Manifest::where('manifest_number', '=', $manifest_number)
                      ->where('manifest_type', '=', 'RI')
                      ->where('receiver_id', '=', $receiver_id)
                      ->update(['customer_view' => '0']);

    }

    /**
     * To Get Logged In Office Details
     */
    public function loggedInOffice(){

        $user = auth()->user();
        $loggedOffice = '0';
        if($user->office_type == "BR" || $user->office_type == "HO"){
            if($user->branch){
                 $loggedOffice = $user->branch;
            }
        }
        if($user->office_type == "FR"){
            if($user->franchisee){
                  $loggedOffice = $user->franchisee;
            }
        }

        return $loggedOffice;
    }

    /**
     * To Get Branch Office Details
     */
    public function branchFranchisees(){
        $franchisees = DB::table("franchisees")->select("franchisees.id","franchisees.code");
        $branches = DB::table("branches")->select("branches.id" ,"branches.code")
            ->union($franchisees)
            ->whereNull('deleted_at')
            ->get();
        return $branches;
    }

    public function branchPartners(Request $request){

        $term = trim($request->q);
        $loggedOffice = $this->loggedInOffice();
        $deliveryPartner = ['BOOKING','DELIVERY','BOTH'];
        $franchisees = DB::table("franchisees")
            ->select("franchisees.id","franchisees.code")
            ->whereIn('franchisee_type',$deliveryPartner)
            ->where('franchisees.code', 'LIKE', "%$term%")
            ->where('branch_id',$loggedOffice->id)
            ->whereNull('franchisees.deleted_at');

        $branches = DB::table("branches")->select("branches.id" ,"branches.code")
            ->union($franchisees)
            ->where('branches.code', 'LIKE' , "%$term%")
            ->whereNull('branches.deleted_at')
            ->get();

        $_branches = [];

        foreach ($branches as $branch) {
             $_branches[] = ['id' => $branch->code, 'text' => $branch->code ,'data-type' => 'FR'];
        }

        return \Response::json($_branches);
    }

     /** this is to add status based on the condition */
     public function getManifestStatus($manifest_number, $manifest_type){

        $count = Manifest::where('manifest_number', '=', $manifest_number)->count();

        if($manifest_type == "RI"){
            $status = "Return Received by Hub";
        }
        if($manifest_type == "RO"){
            $status = "Return Send to Hub";
        }
        // if($count == 0){
        //     $status = "Booked & Dispatched";
        // }
        return $status;
}

    /** To get destination branch details using consignment number */
    public function getDestBranchId($consg_number){
            
        $booking = Booking::where('consg_number', $consg_number )->first();
        if($booking){
            return $booking->dest_branch_id;
        }
        return null;
    }

    public function getOfficeDetails($br_code){
        $branch = Branch::where('code', '=', $br_code)->first();
        if($branch){
            $branch['office_type'] = $branch->branch_type;
            return $branch;
        }
        $franchisee = Franchisee::where('code', '=', $br_code)->first();
        if($franchisee){
            $franchisee['office_type'] = 'FR';
            return $franchisee;
        }
        return false;
    }

     /** this is to get employee details */
     public function getEmployees($office_type, $office_id){

        $employees =  User::where('office_type', '=', $office_type)
                      ->where('office_id', '=', $office_id)->get();
        return $employees;
}



}
