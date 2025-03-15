<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

use App\Manifest;
use App\Booking;
use App\Delivery;
use App\Branch;
use App\Franchisee;
use App\User;

class ManifestController extends Controller
{
    public function __construct() {
        return $this->middleware(['auth', 'role'])->except(['branchFranchisee','bookingDetails', 'bookingDetailsForReturns']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
    //     $manifest_type = $request->type;
    //     if($manifest_type !=''){
    //         $user = auth()->user();
    //         $manifests = Manifest::when($manifest_type,function($q) use($manifest_type,$user){

    //                 if($manifest_type == 'I'){
    //                     $q->where('receiver_id','=', $user->office_id)->where('receiver_type', '=', $user->office_type);
    //                 }
    //                 if($manifest_type == 'O'){
    //                     $q->where('sender_id','=', $user->office_id)->where('sender_type', '=', $user->office_type);
    //                 }
    //                 return $q->where('manifest_type', '=', "$manifest_type");

    //         })->latest()->paginate('10');

    //         return view('manifests.index',compact('manifests','manifest_type'));
    //    }
    //    return redirect()->route('manifests.incoming');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function incoming(){

        $user = auth()->user();
        $manifests = Manifest::with([
            'booking',
            'booking.pincode',
            'booking.delivery',
            'booking.delivery.pincode',
            'sender_branch',
            'sender_franchisee',
            'receiver_branch',
            'receiver_franchisee'
        ])
            ->where('receiver_id', '=', $user->office_id)
            ->where('receiver_type', '=', $user->office_type)
            ->where('manifest_type', '=', 'I')
            ->latest('id')
            ->paginate(10);

        return view('manifests.incoming',compact('manifests'));
    }

    public function incomingCreate(){
        $user = auth()->user();
        $loggedOffice = $this->loggedInOffice();
        $branchFranchisees = $this->branchFranchisees();
        Log::info('Incoming Create View Loaded', [
            'user_id' => $user->id,
            'office_type' => $user->office_type,
            'office_id' => $user->office_id,
        ]);
        return view('manifests.incomingCreate',compact('user','loggedOffice', 'branchFranchisees'));
    }

    public function outgoing(){

        $user = auth()->user();
        $manifests = Manifest::with([
            'booking',
            'booking.pincode',
            'booking.delivery',
            'booking.delivery.pincode',
            'sender_branch',
            'sender_franchisee',
            'receiver_branch',
            'receiver_franchisee'
        ])
            ->where('sender_id', '=', $user->office_id)
            ->where('sender_type', '=', $user->office_type)
            ->where('manifest_type', '=', 'O')
            ->latest('id')
            ->paginate(10);

        return view('manifests.outgoing',compact('manifests'));

    }
    public function outgoingCreate(){
        $user = auth()->user();
        $loggedOffice = $this->loggedInOffice();
        $branchFranchisees = $this->branchFranchisees();
        $employees  =  $this->getEmployees($user->office_type, $user->office_id);
        Log::info('Outgoing Create View Loaded', [
            'user_id' => $user->id,
            'office_type' => $user->office_type,
            'office_id' => $user->office_id,
        ]);
        return view('manifests.outgoingCreate',compact('user','loggedOffice', 'branchFranchisees','employees'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     * @todo- @Janagiraman need to rework
     */
    public function store(Request $request)
    {

        $data = $request->all();

        Log::info('Manifest store data:', $request->all());
        // Ensure `manifest_number` is an array
        if (!isset($data['manifest_number']) || !is_array($data['manifest_number'])) {
            return redirect()->back()->withErrors(['manifest_number' => 'Please provide at least one Consignment Number.']);
        }

        $user = auth()->user();
        try {
            foreach ($data['manifest_number'] as $key => $manifest_number) {
                Log::info('Inside Manifest store data:', $data['manifest_number']);
                // Fetch booking data based on consignment number
                $booking = Booking::where('consg_number', $manifest_number)->first();

                if (!$booking) {
                    return redirect()->back()->withErrors([
                        "manifest_number.{$key}" => "No booking found for consignment number: {$manifest_number}."
                    ])->withInput();
                }

                $status = $this->getManifestStatus($manifest_number, $data['manifest_type']);
                $delivery_user_id = null;
                $last_mile_delivery = 0;
                $customer_view = isset($data['customer_view']) ? 1 : 0;

                if ($data['manifest_type'] === 'I') {
                    $destBranchId = $this->getDestBranchId($manifest_number);
                    $receiver_id = $user->office_id;
                    $receiver_type = $user->office_type;

                    // Validate sender details
                    $officeDetails = $this->getOfficeDetails($data['sender_id']);
                    if (!$officeDetails) {
                        Log::info('office details:', $officeDetails->office_type);

                        return redirect()->back()->withErrors(['sender_id' => 'Invalid sender details.']);
                    }

                    $sender_id = $officeDetails->id;
                    $sender_type = $officeDetails->office_type;

                    // Adjust status if destination matches receiver
                    if ($destBranchId == $receiver_id && $status !== "Booked & Dispatched") {
                        Log::info('Arrived office details:', ['destBranchId' => $destBranchId]);
                        $status = 'Arrived to Destination Hub';
                    }

                }

                if ($data['manifest_type'] === 'O') {
                    $officeDetails = $this->getOfficeDetails($data['receiver_id']);
                    if (!$officeDetails) {
                        return redirect()->back()->withErrors(['receiver_id' => 'Invalid receiver details.']);
                    }

                    $sender_id = $user->office_id;
                    $sender_type = $user->office_type;
                    $receiver_id = $officeDetails->id;
                    $receiver_type = $officeDetails->office_type;

                    if ($sender_id == $receiver_id && $sender_type == $receiver_type) {
                        $status = 'Arrived to Destination Hub';
                        $delivery_user_id = 1;
                        $last_mile_delivery = 1;
                        $customer_view = 1;
                        $this->updateStatusOfIncomingManifest($manifest_number, $sender_id);
                    }
                }

                // Populate missing fields from the booking
                $data['origin_branch_id'][$key] = $booking->origin_office_id ?? null;
                $data['dest_branch_id'][$key] = $booking->dest_branch_id ?? null;

                // Validate each manifest
                $validator = Validator::make($data, [
                    "manifest_number.{$key}" => 'required',
                    "origin_branch_id.{$key}" => 'required',
                    "dest_branch_id.{$key}" => 'required',
                    'manifest_type' => 'required',
                    'sender_id' => 'required',
                    'receiver_id' => 'required_if:last_mile_delivery,0',
                ]);

                if ($validator->fails()) {
                    Log::info('validation error',$validator->attributes());
                    return redirect()->back()->withErrors($validator)->withInput();
                }

                // Save Manifest
                $manifest = Manifest::create([
                    'manifest_type' => $data['manifest_type'],
                    'manifest_number' => $manifest_number,
                    'origin_branch_id' => $data['origin_branch_id'][$key],
                    'dest_branch_id' => $data['dest_branch_id'][$key],
                    'sender_id' => $sender_id,
                    'receiver_id' => $receiver_id,
                    'sender_type' => $sender_type,
                    'receiver_type' => $receiver_type,
                    'last_mile_delivery' => $last_mile_delivery,
                    'delivery_user_id' => $delivery_user_id,
                    'customer_view' => $customer_view,
                    'status' => $status,
                    'user_id' => $user->id,
                    'office_id' => $user->office_id,
                    'office_type' => $user->office_type,
                    'remarks' => $data['remarks'] ?? '',
                ]);

                // Update Booking status
                Booking::where('consg_number', $manifest_number)->update(['status' => $status]);
            }
            $redirectRoute = $data['manifest_type'] === 'I' ? 'manifests.incoming.create' : 'manifests.outgoing.create';
            return redirect()->route($redirectRoute)->with('success', 'Manifest(s) added successfully.');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return redirect()->route('manifests.incoming.create')
                ->with('error', 'An error occurred while saving manifests: ' . $e->getMessage());
        }
    }

    /** To get destination branch details using consignment number */
    public function getDestBranchId($consg_number){

            $booking = Booking::where('consg_number', $consg_number )->first();
            if($booking){
                return $booking->dest_branch_id;
            }
            return null;
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $manifests = Manifest::get()->where('id', '=', $id)->first();
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
        if($manifests->manifest_type == 'I'){
            $office = $this->getOfficeCode($manifests->sender_type, $manifests->sender_id);
            $manifests->sender_office = $office->code;
        }
        if($manifests->manifest_type == 'O'){
            $office = $this->getOfficeCode($manifests->receiver_type, $manifests->receiver_id);
            $manifests->receiver_office = $office->code;
        }


        $branchFranchisees = $this->branchFranchisees();
        $employees  =  $this->getEmployees($user->office_type, $user->office_id);

       return view('manifests.edit',compact('manifests','user','loggedOffice', 'branchFranchisees', 'employees'));
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
        $data = $request->all();
        $user = auth()->user();
        $manifest_type = $data['manifest_type'];

        if($manifest_type == 'I'){

            $receiver_id = $user->office_id;
            $receiver_type = $user->office_type;
            $officeDetails = $this->getOfficeDetails($data['sender_id']);
            $sender_id   = $officeDetails->id;
            $sender_type = $officeDetails->office_type;
        }
        if($manifest_type == 'O'){

            $officeDetails = $this->getOfficeDetails($data['receiver_id']);
            $receiver_id = $officeDetails->id;
            $receiver_type = $officeDetails->office_type;
            $sender_id = $user->office_id;;
            $sender_type = $user->office_type;

        }
        $this->validate(
            $request, [
                'manifest_type' => 'required',
                'manifest_number' => 'required',
                'origin_branch_id' => 'required',
                'dest_branch_id' => 'required',
                'sender_id' => 'required',
                'receiver_id' => 'required',
            ]
        );

       $last_mile_delivery = 0;
       $delivery_user_id = null;
       $customer_view = 0;
       $status = $this->getManifestStatus($data['manifest_number'],$data['manifest_type']);
       if($request->input('last_mile_delivery')){
           $last_mile_delivery =  $request->input('last_mile_delivery');
           $delivery_user_id = $data['delivery_user_id'];
           $receiver_id = $user->office_id;
           $receiver_type = $user->office_type;
           $status = "Arrived to Destination Hub";
       }
       if($request->input('customer_view')){
            $customer_view = 1;
       }
       $manifest = Manifest::find($id);
       $manifest->manifest_type = $manifest_type;
       $manifest->manifest_number = $request->input('manifest_number');
       $manifest->origin_branch_id = $request->input('origin_branch_id');
       $manifest->dest_branch_id = $request->input('dest_branch_id');
       $manifest->sender_id = $sender_id;
       $manifest->sender_type = $sender_type;
       $manifest->receiver_id = $receiver_id;
       $manifest->receiver_type = $receiver_type;
       $manifest->last_mile_delivery = $last_mile_delivery;
       $manifest->delivery_user_id = $delivery_user_id;
       $manifest->remarks = $request->input('remarks');
       $manifest->status = $status;
       $manifest->customer_view = $customer_view;
       $manifest->user_id = $user->id;
       $manifest->office_id = $user->office_id;
       $manifest->office_type = $user->office_type;
       $manifest->save();

        if($manifest_type == 'I'){
             return redirect()->route('manifests.incoming')->with('success', 'Manifest has been added successfully');
        }
        if($manifest_type == 'O'){
            return redirect()->route('manifests.outgoing')->with('success', 'Manifest has been added successfully');
        }

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
        $manifest = Manifest::find($id);
        $manifest->delete();

        if($manifest->manifest_type == 'I'){
            return redirect()->route('manifests.incoming')->with('success', 'Manifest deleted successfully');
       }
       if($manifest->manifest_type == 'O'){
           return redirect()->route('manifests.outgoing')->with('success', 'Manifest deleted successfully');
       }

    }

    public function bookingDetails(Request $request, Booking $booking): \Illuminate\Http\JsonResponse
    {
        $manifestNumber = $request->input('manifest_number');
        $manifestType = $request->input('manifest_type');

        if (!$manifestNumber || !$manifestType) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Manifest number and type are required.',
            ], 400);
        }

        $senderBranch = null;
        $senderOfficeType = null;

        // Check if it's outgoing and validate if incoming is required
        if ($manifestType == "O") {
            $booking = Booking::where('consg_number', $manifestNumber)->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'There is no booking entry for this consignment number.',
                ], 404);
            }

