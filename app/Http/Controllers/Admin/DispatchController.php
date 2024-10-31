<?php

namespace App\Http\Controllers\Admin;

use App\Dispatch;
use App\Http\Controllers\Controller;
use App\Mode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Branch;

class DispatchController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role']);
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $dispatches = Dispatch::latest()->paginate(10);
        $modes = Mode::all();

        return view('dispatches.index', compact('dispatches','modes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $modes = Mode::all();
        $franchisees = DB::table("franchisees")->select("franchisees.id","franchisees.code");

        $branches = DB::table("branches")->select("branches.id" ,"branches.code")
            ->union($franchisees)
            ->get();

        return view('dispatches.create',compact('branches', 'modes'));
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
        $request->validate([
            'dest_office_id' => 'required',
            'vehicle_number' => '',
            'mode_id' => '',
            'consg_number' => 'required',
            'status' => '',
        ]);

        Dispatch::create($request->all());

        return redirect()->route('dispatches.index')
            ->with('success', 'dispatch created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function show(Dispatch $dispatch)
    {
        //
        return view('dispatches.show', compact('dispatch'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function edit(Dispatch $dispatch)
    {
        //
        $modes = Mode::all();
        return view('dispatches.edit', compact('dispatch', 'modes'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Dispatch $dispatch)
    {
        //

        $request->validate([
            'dest_office_id' => 'required',
            'vehicle_number' => '',
            'mode_id' => '',
            'consg_number' => 'required',
            'status' => '',
        ]);

        $dispatch->update($request->all());

        return redirect()->route('dispatches.index')
            ->with('success', 'dispatches updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Dispatch  $dispatch
     * @return \Illuminate\Http\Response
     */
    public function destroy(Dispatch $dispatch)
    {
        //
        $dispatch->delete();

        return redirect()->route('dispatches.index')
            ->with('success', 'dispatch deleted successfully');
    }
}
