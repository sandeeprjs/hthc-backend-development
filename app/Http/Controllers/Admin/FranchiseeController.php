<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Franchisee;
use App\Branch;
use App\ServiceablePin;
use App\Pincode;
use App\Country;
use Session;
use Illuminate\Support\Facades\Storage;

class FranchiseeController extends Controller
{

     /**
     * Instantiate a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      return $this->middleware(['auth', 'role']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'enterprise_name' => ['required'],

        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $this->validate($request, [
            'filter_by' => 'nullable',
            'filter_value' => 'nullable',
            'filter_val' => 'nullable'
        ]);

        $user = auth()->user();
        $filter_by = $request->input('filter_by');
        $filter_val = $request->input('filter_val');
        if($filter_val == '')
        $filter_val = $request->input('filter_value');
        $franchisees = Franchisee::when($filter_by, function ($q) use ($filter_by, $filter_val){
                    if($filter_by == 'CD'){
                       return $q->where('code', 'LIKE', "%$filter_val%");
                    }
                    if($filter_by == 'NM'){
                        return $q->where('enterprise_name', 'LIKE', "%$filter_val%");
                    }
                    if($filter_by == 'TY'){
                        return $q->where('franchisee_type', '=', "$filter_val");
                    }
                    if($filter_by == 'MBL'){
                        return $q->where('mobile_number', 'LIKE', "%$filter_val%");
                    }

                })
                ->when(!$user->isAdmin(), function($q) use ($user){
                    if(!$user->isAdmin()){
                        return $q->where('branch_id', '=', $user->office_id);
                    }
                })
                ->orderBy('updated_at', 'desc')->paginate('10');




        return view('franchisees.index',compact('franchisees'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $user = auth()->user();
        $branches = Branch::when(!$user->isAdmin(), function($q) use ($user){
            if(!$user->isAdmin()){
                return $q->where('id', '=', $user->office_id);
            }
        })->get();
        $countries = Country::all();
        $pincodes = Pincode::get();
        return view('franchisees.create',compact('branches','countries','pincodes','user'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        // echo '<pre>';
        // print_r($request->all());
        // exit;
        $data =  $request->all();
        $this->validate(
            $request, [
                'branch_id' => 'required',
                'email' => 'nullable|email|max:255|unique:franchisees,email',
                'mobile_number' => 'required|digits:10|regex:/^[0-9]/',
                'code' => 'required|min:1|unique:branches,code|unique:franchisees,code,NULL,id,deleted_at,NULL',
                'enterprise_name' => 'required',
                'pincode_id' => 'required',
                'service_pincode_id' => 'required',
                'phone_number' => 'nullable|regex:/^[0-9 ]+$/',
                'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'contact_person_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'franchisee_type' => 'required',
                'current_bank_name' => '',
                'branch_name' => '',
                'account_number' => '',
                'ifsc_code' => '',
                'avatar' => 'image|mimes:jpeg,png,jpg|max:4028',
                'doc_proof' => 'image|mimes:jpeg,png,jpg|max:4028',
            ],
            [
                'code.required' => 'Franchisee Code is required',
                'enterprise_name.required' => 'Franchisee Name is required',
                'add_line_1.required' => 'Address is required',
                'pincode_id.required' => 'Origin Pincode is required',
                'contact_person_name.required' => 'Contact Person Name is required',
                'service_pincode_id.required' => 'Serviceable Pincodes is required'
            ]


        );

        // Handle avatar file upload
        $avatarimage = $franchisee->avatar ?? null; // Retain existing avatar if no new file is uploaded
        if ($request->hasFile('avatar')) {
            if ($avatarimage) {
                Storage::delete('public/uploads/partners/photo/' . $avatarimage); // Delete old file
            }
            $profile = $request->file('avatar');
            $avatarimage = md5($profile->getClientOriginalName() . time()) . "." . $profile->getClientOriginalExtension();
            $profile->storeAs('public/uploads/partners/photo', $avatarimage);
        }

        // Handle document proof file upload
        $doc_proof = $franchisee->doc_proof ?? null; // Retain existing doc_proof if no new file is uploaded
        if ($request->hasFile('doc_proof')) {
            if ($doc_proof) {
                Storage::delete('public/uploads/partners/idproof/' . $doc_proof); // Delete old file
            }
            $doc = $request->file('doc_proof');
            $doc_proof = md5($doc->getClientOriginalName() . time()) . "." . $doc->getClientOriginalExtension();
            $doc->storeAs('public/uploads/partners/idproof', $doc_proof);
        }


        $franchisee = Franchisee::create([
            'branch_id' => $data['branch_id'],
            'email' => $data['email'],
            'mobile_number' => $data['mobile_number'],
            'code' => $data['code'],
            'enterprise_name' => $data['enterprise_name'],
            'pincode_id' => $data['pincode_id'],
          //  'service_pincode_id' => $data['service_pincode_id'],
            'phone_number' => $data['phone_number'],
            'city' => $data['city'],
            'state' => $data['state'],
            'contact_person_name' => $data['contact_person_name'],
            'franchisee_type' => $data['franchisee_type'],
            'current_bank_name' => $data['current_bank_name'],
            'branch_name' => $data['branch_name'],
            'account_number' => $data['account_number'],
            'ifsc_code' => $data['ifsc_code'],
            'avatar' => $avatarimage,
            'doc_proof' => $doc_proof,
            'add_line_1' => $data['add_line_1']

            ]);

        $serviceablePin = $request->get('service_pincode_id');

        if($serviceablePin){
            foreach($serviceablePin as $key => $pincode){
                ServiceablePin::create(
                        [
                         'office_type' => 'FR',
                         'office_id' => $franchisee->id,
                         'pincode_id' => $pincode
                        ]
                );
            }
        }

        return redirect()->route('franchisees.index')->with('success', 'Franchisee added successfully!');



       // $franchisee = Franchisee::create($request->all());
       // $serviceablePin = $request->get('service_pincode_id');

        // if($serviceablePin){
        //     foreach($serviceablePin as $key => $pincode){
        //         ServiceablePin::create(
        //                 [
        //                  'office_type' => 'FR',
        //                  'office_id' => $franchisee->id,
        //                  'pincode_id' => $pincode
        //                 ]
        //         );
        //     }
        // }

        // return redirect()->route('franchisees.index')->with('success', 'Franchisee added successfully!');


    }

    /**
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
        $franchisee = Franchisee::get()->where('id', $id)->first();
        $pincodes = Pincode::get();
        $branches = Branch::get()->all();
        $countries = Country::all();



        return view('franchisees.create',compact('branches','franchisee','pincodes','countries'));
    }

    /** Partners Details View */
    public function view($id){
        $franchisee = Franchisee::get()->where('id', $id)->first();
        $pincodes = Pincode::get();
        $branches = Branch::get()->all();
        $countries = Country::all();


        return view('franchisees.view',compact('branches','franchisee','pincodes','countries'));
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

        $this->validate(
            $request, [
                'branch_id' => 'required',
                'email' => 'nullable|email|max:255|unique:franchisees,email,'.$id,
                'mobile_number' => 'required|digits:10|numeric',
                'code' => 'required|unique:branches,code|unique:franchisees,code,'.$id.',id,deleted_at,NULL',
                'enterprise_name' => 'required',
                'pincode_id' => 'required',
                'service_pincode_id' => 'required|array|min:1',
                'phone_number' => 'nullable|regex:/^[0-9 ]+$/',
                'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'franchisee_type' => 'required',
                'contact_person_name' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'current_bank_name' => '',
                'branch_name' => '',
                'account_number' => '',
                'ifsc_code' => '',
                'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:4028',
                'doc_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:4028',

            ],
            [
                'code.required' => 'Franchisee Code is required',
                'enterprise_name.required' => 'Franchisee Name is required',
                'add_line_1.required' => 'Address is required',
                'pincode_id.required' => 'Origin Pincode is required',
                'contact_person_name.required' => 'Contact Person Name is required',
                'service_pincode_id.required' => 'Serviceable Pincodes is required'
            ]
        );
// echo '<pre>';
// print_r($request->input());exit;
        $avatarimage = null;
        if($request->input('old_avatar') != ''){
            $avatarimage = $request->input('old_avatar');
        }
        if (request()->hasFile('avatar')) {

            Storage::delete('/public/uploads/partners/photo/' . $avatarimage);
            $profile = request()->file('avatar');
            $avatarimage = md5($profile->getClientOriginalName() . time()) . "." . $profile->getClientOriginalExtension();
            $profile->move('./storage/uploads/partners/photo', $avatarimage);

        }
        $doc_proof = null;
        if($request->input('old_doc_proof') != ''){
            $doc_proof = $request->input('old_doc_proof');
        }
        if (request()->hasFile('doc_proof')) {
            Storage::delete('/public/uploads/partners/idproof/' . $doc_proof);
            $doc = request()->file('doc_proof');
            $doc_proof = md5($doc->getClientOriginalName() . time()) . "." . $doc->getClientOriginalExtension();
            $doc->move('./storage/uploads/partners/idproof', $doc_proof);
        }

        $franchisee = Franchisee::find($id);
        $franchisee->email = $request->input('email');
        $franchisee->mobile_number = $request->input('mobile_number');
        $franchisee->code = $request->input('code');
        $franchisee->enterprise_name = $request->input('enterprise_name');
        $franchisee->pincode_id = $request->input('pincode_id');
       // $franchisee->service_pincode_id = $request->input('service_pincode_id');
        $franchisee->phone_number = $request->input('phone_number');
        $franchisee->city = $request->input('city');
        $franchisee->state = $request->input('state');
        $franchisee->franchisee_type = $request->input('franchisee_type');
        $franchisee->contact_person_name = $request->input('contact_person_name');
        $franchisee->current_bank_name = $request->input('current_bank_name');
        $franchisee->branch_name = $request->input('branch_name');
        $franchisee->account_number = $request->input('account_number');
        $franchisee->ifsc_code = $request->input('ifsc_code');
        $franchisee->avatar = $avatarimage;
        $franchisee->doc_proof = $doc_proof;
        $franchisee->add_line_1 = $request->input('add_line_1');

        //$franchisee->fill($request->all());
        $franchisee->save();

        $franchisee->serviceablePins()->delete();

        if ($request->get('service_pincode_id')) {
            $serviceable_pins = array_unique($request->get('service_pincode_id'));

            foreach ($serviceable_pins as $pincode_id) {
                $franchisee->serviceablePins()->create([
                    'office_type' => 'FR',
                    'pincode_id' => $pincode_id
                ]);
            }
        }
        return redirect()->route('franchisees.index')->with('success', 'Franchisee Updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Franchisee $franchisee)
    {
        $franchisee->delete();

        return redirect()->route('franchisees.index')
            ->with('success', 'Franchisee deleted successfully');

    }

    public function messages($id = '')
    {
            return [
                'enterprise_name.required' => 'Franchisee Name is required',

            ];
    }

    /**
     * To get Booking Partner
     */
    public function bookingPartner(Request $request){
        $term = $request->input('term');
        $bookingPartner = ['BOOKING','BOTH'];
        $partner = Franchisee::select(['id', 'code as text'])->where('code', 'LIKE', "$term%")
        ->whereIn('franchisee_type', $bookingPartner)->get();


        return response()->json($partner);
    }

     /**
     * To get Delivery Partner
     */
    public function deliveryPartner(Request $request){
        $term = $request->input('term');
        $deliveryPartner = ['DELIVERY','BOTH'];
        $partner = Franchisee::select(['id', 'code as text'])->where('code', 'LIKE', "$term%")
        ->whereIn('franchisee_type', $deliveryPartner)->get();


        return response()->json($partner);
    }
}
