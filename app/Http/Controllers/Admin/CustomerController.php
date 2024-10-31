<?php

namespace App\Http\Controllers\Admin;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Subscription;
use Illuminate\Http\Request;
use App\Http\Helpers\AppHelper;
use App\Pincode;
use App\CustomerOffice;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role'])->except(['search', 'customerDetails']);
    }

    public function index(Request $request)
    {
        //
        $this->validate($request, [
            'customer_code' => 'nullable',
            'customer_name' => 'nullable',
            'mobile_number' => 'nullable'
        ]);

        $customer_code = $request->input('customer_code');
        $customer_name = $request->input('customer_name');
        $mobile_number = $request->input('mobile_number');

        $subscriptions = Subscription::all();
        $user = auth()->user();
        $customerId[] = 0;

        $cust = CustomerOffice::where('office_type', '=', $user->office_type)
                ->where('office_id', '=', $user->office_id)->get();
        if($cust){
            foreach($cust as $customer){
                $customerId[]=$customer->customer_id;
            }
        }
        $customers = Customer::when($customer_code, function ($q) use ($customer_code){
            return $q->where('code', 'LIKE', "%$customer_code%");
        })->when($customer_name, function ($q) use ($customer_name){
            return $q->where('customer_name', 'LIKE', "%$customer_name%");
        })->when($mobile_number, function ($q) use ($mobile_number){
            return $q->where('mobile_number', 'LIKE', "%$mobile_number%");
        })
        ->whereIn('id' , $customerId)
        ->orderBy('updated_at', 'desc')->paginate('10');


        return view('customers.index', compact('customers', 'subscriptions'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $subscriptions = Subscription::all();
        $countryList = AppHelper::countriesOptionList();
        $pincodes = Pincode::get();
        return view('customers.create', compact('subscriptions','countryList','pincodes'));
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
        $user = auth()->user();

        $request->validate([
            'code' => 'required|unique:customers,code,NULL,id,deleted_at,NULL',
            'customer_name' => 'required|regex:/^[a-zA-Z ]+$/',
            'pincode_id' => 'required|numeric',
            'email' => 'nullable|email|unique:customers',
            'email_verified_at' => '',
            'email_verification_code' => '',
            'mobile_number' => 'required|digits:10|regex:/^[0-9]/',
            'mobile_verification_code' => '',
            'mobile_verified_at' => '',
            'subscription_id' => '',
            'add_line_2' => '',
            'district' => '',
            'active' => '',
            'remarks' => '',
            'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
        ]);
        $customer = Customer::create($request->all());

        $office   = CustomerOffice::create(
                        [
                        'customer_id' => $customer->id,
                        'office_type' => $user->office_type,
                        'office_id' => $user->office_id
                        ]
        );


        return redirect()->route('customers.index')
            ->with('success', 'customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function show(Customer $customer)
    {
        //
        $subscriptions = Subscription::all();
        return view('customers.show', compact('customer','subscriptions'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function edit(Customer $customer)
    {
        //

        $subscriptions = Subscription::all();
        $pincodes = Pincode::get();
        $countryList = AppHelper::countriesOptionList();
        return view('customers.edit', compact('customer','subscriptions','pincodes','countryList'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Customer $customer)
    {



        $request->validate([
            'code' => 'required|unique:customers,code,'.$customer->id,
            'customer_name' => 'required|regex:/^[a-zA-Z ]+$/',
            'pincode_id' => 'required|numeric',
            'email' => 'nullable|email|unique:customers,email,'.$customer->id, 
            'mobile_number' => 'required|digits:10|regex:/^[0-9]/',
            'email_verified_at' => '',
            'email_verification_code' => '',
            'mobile_verification_code' => '',
            'mobile_verified_at' => '',
            'subscription_id' => '',
            'add_line_2' => '',
            'active' => '',
            'district' => '',
            'remarks' => '',
            'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
        ]);

        $customer->update($request->all());

        return redirect()->route('customers.index')
            ->with('success', 'customer updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Customer  $customer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customer $customer)
    {
        //
        $customer->delete();

        return redirect()->route('customers.index')
            ->with('success', 'customer deleted successfully');
    }


    public function customerDetails(Request $request) {
        $this->validate($request, [
            'id' => 'required'
        ]);

        $customer = Customer::where('id', $request->input('id'))
            ->select(['id', 'customer_name', 'add_line_1', 'city', 'state', 'country_id', 'pincode_id', 'email', 'mobile_number'])
            ->first();

        if (!$customer) {
            return response()->json([
                'error' => 'not found'
            ], 404);
        }

        $pincode = Pincode::where('id', $customer->pincode_id)->select(['pincode'])->first();

        return response()->json([
            'customer' => $customer,
            'pincode' => $pincode->pincode
        ],200);
    }

    public function customerSearch(Request $request) {
        $term = $request->input('term');
        $customers = Customer::where('code', 'LIKE', "%$term%")->get();

//        $customerList = array();
//        foreach ($customers as $customer) {
//            $customerList[] = '<li data-value="'.$customer->id.'" onClick="selectCountry('.$customer->code.')">'.$customer->code.'</li>';
//        }

        $output = '<ul class="dropdown-menu" style="display:block; position:relative">';
        foreach($customers as $customer)
        {
            $output .= '<li data-id="'.$customer->id.'"><a href="#">'.$customer->code.'</a></li>';
        }
        $output .= '</ul>';

        return response()->json($output);
    }

    /*
     * fetch customers based on code
     */
    public function search(Request $request)
    {
        $term = trim($request->q);

        if (!$term) {
            return response()->json('');
        }

        $user = Auth::user();
        $customerIds = CustomerOffice::where('office_type', $user->office_type)->where('office_id', $user->office_id)->pluck('customer_id');

        $customers = Customer::select(['id', 'code as text'])->where('code', 'LIKE', "$term%")->get();
        // $customers = Customer::whereIn('id', $customerIds)->select(['id', 'code as text'])->where('code', 'LIKE', "$term%")->get();

        return response()->json($customers);
    }
}
