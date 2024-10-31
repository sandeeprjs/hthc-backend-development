<?php

namespace App\Http\Controllers\Admin;

use App\Franchisee;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Branch;
use App\ServiceablePin;
use App\Pincode;
use App\Country;
use Session;
use App\Consignment;

class BranchController extends Controller
{
    public function __construct() {
        return $this->middleware(['auth', 'role'])->except(['find', 'serviceableBranches', 'officeList']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


    $this->validate($request, [
        'branch_code' => 'nullable',
        'branch_name' => 'nullable',
        'city' => 'nullable'
    ]);

    $branch_code = $request->input('branch_code');
    $branch_name = $request->input('branch_name');
    $pincode = $request->input('pincode');


    $branches = Branch::when($branch_code, function ($q) use ($branch_code){

            return $q->where('code', 'LIKE', "%$branch_code%");

        })->when($branch_name, function ($q) use ($branch_name){

            return $q->where('branch_name', 'LIKE', "%$branch_name%");

        })->when($pincode, function ($q) use ($pincode){

             $pinCodeId = '0';
            $pinid = Pincode::where('pincode','=',"$pincode")->first();
            if($pinid){
                $pinCodeId = $pinid->id;
            }
            return $q->where('pincode_id', '=', "$pinCodeId");

        })->leftJoin('pincodes', 'pincodes.id', '=', 'branches.pincode_id')
          ->select('pincodes.pincode','branches.*')->orderBy('updated_at', 'desc')->paginate('10');

        if(!$branches){
        $branches = array();
    }

       return view('branches.index',compact('branches'));
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        //
        $countries = Country::all();
        $pincodes = Pincode::get();
        return view('branches.create', compact('countries','pincodes'));

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
        $this->validate(
            $request, [
                'branch_type' => 'required',
                'branch_name' => 'required',
                'code' => 'required|unique:branches,code|unique:franchisees,code,NULL,id,deleted_at,NULL',
                'email' => 'nullable|email|max:255|unique:branches,email,NULL,id,deleted_at,NULL',
                'mobile_number' => 'required|digits:10|regex:/^[0-9]/|unique:branches',
                'pincode_id' => 'required',
                'service_pincode_id' => 'required|array|min:1',
                'incharge_name' => 'nullable|regex:/^[\pL\s\-]+$/u',
                'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'phone_number' => 'nullable|regex:/^[0-9 ]+$/'
                ]
        );


        $branch = Branch::create($request->all());
        $branch_type = $request->get('branch_type');
        $serviceablePin =  $request->get('service_pincode_id');

        if($serviceablePin){
            foreach($serviceablePin as $key => $pincodeid){
                ServiceablePin::create(
                        [
                         'office_type' => $branch_type,
                         'office_id' => $branch->id,
                         'pincode_id' => $pincodeid
                        ]
                );
            }
        }
        return redirect()->route('branches.index')->with('success', 'Branch has been added successfully');


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
        $branch = Branch::get()->where('id', $id)->first();
        $pincodes = Pincode::get();
        $countries = Country::all();

        return view('branches.create',compact('branch','pincodes', 'countries'));
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
        $this->validate(
            $request, [
                'branch_type' => 'required',
                'branch_name' => 'required',
                'code' => 'required|unique:franchisees,code,NULL,id,deleted_at,NULL|unique:branches,code,'.$id.',id,deleted_at,NULL',
                'email' => 'nullable|email|max:255|unique:branches,email,'.$id.',id,deleted_at,NULL',
                'mobile_number' => 'required|digits:10|regex:/^[0-9]/',
                'pincode_id' => 'required',
                'service_pincode_id' => 'required|array|min:1',
                'incharge_name' => 'nullable|regex:/^[\pL\s\-]+$/u',
                'city' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
                'phone_number' => 'nullable|regex:/^[0-9 ]+$/'
                ]
        );

        $branch_type = $request->get('branch_type');
        $branch = Branch::find($id);
        // echo '<pre>';
        // print_r($branch);
        // exit;
        if($branch){
         //  $consignments = Consignment::where('');
           Consignment::where('office_type', '=', $branch->branch_type)
           ->where('office_id', '=', $branch->id)
           ->update(array('office_type' => $branch_type));
        }
        // $branch = Branch::find($id);
        $branch->fill($request->all());
        $branch->save();


         $branch->serviceablePins()->delete();

         if ($request->get('service_pincode_id')) {
             $serviceable_pins = array_unique($request->get('service_pincode_id'));

             foreach ($serviceable_pins as $pincode_id) {
                 $branch->serviceablePins()->create([
                     'office_type' => $branch_type  ,
                     'pincode_id' => $pincode_id
                 ]);
             }
         }
         ///Session::flash('message', 'Branch has been updated successfully!');
         return redirect()->route('branches.index')->with('success', 'Branch has been updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Branch $branch)
    {
                $relationships = array('franchisees');


                $branch = Branch::find($branch->id);
                $should_delete = true;
                foreach($relationships as $r) {
                    if ($branch->$r->isNotEmpty()) {
                        $should_delete = false;
                        return redirect()->route('branches.edit',$branch->id)->with('failed','This Branch has some Franchisee, So can not be deleted!');
                        break;
                    }
                }

                if ($should_delete == true) {
                    $branch->delete();
                    return redirect()->route('branches.index')->with('success','Branch has been deleted Successfully!');
                }

    }

    // this is for autocomplete serviceable pincodes
    /*
     * todo: @janagiraman, Replace the foreach loops with the alias
     */
    public function find(Request $request)
    {
        ///$dd = $_REQUEST[]
        $term = trim($request->q);

        if (empty($term)) {
            return \Response::json([]);
        }

        $pins = Pincode::where('serviceable','=','1')->where('pincode', 'LIKE', "$term%")->get();

        $serviceable_pins = [];


        foreach ($pins as $pin) {
            $serviceable_pins[] = ['id' => $pin->id, 'text' => $pin->pincode];
        }

        return \Response::json($serviceable_pins);
    }


    public function serviceableBranches(Request $request) {
        $this->validate($request, [
            'pinId' => 'required'
        ]);

        $pinId = $request->input('pinId');
        $servBranchIds = ServiceablePin::where('pincode_id', $pinId)->whereIn('office_type', ['BR', 'HO'])->pluck('office_id');


//        print_r($servBranchIds);
        $branches = Branch::select(['id', 'code', 'branch_name as name'])->whereIn('id', $servBranchIds)->get();

        return response()->json($branches);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * @uses consignments
     */
    public function officeList(Request $request) {
        $this->validate($request, [
            'officeType' => 'required|in:HO,BR,FR',
            'term' => 'required'
        ]);
        $officeType = $request->input('officeType');
        $term = $request->input('term');

        if ($officeType == 'FR') {
            $office = Franchisee::select(['id', 'code as text'])->where('code', 'LIKE', "$term%")->get();
        } else {
            $office = Branch::select(['id', 'code as text'])->where('branch_type', '=', $officeType)->where('code', 'LIKE', "$term%")->get();
        }

        return response()->json($office);
    }
}
