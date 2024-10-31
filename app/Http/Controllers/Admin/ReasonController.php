<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Reason;
use Illuminate\Http\Request;

class ReasonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reasons = Reason::orderBy('updated_at', 'DESC')->paginate(10);

        return view('reasons.index', compact('reasons'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('reasons.create');
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
           // 'code' => 'required',
            'name' => 'required',
            'type' => 'required'
            ]);

        if($request->type == 'return'){
            $code = 'R'.random_int(100, 999);
        } 
        if($request->type == 'cancel'){
              $code = 'C'.random_int(100, 999);
        } 
 
        $reason = new Reason();
        $reason->code = $code;
        $reason->name = $request->input('name');
        $reason->type = $request->input('type');
        $reason->save();

        return redirect( route('reasons.index'))->withSuccess('Reason added successfully!');
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
        $reason = Reason::find($id);

        return view('reasons.edit', compact('reason'));
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
        $this->validate($request, [
           // 'code' => 'required',
            'name' => 'required',
            'type' => 'required'
        ]);

        $reason = Reason::find($id);
       // $reason->code = $request->input('code');
        $reason->name = $request->input('name');
        $reason->type = $request->input('type');
        $reason->save();

        return redirect( route('reasons.index'))->withSuccess('Reason updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $reason = Reason::find($id);

        if (!$reason) {
            echo 'not found';
        }

        $reason->delete();

        return redirect(route('reasons.index'))->withSuccess('Reason deleted successfully!');
    }
}
