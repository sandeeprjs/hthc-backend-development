<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\BulkBooking;
use App\Consignment;
use App\Country;
use App\Customer;
use App\CustomerOffice;
use App\Delivery;
use App\File;
use App\Franchisee;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Imports\BookingImport;
use App\Module;
use App\Subscription;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;
use function foo\func;
use App\Pincode;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConsignmentBooked;
use App\Mail\BulkBookingMail;
use App\Mail\TestEmail;
use URL;



class BookingController extends Controller
{
    public function __construct() {
        $this->middleware(['auth', 'role'])->except('calculateVolumetricWeight', 'sms', 'getBulkBookingSample', 'getManifestSample');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->validate($request, [
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'consg_number' => 'nullable',
            'customer_id' => 'nullable',
            'subscription_id' => 'nullable',
            'status' => 'nullable',
            'fr_id' => 'nullable'
        ]);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $consgNumber = $request->input('consg_number'); //consg no. filter is not required as per comment from Sudhilal.
        $customerId = $request->input('customer_id');
        $subscriptionId = $request->input('subscription_id');
        $status = $request->input('status');
        $frCode = $request->input('fr_id');

        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }

        if($request->get('btnSubmit') == 'export') {

            $consg_number = $request->input('consg_number');
            $customer_id = $request->input('customer_id');
            $start_date = $request->input('start_date');
            $end_date = $request->input('end_date');
            $status = $request->input('status');
            return Excel::download(new BookingsExport($consgNumber,$customerId,$startDate,$endDate), 'bookings.xlsx');
        }

        $user = Auth::user();