            // Check if the current branch matches the booking's origin branch
            $currentBranchId = auth()->user()->office_id;
            if ($currentBranchId !== $booking->origin_office_id) {
                // Require an incoming manifest if the consignment is already moved
                $incoming = $this->checkIncomingOrNot($manifestNumber);

                if (!$incoming) {
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'There is no incoming manifest for this consignment number.',
                    ], 404);
                }
            }
        }

        $isManifestExists = $this->checkIsManifestExists($manifestType, $manifestNumber);

        if (!$isManifestExists) {
            $booking = Booking::where('consg_number', $manifestNumber)->first();

            if (!$booking) {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'There is no booking entry for this consignment number.',
                ], 404);
            }

            $destBranchId = $booking->dest_branch_id;
            $originBranchId = $booking->origin_office_id;
            $senderOfficeType = $booking->origin_office_type;

            // Fetch the branch codes based on IDs
            $originBranchCode = Branch::find($originBranchId)->code ?? 'N/A';
            $destBranchCode = Branch::find($destBranchId)->code ?? 'N/A';

            $senderBranch = $booking->origin_office_type === 'FR'
                ? $booking->booking_franchisee
                : $booking->booking_branch;

            return response()->json([
                'status' => 'success',
                'origin_branch_id' => $originBranchId,
                'origin_branch_code' => $originBranchCode,
                'dest_branch_id' => $destBranchId,
                'dest_branch_code' => $destBranchCode,
                'booking_office_code' => $senderBranch,
                'booking_office_type' => $senderOfficeType,
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Already exists as a manifest entry.',
        ], 409);
    }



    public function branchFranchisee(Request $request){

        $term = trim($request->q);

        $deliveryPartner = ['BOOKING','DELIVERY','BOTH'];
        $franchisees = DB::table("franchisees")
            ->select("franchisees.id","franchisees.code")
            ->whereIn('franchisee_type',$deliveryPartner)
            ->where('franchisees.code', 'LIKE', "%$term%")
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

    public function branchFranchisees(){

        $franchisees = DB::table("franchisees")->select("franchisees.id","franchisees.code");
        $branches = DB::table("branches")->select("branches.id" ,"branches.code")
            ->union($franchisees)
            ->whereNull('deleted_at')
            ->get();
        return $branches;
    }

    public function getOfficeDetails($br_code){
        $branch = Branch::where('code', '=', $br_code)->first();

        if($branch){
             Log::info($branch);
             $branch['office_type'] = $branch->branch_type;
             Log::info($branch['office_type']);
             return $branch;
        }
        $franchisee = Franchisee::where('code', '=', $br_code)->first();
        if($franchisee){
            $franchisee['office_type'] = 'FR';
            return $franchisee;
        }
        return false;
    }

    public function getOfficeCode($officeType, $officeId){

        if($officeType == 'HO' || $officeType == 'BR'){
            $office = Branch::where('id', '=', $officeId)->first();
        }
        if($officeType == 'FR'){
            $office = Franchisee::where('id', '=', $officeId)->first();
        }
        return $office;
    }

    public function getManifestDetails($manifest_number, $manifest_type){


        $manifest = Manifest::when($manifest_type,function($q) use($manifest_type, $manifest_number){

           if($manifest_type == 'I'){
                    $q->where('manifest_type', '=', 'O');
           }
           if($manifest_type == 'O'){
                    $q->where('manifest_type', '=', 'I');
           }
           return $q->where('manifest_number', '=', $manifest_number);

        })->latest()->first();

        return $manifest;
    }

    public function checkIsManifestExists($manifest_type, $manifest_number){

        $user = auth()->user();
        $manifest = Manifest::when($manifest_type,function($q) use($manifest_type, $user, $manifest_number){


            if($manifest_type == 'I'){

                $receiver_id = $user->office_id;
                $receiver_type = $user->office_type;
                $q->where('receiver_id', '=', $receiver_id)->where('receiver_type', '=', $receiver_type);
            }
            if($manifest_type == 'O'){

                $sender_id = $user->office_id;;
                $sender_type = $user->office_type;
                $q->where('sender_id', '=', $sender_id)->where('sender_type', '=', $sender_type);
            }
            return $q->where('manifest_type', '=', $manifest_type)
                     ->where('manifest_number', '=', $manifest_number);

        })->first();
      //  dd($manifest);
        if($manifest){

            return true;
        }
        return false;

    }

    public function checkIsManifestReturnExists($manifest_type, $manifest_number){

        $user = auth()->user();
        $manifest = Manifest::when($manifest_type,function($q) use($manifest_type, $user, $manifest_number){


            if($manifest_type == 'RI'){

                $receiver_id = $user->office_id;
                $receiver_type = $user->office_type;
                $q->where('receiver_id', '=', $receiver_id)->where('receiver_type', '=', $receiver_type);
            }
            if($manifest_type == 'RO'){

                $sender_id = $user->office_id;;
                $sender_type = $user->office_type;
                $q->where('sender_id', '=', $sender_id)->where('sender_type', '=', $sender_type);
            }
            return $q->where('manifest_type', '=', $manifest_type)
                     ->where('manifest_number', '=', $manifest_number);

        })->first();
      //  dd($manifest);
        if($manifest){
            $no_of_attempts = $this->getAttempts($manifest_number);

            if($no_of_attempts > 4){
                return true;
            }else{
                return false;
            }

        }
        return false;

    }
    public function getAttempts($consg_number){
        $bookings = Booking::where('consg_number', '=', $consg_number)
        ->first();
        $no_of_attempts = 0;
        if($bookings){
            if(isset($bookings->delivery->no_of_attempts)){
                $no_of_attempts = $bookings->delivery->no_of_attempts;
            }
            return $no_of_attempts;
        }
        return '0';

    }

    public function checkIncomingOrNot($manifest_number){
        $user = auth()->user();
        $manifest_type = 'I';
        $manifest = Manifest::when($manifest_type,function($q) use($manifest_type, $user, $manifest_number){
                $receiver_id = $user->office_id;
                $receiver_type = $user->office_type;
               return $q->where('manifest_number', '=', $manifest_number)->where('receiver_id', '=', $receiver_id)->where('receiver_type', '=', $receiver_type);
        })->first();
        if($manifest){
            return true;
        }
        return false;

    }

    /**
     * To check Return Incoming Or Not
     */
    public function checkReturnsIncomingOrNot($manifest_number){
        $user = auth()->user();
        $manifest_type = 'RI';
        $manifest = Manifest::when($manifest_type,function($q) use($manifest_type, $user, $manifest_number){
                $receiver_id = $user->office_id;
                $receiver_type = $user->office_type;
               return $q->where('manifest_number', '=', $manifest_number)->where('receiver_id', '=', $receiver_id)->where('receiver_type', '=', $receiver_type);
        })->first();
        if($manifest){
            return true;
        }
        return false;

    }


    /** this is to get employee details */
    public function getEmployees($office_type, $office_id){

            $employees =  User::where('office_type', '=', $office_type)
                          ->where('office_id', '=', $office_id)->get();
            return $employees;
    }

    /** this is to add status based on the condition */
    public function getManifestStatus($manifest_number, $manifest_type){

            $count = Manifest::where('manifest_number', '=', $manifest_number)->count();

            if($manifest_type == "I"){
                $status = "Arrived to Hub";
            }
            if($manifest_type == "O"){
                $status = "In Transit";
            }
            // if($count == 0){
            //     $status = "Booked & Dispatched";
            // }
            return $status;
    }

    public function updateStatusOfIncomingManifest($manifest_number, $receiver_id){

        $manifest =  Manifest::where('manifest_number', '=', $manifest_number)
                      ->where('manifest_type', '=', 'I')
                      ->where('receiver_id', '=', $receiver_id)
                      ->update(['customer_view' => '0']);

    }

    /**
     * To get booking details for Return
     */
    public function bookingDetailsForReturns(Request $request,Booking $booking){

        $sender_branch = null;
        $sender_office_type = null;

        $isDelivered = $this->checkReturnIsDelivered($request->manifest_number);
       // echo $isDelivered;exit;
        if($isDelivered){
            return response()->json([
                'status' => 'failed',
                'message' => 'This Consignment Number Already Delivered',
            ], 200);
        }



        if($request->manifest_type == "RO"){
           $incoming =  $this->checkReturnsIncomingOrNot($request->manifest_number);

           if(!$incoming){
                return response()->json([
                    'status' => 'failed',
                    'message' => 'There is no return incoming for this consignment number',
                ], 200);
           }
        }

       $isManifestExists = $this->checkIsManifestReturnExists($request->manifest_type, $request->manifest_number);

       if(!$isManifestExists){

            $manifest = $this->getManifestDetails($request->manifest_number, $request->manifest_type);

            if($manifest){
                $dest_branch_id = $manifest->dest_branch_id;
                $origin_branch_id = $manifest->origin_branch_id;

                if($request->manifest_type == "RI"){

                    if($manifest->sender_type == 'FR'){
                        $sender_branch = $manifest->sender_franchisee->code;
                        $sender_office_type = $manifest->sender_type;
                     }
                     if($manifest->sender_type == "BR" || $manifest->sender_type == "HO"){
                        $sender_branch = $manifest->sender_branch->code;
                        $sender_office_type = $manifest->sender_type;
                     }
                }

                if($request->manifest_type == "RO"){
                     $sender_branch = $manifest->sender_branch;
                     $sender_office_type = $manifest->sender_type;
                }
            }

            if(!$manifest) {

                $bookings = Booking::where('consg_number','=' , $request->manifest_number)->first()
                ->append(['booking_branch','booking_franchisee']);

                $delivery =  Delivery::where('booking_id', '=', $bookings->id)->first();

                if($bookings){

                            $dest_branch_id = $bookings->dest_branch_id;
                            $origin_branch_id = $bookings->origin_office_id;
                            // $origin_pincode_id = $bookings->pincode_id;
                            // $dest_pincode_id = $delivery->pincode_id;
                            $sender_office_type = $bookings->origin_office_type;

                            if($bookings->origin_office_type == 'HO' || $bookings->origin_office_type == 'BR'){
                                    $sender_branch   = $bookings->booking_branch;
                            }
                            if($bookings->origin_office_type == 'FR'){
                                    $sender_branch   = $bookings->booking_franchisee;

                            }
                }else{
                    return response()->json([
                        'status' => 'failed',
                        'message' => 'There is no booking entry for this consignment number',
                    ], 200);
                }

            }
            return response()->json([
                'status' => 'success',
                'origin_branch_id' => $origin_branch_id,
                'dest_branch_id' => $dest_branch_id,
                'booking_office_code' => $sender_branch,
                'booking_office_type' => $sender_office_type
                // 'origin_pincode_id' => $origin_pincode_id,
                // 'dest_pincode_id' => $dest_pincode_id
            ], 200);
        }

        return response()->json([
            'status' => 'failed',
            'message' => 'Already Exists this Manifest entry',

        ], 200);


    }

    public function checkReturnIsDelivered($manifest_number){

        $isDelivered = Booking::where('consg_number','=' , $manifest_number)
          ->where('status', '=', 'Delivered')->first();
        if($isDelivered){
            return true;
        }
        return false;
    }
}
