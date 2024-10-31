<?php

namespace App\Http\Controllers\Admin;

use App\Country;
use App\Http\Controllers\Controller;
use App\Http\Helpers\AppHelper;
use App\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PincodeController extends Controller
{
    public function __construct() {
        return $this->middleware(['auth', 'role'])->except(['pincodeDetails']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'pincode' => 'nullable',
            'area' => 'nullable',
            'city' => 'nullable',
        ]);

        $pin = $request->input('pincode');
        $area = $request->input('area');
        $city = $request->input('city');

        $pincodes = Pincode::when($pin, function ($q) use ($pin){
                return $q->where('pincode', 'LIKE', "%$pin%");
        })->when($area, function ($q) use ($area){
            return $q->where('area_name', 'LIKE', "%$area%");
        })->when($city, function ($q) use ($city){
            return $q->where('city', 'LIKE', "%$city%");
        })->orderBy('updated_at', 'desc')->paginate(10);

        return view('pincodes.index', compact('pincodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $countryList = AppHelper::countriesOptionList();

        return view('pincodes.create', compact('countryList'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'pincode' => 'required|unique:pincodes,pincode,NULL,id,deleted_at,NULL|regex:/^([0-9\s\(\)]*)$/|min:6|max:6',
            'city' => 'required|regex:/^[a-zA-Z ]+$/',
            'district' => 'nullable|regex:/^[a-zA-Z ]+$/',
            'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
        ]);

        $pincode = new Pincode();
        $pincode->pincode = $request->input('pincode');
        $pincode->area_name = $request->input('area_name');
        $pincode->city = $request->input('city');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->country_id = $request->input('country');
        $pincode->serviceable = $request->input('serviceable');
        $pincode->save();

        return redirect(route('pincodes.index'))->withSuccess('Pincode added successfully!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Pincode  $pincode
     * @return \Illuminate\Http\Response
     */
    public function show(Pincode $pincode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Pincode  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $pincode = Pincode::find($id);

        if (!$pincode) {
            echo 'not found'; //change with 404 page
        }

        $countryId = Country::where('id', $pincode->country_id)->pluck('id');
        $countryList = AppHelper::countriesOptionList($countryId[0]);

        return view('pincodes.edit', compact(['pincode', 'countryList']));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Pincode  $pincode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
         $rules = [
             'city' => 'required|regex:/^[a-zA-Z ]+$/',
             'district' => 'nullable|regex:/^[a-zA-Z ]+$/',
             'state' => 'nullable|regex:/^[a-zA-Z ]+$/',
        ];
        $messages = [
            'required' => 'The :attribute field is required.'
        ];

        $pincode = Pincode::find($id);

        $newPin = $request->input('pincode');
        if($pincode->pincode != $newPin) {
            $rules['pincode'] = 'required|unique:pincodes,pincode,NULL,id,deleted_at,NULL|regex:/^([0-9\s\(\)]*)$/|min:6|max:6';
        }

        Validator::make($request->all(),$rules, $messages)->validate();

        $pincode->pincode = $request->input('pincode');
        $pincode->area_name = $request->input('area_name');
        $pincode->city = $request->input('city');
        $pincode->district = $request->input('district');
        $pincode->state = $request->input('state');
        $pincode->country_id = $request->input('country');
        $pincode->serviceable = $request->input('serviceable');

        $pincode->save();

        return redirect(route('pincodes.index'))->withSuccess('Pincode updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pincode = Pincode::find($id);

        if (!$pincode) {
            return 'not found';
        }

        $pincode->delete();

        return redirect(route('pincodes.index'))->withSuccess('Pincode deleted successfully!');
    }

    /**
     * @param Request $request
     * @return
     */
    public function pincodeDetails(Request $request) {
        $id = $request->input('id');

        $pincode = Pincode::where('id', '=', $id)->first();

        return response()->json($pincode);
    }

     // this is for autocomplete serviceable pincodes
    public function findPincode(Request $request)
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
}