        $bookings = Booking::when(!$user->isAdmin(), function ($q) use ($user) {
                $q->where('origin_office_type', $user->office_type)
                    ->where('origin_office_id', $user->office_id);
            })
            ->when($consgNumber, function ($q) use ($consgNumber) {
                $q->where('consg_number', $consgNumber);
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($customerId, function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->when($subscriptionId, function ($q) use ($subscriptionId) {
                $q->where('subscription_id', $subscriptionId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($frCode, function ($q) use ($frCode) {
                $q->where('origin_office_type', 'FR')->where('origin_office_id', $frCode);
            })
            ->latest('id')->paginate(20);

        $subscriptions = Subscription::select(['id', 'name'])->get();
        $customer = Customer::select(['id', 'code'])->where('id', $customerId)->first();
        $franchisee = Franchisee::select(['id', 'code'])->where('id', $frCode)->first();
        $bookingStatuses = Booking::distinct('status')->pluck('status');

        return view('bookings.index', compact(['bookings', 'customer', 'subscriptions', 'bookingStatuses', 'franchisee']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function create()
    {
        $subscriptions = Subscription::select(['id', 'name', 'price'])->get();
        $subscriptionLists = array();
        foreach ($subscriptions as $subscription) {
            $selected = '';
            $subscriptionLists[] = '<option value="'.$subscription->id.'"'.$selected.'>'.$subscription->name.'</option>';
        }

        $user = Auth::user();
        $user = $user->only(['id', 'username', 'first_name', 'last_name']);

        $countryList = AppHelper::countriesOptionList();
        $pincodes = Pincode::get();
        $customers = Customer::get();

        return view('bookings.create', compact(['subscriptionLists', 'countryList', 'user', 'pincodes','customers', 'subscriptions']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     * @throws \Exception
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'consg_number' => 'required|unique:bookings,consg_number,NULL,id,deleted_at,NULL',
            'consg_type' => 'required',
            'sender_name' => 'required',
            'sender_address' => 'nullable',
            'sender_area' => 'nullable',
            'sender_pincode_id' => 'required',
            'sender_city' => 'nullable',
            'sender_district' => 'nullable',
            'sender_state' => 'nullable',
            'sender_country' => 'nullable',
            'sender_sms' => 'nullable',
            'sender_mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'sender_email' => 'nullable|email',
            'receiver_name' => 'required',
            'receiver_address' => 'nullable',
            'receiver_area' => 'nullable',
            'receiver_pincode_id' => 'required',
            'receiver_city' => 'nullable',
            'receiver_district' => 'nullable',
            'receiver_state' => 'nullable',
            'receiver_country' => 'nullable',
            'receiver_sms' => 'nullable',
            'receiver_mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'receiver_email' => 'nullable|email',
            'length' => 'required_if:consg_type,non-dox',
            'breadth' => 'required_if:consg_type,non-dox',
            'height' => 'required_if:consg_type,non-dox',
            'captured_weight' => 'required_if:consg_type,dox',
            'vol_weight' => 'required_if:consg_type,non-dox',
            'booking_user_id' => 'required',
            'booked_amount' => 'required',
            'risk_covered' => 'nullable',
            'declared_consg_value' => 'required_if:risk_covered,1',
            'remarks' => 'nullable',
        ]);

        $this->validate($request, [
            'consg_number' => [  function($attribute,$val,$fail) use($request) {
                $user = auth()->user();
                $consignment = Consignment::where('consg_number',$request->input('consg_number'))->first();
                if(!$consignment){
                    $fail('Invalid Consignment Number');
                }
                if($consignment){
                    if($user->office_type == $consignment->office_type && $user->office_id == $consignment->office_id){
                        return true;
                    }else{
                        $fail('Can not book this '.$attribute . '. It was generated to different Office');
                    }
                }

            }]
        ]);

        $user = Auth::user();
        $customer = Customer::where('id', '=', $request->input('customer_id'))->first();

        if (!$customer) {
            $customer = new Customer();
            $customer->code = $request->input('customer_id');
            $customer->customer_name = $request->input('sender_name');
            $customer->city = $request->input('sender_city');
            $customer->state = $request->input('sender_state');
            $customer->add_line_1 = $request->input('sender_address');
            $customer->add_line_2 = $request->input('sender_area');
            $customer->city = $request->input('sender_city');
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

        $booking = new Booking();

        //sender details
        $booking->consg_number = $request->input('consg_number');
        $booking->consg_type = $request->input('consg_type');
        $booking->subscription_id = $request->input('subscription_id');
        $booking->customer_id = $customer->id ?? $request->input('customer_id');
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

        //consg details
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

        //select from user type
        $booking->origin_office_type = $user->office_type;
        $booking->origin_office_id = $user->office_id;
        $booking->dest_branch_id = $request->input('dest_branch_id');
        $booking->booking_user_id = $user->id;
        $booking->sms_to_sender = $request->input('sender_sms');
        $booking->sms_to_receiver = $request->input('receiver_sms');
        $booking->status = 'Booked & Dispatched';
        $booking->remarks = $request->input('remarks');
        $booking->save();

        //delivery details
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
        $delivery->office_type = $request->input('office_type'); //update it with delivery user's office type
        $delivery->office_id = $request->input('office_id'); //update it with delivery user's office id
        $delivery->delivery_datetime = $request->input('delivery_datetime');
        $delivery->delivery_status = $request->input('delivery_status'); //replace with status_id
        $delivery->delivery_user_id = $request->input('delivery_user_id');
        $delivery->no_of_attempts = $request->input('no_of_attempts');
        $delivery->no_of_pieces = $request->input('no_of_pieces');
        $delivery->penalty = $request->input('penalty');
        $delivery->actual_delivery_charge = $request->input('actual_delivery_charge');
        $delivery->remarks = $request->input('remarks');

        $delivery->save();

        if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
            AppHelper::sendTrackingMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
        }

        if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
            AppHelper::sendTrackingMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
        }

        if($booking->mobile_number!=''){
            AppHelper::sendShipperCopy($booking->customer_name, $booking->mobile_number, $booking->consg_number);
        }

        // if($booking->email){
            // $data = ['message' => 'This is a test!'];
            //Mail::to('janagiraman@netiapps.com')->send(new TestEmail($data));
            //"https://hthc.co.in/booking/acknowledgement/s-$booking->consg_number" ;
        //     Mail::to($booking->email)->send(new ConsignmentBooked($booking, $delivery,'sender'));
        // }
        // if($delivery->email){
        //     Mail::to($delivery->email)->send(new ConsignmentBooked($booking, $delivery,'receiver'));
        // }


        return redirect(route('bookings.index'))->withSuccess('Booking created successfully!');

    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

    }

    public function bulkBooking(Request $request) {
        $user = Auth::user();
        $user = $user->only(['id', 'username', 'first_name', 'last_name', 'office_type']);
        $countryList = AppHelper::countriesOptionList();

        return view('bookings.bulk', compact(['countryList', 'user']));
    }

    public function import(Request $request)
    {

        $this->validate($request, [
            'sender_name' => 'required',
            'sender_address' => 'nullable',
            'sender_area' => 'nullable',
            'sender_pincode_id' => 'required',
            'sender_city' => 'nullable',
            'sender_district' => 'nullable',
            'sender_state' => 'nullable',
            'sender_country' => 'nullable',
            'sender_sms' => 'nullable',
            'sender_mobile_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/',
            'sender_email' => 'nullable|email',
            'captured_weight' => 'required_if:consg_type,dox',
            'booking_user_id' => 'required',
            'booked_amount' => 'required',
            'risk_covered' => 'nullable',
            'declared_consg_value' => 'required_if:risk_covered,1',
            'remarks' => 'nullable',
            'book_for_partner' => 'nullable',
            'fr_id' => 'required_if:book_for_partner,1'
        ]);

        $user = Auth::user();
        $customer = Customer::where('id', '=', $request->input('customer_id'))->first();
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

        if (!$customer) {
            $customer = new Customer();
            $customer->code = $request->input('customer_id');
            $customer->customer_name = $request->input('sender_name');
            $customer->city = $request->input('sender_city');
            $customer->state = $request->input('sender_state');
            $customer->add_line_1 = $request->input('sender_address');
            $customer->add_line_2 = $request->input('sender_area');
            $customer->city = $request->input('sender_city');
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

        //booking import
        $import = new BookingImport($request->except('_token', 'excel'), $customerId);
        try {
            Excel::import($import, $request->file('excel'));
        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            $failures = $e->failures();

            foreach ($failures as $failure) {
                $failure->row(); // row that went wrong
                $failure->attribute(); // either heading key (if using heading row concern) or column index
                $failure->errors(); // Actual error messages from Laravel validator
                $failure->values(); // The values of the row that has failed.
            }
        }

        return redirect(route('bookings.validate', [$import->getBatchId(), 'has_error=0']))->withSuccess('Please review the uploaded data');
    }

    /**
     * To validate bulk data of sheet
     * @param Request $request
     * @param $batchId
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validateExcel(Request $request, $batchId)
    {
        $this->validate($request, [
            'has_error' => 'nullable'
        ]);

        $hasError = $request->input('has_error');

        $bookings = BulkBooking::where('batch_id', $batchId)
            ->where('has_error', $hasError)
            ->orderBy('updated_at', 'desc')->paginate(20);

        return view('bookings.validate', compact(['bookings', 'batchId']));
    }

    public function bulkCreate($batchId) {
        $user = Auth::user();
        $bulkBookingIds = BulkBooking::where('batch_id', $batchId)->pluck('id');
        $bulkBookings = BulkBooking::where('batch_id', $batchId)->where('has_error', 0)->get();

        foreach ($bulkBookings as $key => $bulkBooking) {
            $consignment = AppHelper::generateSingleConsignment($user->office_type, $user->office_id, $batchId);

            $booking = new Booking();

            //sender details
            $sender_email = $bulkBooking->email;
            $sender_name = $bulkBooking->customer_name;
            $booking->consg_number = $consignment->consg_number;
            $booking->consg_type = $bulkBooking->consg_type;
            $booking->subscription_id = $bulkBooking->subscription_id;
            $booking->customer_id = $bulkBooking->customer_id;
            $booking->customer_name = $bulkBooking->customer_name;
            $booking->gender = $bulkBooking->gender;
            $booking->mobile_number = $bulkBooking->mobile_number;
            $booking->phone_number = $bulkBooking->phone_number;
            $booking->email = $bulkBooking->email;
            $booking->add_line_1 = $bulkBooking->add_line_1;
            $booking->add_line_2 = $bulkBooking->add_line_2;
            $booking->district = $bulkBooking->add_line_2;
            $booking->landmark = $bulkBooking->landmark;
            $booking->pincode_id = $bulkBooking->pincode_id;
            $booking->city = $bulkBooking->city;
            $booking->state = $bulkBooking->state;
            $booking->country_id = $bulkBooking->country_id;
            $booking->batch_id = $bulkBooking->batch_id;
            //consg details
            $booking->weight = $bulkBooking->weight;
            $booking->booked_amount = $bulkBooking->booked_amount;
            $booking->amount_due = $bulkBooking->amount_due;
            $booking->payment_mode = $bulkBooking->payment_mode;
            $booking->payment_id = $bulkBooking->payment_id;
            $booking->insured = $bulkBooking->insured;
            $booking->declared_consg_value = $bulkBooking->declared_consg_value;

            //select from user type
            $booking->origin_office_type = $bulkBooking->origin_office_type;
            $booking->origin_office_id = $bulkBooking->origin_office_id;
            $booking->booking_user_id = $user->id;
            $booking->sms_to_sender = $bulkBooking->sms_to_sender;
            $booking->sms_to_receiver = $bulkBooking->sms_to_receiver;
            $booking->status = 'Booked & Dispatched';
            $booking->save();

            //delivery details
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

            //uncomment before moving to the server
//            if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
//                AppHelper::sendTrackingMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
//            }
            if($delivery->email){
                Mail::to($delivery->email)->send(new ConsignmentBooked($booking, $delivery,'receiver'));
            }

        }

        $batch_id = $batchId * env('ENC_KEY');
        if($sender_email){

            Mail::to($sender_email)->send(new BulkBookingMail($batch_id, $sender_name));
        }

        $booking = Booking::select('id', 'customer_id', 'customer_name', 'mobile_number', 'sms_to_sender')->where('batch_id', '=', $batchId)->latest('id')->first();
        if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
            // AppHelper::sendBulkTrackingMessage($booking->customer_name, $booking->mobile_number, $batch_id);
        }
        if($booking->mobile_number){
            AppHelper::sendShipperCopy($booking->customer_name, $booking->mobile_number, $batch_id);
        }




        BulkBooking::destroy($bulkBookingIds);
        $consignments = Consignment::select('id', 'consg_number')->where('batch_id', '=', $batchId)->get();
        $bookingIds = Booking::where('batch_id', $batchId)->pluck('id');

        $customer = Customer::select('id', 'code', 'customer_name')->where('id', $booking->customer_id)->first();
        $deliveries = Delivery::select('id', 'booking_id', 'receiver_name', 'add_line_1', 'add_line_2', 'city', 'mobile_number', 'pincode_id')
            ->whereIn('booking_id', $bookingIds)
            ->with(['booking' => function($q) {
                $q->select('id', 'consg_number');
            }, 'pincode' => function($q) {
                $q->select('id', 'pincode');
            }])->get();

        $success_message = '<b>'.($key + 1).'</b> booking has been successfully created for <b>'.$booking->customer_name.'</b>.';

        return redirect(route('bookings.index'))
            ->with(['bulk-success' => $success_message, 'batch_id' => $batchId, 'customer' => $customer, 'deliveries' => $deliveries]);
    }

    public function printBulkConsg(Request $request) {
        $batchId = $request->input('batchId');
        $consignments = Consignment::where('batch_id', '=', $batchId)->get();

        return $consignments;
    }

    public function sendSMS() {
//        AppHelper::sendBulkTrackingMessage('test name', '8269808219', 12);
        return env('MVAYOO_URL');

        return response()->json('sent');
    }

    public function sendSMSTest() {

        $mobileNumbers= "8792422947";
        $AWB = "BR01-00007765";
        $CustomerName = "Druva Kumar";
        $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
        $numbers = array($mobileNumbers);
        $sender = urlencode('hthcin');
        // $url = "www.hthc.co.in/track";
        $url = "hthc.co.in/sp/s-BR01-00007765";

        //$message = "Dear $CustomerName, your AWB # $AWB is booked and will be delivered by HTHC Courier, to track $url";
        $message = "Dear $CustomerName, Your shipment is booked with HTHC Courier. Please check the shipper copy $url";

        // $url = "https://hthc.co.in/booking/acknowledgement/$consgNumber";
       // $url="https://hthc.co.in/booking";
        //$message = "Dear $Name, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, $url";

        //$message = "Dear $Name, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, %%|url^{"inputtype" : "text", "maxlength" : "150"}%%
        $numbers = implode(',', $numbers);

        // Prepare data for POST request
        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // Send the POST request with cURL
        $ch = curl_init('https://api.textlocal.in/send/');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        // $mobileNumbers= "8792422947";
        // $consgNumber = "s-HO001-00000291";
        // $Name = "Jani";
        // $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
        // $numbers = array($mobileNumbers);
        // $sender = urlencode('hthcin');
        // $url = "https://hthc.co.in/booking/acknowledgement";
        // $message = "Dear $Name, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, $url";
        // $numbers = implode(',', $numbers);

        // // Prepare data for POST request
        // $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

        // // Send the POST request with cURL
        // $ch = curl_init('https://api.textlocal.in/send/');
        // curl_setopt($ch, CURLOPT_POST, true);
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // $response = curl_exec($ch);
        // curl_close($ch);

    //     $mobileNumbers= "8792422947";
    //     $consgNumber = "s-HO001-00000291";
    //     $CustomerName = "Kubulu";
    //     $apiKey = urlencode('NTE0NTU0NmIzNzU1NGU1MzQ0NTM3NDY3Nzk1MDU0Nzk=');
    //     $numbers = array($mobileNumbers);
    //     $sender = urlencode('hthcin');
    //     $url = "https://hthc.co.in/booking/acknowledgement/$consgNumber";
    //     // $message = "Dear $CustomerName, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, $url";
    //    // $message = "Dear $CustomerName, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, $url";
    //     // $message = "Dear $CustomerName, Your shipment is booked with HTHC Courier. Please see the shipper copy for your reference, $url";
    //     $message = "Dear $CustomerName. Your consignment AWB # $consgNumber has been successfully delivered by HTHC Courier.";
    //     $numbers = implode(',', $numbers);

    //     // Prepare data for POST request
    //     $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);

    //     // Send the POST request with cURL
    //     $ch = curl_init('https://api.textlocal.in/send/');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $response = curl_exec($ch);
    //     curl_close($ch);

    // $data = array('apikey' => $apiKey);
    // $ch = curl_init('https://api.textlocal.in/get_templates/');
	// curl_setopt($ch, CURLOPT_POST, true);
	// curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	// $response = curl_exec($ch);
	// curl_close($ch);

        // Process your response here
        echo $response;

    //     $username="hthcblr@gmail.com";
    //     $hash="629b1a5d3a04dee3363302cbbe4731a57fcc2d90be0c804fc4c750a412588b3c";
    //     $test = "0";
    //     // Data for text message. This is the text message data.
    //     $sender = "TXTLCL"; // This is who the message appears to be from.
    //   echo   $message = "Dear $name, your AWB # $AWB is booked and will be delivered by HTHC Courier, to track www.hthc.co.in/track";
    //     // $message = "Dear $CustomerName, Your AWB # $AWB has been booked successfully. To track, https://hthc.co.in/track";
    //     $message = urlencode($message);
    //     $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$mobileNumbers."&test=".$test;
    //     $ch = curl_init('http://api.textlocal.in/send/?');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $result = curl_exec($ch); // This is the result from the API
    //     curl_close($ch);

    //     // Config variables. Consult http://api.textlocal.in/docs for more info.
    //     $test = "0";
    //     // Data for text message. This is the text message data.
    //     $sender = "hthcin"; // This is who the message appears to be from.
    //    // $message = "Dear $name Your AWB # $consgNumber is Booked and will be delivered by HTHC Courier, to track $track";
    // //$message = "Dear $name Your shipment is booked. Please see the shipper copy for your reference, $url";
    //    //$message = "Dear $name Your shipment is booked. Please see this shipper copy for your reference, $url";
    //    $message = "Dear $name Your AWB # $consgNumber is Booked and will be delivered by HTHC Courier, to track $track";
    //    $message = urlencode($message);
    //     $data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$mobileNumbers."&test=".$test;
    //     $ch = curl_init('http://api.textlocal.in/send/?');
    //     curl_setopt($ch, CURLOPT_POST, true);
    //     curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    //     $result = curl_exec($ch); // This is the result from the API
    //     curl_close($ch);
    //     echo $result;
        /*
        $consgNumber = "HO001-00000291";
        $name = "Jani";
        $mobileNumbers= "9943308193";
            // Authorisation details.
          ///  echo 'base_bath=='.$base_path = base_path();
        $username =  env('TEXTLOCAL_USERNAME');
        $hash =  env('TEXTLOCAL_HASH');
        //$url = 'https://hthc.co.in/booking/acknowledgement/s-HO001-00000291' ;
        $url = 'www.hthc.co.in/booking/acknowledgement/s-'.$consgNumber;
       //  $url = "www.hthc.co.in/booking/acknowledgement/s-HO001-00000291" ;
        // $url = "www.hthc.co.in/track?consg_number=$consgNumber" ;

         // Authorisation details.
	// $username = "hthcblr@gmail.com";
	// $hash = "629b1a5d3a04dee3363302cbbe4731a57fcc2d90be0c804fc4c750a412588b3c";

	// Config variables. Consult http://api.textlocal.in/docs for more info.
	$test = "0";

	// Data for text message. This is the text message data.
	$sender = "hthcin"; // This is who the message appears to be from.
	$numbers = "9943308193"; // A single number or a comma-seperated list of numbers
	$message = "Dear $name Your shipment is booked. Please see the shipper copy for your reference, $url";
	// $message = rawurlencode("Dear $name Your shipment is booked. Please see the shipper copy for your reference, $url");
	// 612 chars or less
	// A single number or a comma-seperated list of numbers
	$message = urlencode($message);
	$data = "username=".$username."&hash=".$hash."&message=".$message."&sender=".$sender."&numbers=".$numbers."&test=".$test;
	$ch = curl_init('http://api.textlocal.in/send/?');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$result = curl_exec($ch); // This is the result from the API
    curl_close($ch);
    */

    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return redirect(route('bookings.index'))->withSuccess('Booking Id not found!');
        }

        $subscriptions = Subscription::select(['id', 'name', 'price'])->where('consg_type', '=', $booking->consg_type)->get();
        $subscriptionLists = [];
        foreach ($subscriptions as $subscription) {
            $selected = $booking->subscription_id == $subscription->id ? 'selected' : '';
            $subscriptionLists[] = '<option value="'.$subscription->id.'"'.$selected.'>'.$subscription->name.'</option>';
        }

        $user = User::select(['id', 'username', 'first_name', 'last_name'])->where('id', '=', $booking->booking_user_id)->first();
        if (!$user) {
            $user = [
                'id' => '',
                'username' => 'Unknown',
                'first_name' => '',
                'last_name' => ''
            ];
        }

        $delivery = Delivery::where('booking_id', $booking->id)->first();

        $senderCountryId = Country::where('id', $booking->country_id)->pluck('id')->first();
        $senderCountryList = AppHelper::countriesOptionList($senderCountryId ?? null);

        $receiverCountryList = [];
        if ($delivery) {
            $receiverCountryId = Country::where('id', $delivery->country_id)->pluck('id')->first();
            $receiverCountryList = AppHelper::countriesOptionList($receiverCountryId ?? null);
        }

        $logedInUser = Auth::user();
        $segment = $request->segment(2);
        $module = Module::where('name', '=', $segment)->first();
        $deletePermission = null;

        if ($module) {
            foreach ($logedInUser->roles as $role) {
                if ($role->hasDeletePermission($module->id)) {
                    $deletePermission = 1;
                }
            }
        }

        return view('bookings.edit', compact([
            'delivery', 'booking', 'subscriptionLists',
            'senderCountryList', 'receiverCountryList', 'user',
            'deletePermission'
        ]));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function update(Request $request, $id)
    {
        $rules = [
            'consg_type' => 'required',
            'sender_name' => 'required',
            'sender_address' => 'nullable',
            'sender_area' => 'nullable',
            'sender_pincode_id' => 'required',
            'sender_city' => 'nullable',
            'sender_district' => 'nullable',
            'sender_state' => 'nullable',
            'sender_country' => 'nullable',
            'sender_sms' => 'nullable',
            'sender_mobile_number' => 'nullable|required_if:sender_sms,1|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'sender_email' => 'nullable|email',
            'receiver_name' => 'required',
            'receiver_address' => 'nullable',
            'receiver_area' => 'nullable',
            'receiver_pincode_id' => 'required',
            'receiver_city' => 'nullable',
            'receiver_district' => 'nullable',
            'receiver_state' => 'nullable',
            'receiver_country' => 'nullable',
            'receiver_sms' => 'nullable',
            'receiver_mobile_number' => 'nullable|required_if:receiver_sms,1|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_phone_number' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10',
            'receiver_email' => 'nullable|email',
            'length' => 'required_if:consg_type,non-dox',
            'breadth' => 'required_if:consg_type,non-dox',
            'height' => 'required_if:consg_type,non-dox',
            'captured_weight' => 'required_if:consg_type,dox',
            'vol_weight' => 'required_if:consg_type,non-dox',
            'booking_user_id' => 'required',
            'booked_amount' => 'nullable',
            'risk_covered' => 'nullable',
            'declared_consg_value' => 'required_if:risk_covered,1',
            'remarks' => 'nullable'
        ];


        $messages = [
            'required' => 'The :attribute field is required.'
        ];

        $booking = Booking::find($id);

        if($booking->consg_number != $request->input('consg_number')) {
            $rules['consg_number'] = 'required|unique:bookings';
        }
        Validator::make($request->all(),$rules, $messages)->validate();

        $this->validate($request, [
            'consg_number' => [  function($attribute,$val,$fail) use($request) {
                $user = auth()->user();
                $consignment = Consignment::where('consg_number',$request->input('consg_number'))->first();
                if(!$consignment){
                    $fail('Invalid Consignment Number');
                }
                // if($consignment){
                //     if($user->office_type == $consignment->office_type && $user->office_id == $consignment->office_id){
                //         return true;
                //     }else{
                //         $fail('Can not book this '.$attribute . '. It was generated to different Office');
                //     }
                // }

            }]
        ]);

        //sender details
        $booking->consg_number = $request->input('consg_number');
        $booking->consg_type = $request->input('consg_type');
        $booking->subscription_id = $request->input('subscription_id');
        $booking->customer_id = $request->input('customer_id');
        $booking->customer_name = $request->input('sender_name');
//        $booking->gender = $request->input('gender');
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

        //consg details
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

        //select from user type
        $booking->dest_branch_id = $request->input('dest_branch_id');
        $booking->sms_to_sender = $request->input('sender_sms');
        $booking->sms_to_receiver = $request->input('receiver_sms');
        $booking->remarks = $request->input('remarks');
        $booking->save();

        //delivery details
        $delivery = Delivery::where('booking_id', $booking->id)->first();
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
        $delivery->office_type = $request->input('office_type'); //update it with delivery user's office type
        $delivery->office_id = $request->input('office_id'); //update it with delivery user's office id
        $delivery->delivery_datetime = $request->input('delivery_datetime');
        $delivery->delivery_status = $request->input('delivery_status'); //replace with status_id
        $delivery->delivery_user_id = $request->input('delivery_user_id');
        $delivery->no_of_attempts = $request->input('no_of_attempts');
        $delivery->no_of_pieces = $request->input('no_of_pieces');
        $delivery->penalty = $request->input('penalty');
        $delivery->actual_delivery_charge = $request->input('actual_delivery_charge');
        $delivery->remarks = $request->input('remarks');
        $delivery->save();

        if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
            AppHelper::sendTrackingMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
        }

        if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
            AppHelper::sendTrackingMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
        }

        if($booking->mobile_number != ''){
            AppHelper::sendShipperCopy($booking->customer_name, $booking->mobile_number, $booking->consg_number);
        }


        return redirect(route('bookings.index'))->withSuccess('Booking updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            echo 'not found';
        }

        $booking->delete();

        return redirect(route('bookings.index'))->withSuccess('Booking deleted successfully!');
    }

    public function calculateVolumetricWeight(Request $request) {
        $this->validate($request, [
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric'
        ]);

        $length = $request->input('length');
        $width = $request->input('width');
        $height = $request->input('height');

        $volWeight = ($length * $width * $height) / 5000;

        return response()->json([
            'message' => 'success',
            'volWeight' => $volWeight
        ], 200);
    }

   public function bulkBookingDetails(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $booking = BulkBooking::where('id', $request->input('id'))
            ->select(['receiver_name', 'receiver_add_line_1', 'receiver_add_line_2', 'wrong_pincode', 'receiver_mobile_number', 'receiver_pincode_id'])
            ->with('pincode')->first();

       if (!$booking) {
           return response()->json([
               'error' => 'not found'
           ], 404);
       }

        return response()->json($booking);
   }

   public function sheetUpdate(Request $request) {
        $this->validate($request, [
            'bulk_booking_id' => 'required',
            'receiver_name' => 'nullable',
            'receiver_address' => 'nullable',
            'receiver_area' => 'nullable',
            'receiver_pincode_id' => 'required',
            'receiver_mobile' => 'nullable'
        ]);
        $id = $request->input('bulk_booking_id');

        $booking = BulkBooking::where('id', $id)->first();

        if (!$booking) {
            return response()->json([
                'message' => 'incorrect ID'
            ], 404);
        }

        $booking->receiver_name = $request->input('receiver_name');
        $booking->receiver_add_line_1 = $request->input('receiver_address');
        $booking->receiver_add_line_2 = $request->input('receiver_area');
        $booking->receiver_pincode_id = $request->input('receiver_pincode_id');
        $booking->receiver_mobile_number = $request->input('receiver_mobile');
        $booking->has_error = 0;
        $booking->save();

        return response()->json([
            'message' => 'ok'
        ], 200);
   }

   public function rowDelete(Request $request) {
        $this->validate($request, [
            'deleteId' => 'required'
        ]);

        $booking = BulkBooking::find($request->input('deleteId'));
        if (!$booking) {
            return response()->json([
                'message' => 'failed'
            ], 412);
        }

        $booking->delete();

        return response()->json([
            'message' => 'success'
        ], 200);
   }


   /*
    * To download the bulk booking sample file
    */
   public function getBulkBookingSample() {
       $file = public_path().'/files/sample_bulk_booking.xlsx';

       return Response::download($file, 'bulk_booking_sample.xlsx');
   }

   /**
    * To check consignment number generated by current logged office
    */
    public function checkConsgNumberBelongsToCurrentLoggedOffice($consg_number){

        $user = auth()->user();
        // print_r($user->office_type);
        // print_r($user->office_id);
        $consignment = Consignment::where('consg_number',$consg_number)->first();
        // print_r($consignment->office_type);
        // print_r($consignment->office_id);
        if($user->office_type == $consignment->office_type && $user->office_id == $consignment->office_id){
            return true;
        }
        return false;
    }

    /**
     * To show the delivered view
     */
     /** Employee View */
     public function view($id){

        $booking = Booking::where('id',$id)
        ->with('delivery', 'office', 'delivery.receiverImageUrl', 'delivery.receiverSignUrl','delivery.user','delivery.deliveryBranch')->first();
      //dd($booking);
        return view('bookings.view', compact(['booking']));

     }

     public function emailTest(){

        $data = ['message' => 'This is a test!'];

        Mail::to('janagiraman@netiapps.com')->send(new TestEmail($data));

     }

     public function testMail(){
        $data = ['message' => 'This is a test!'];

        Mail::to('janagiraman@netiapps.com')->send(new TestEmail($data));
     }


}
