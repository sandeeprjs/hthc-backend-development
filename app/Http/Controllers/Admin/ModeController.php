<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mode;
use Illuminate\Http\Request;

class ModeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $modes = Mode::latest()->paginate(10);

        return view('modes.index', compact('modes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('modes.create');

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
            'code' => 'required',
            'name' => 'required',
            'type' => '',
            'description' => '',
        ]);

        Mode::create($request->all());

        return redirect()->route('modes.index')
            ->with('success', 'mode is created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function show(Mode $mode)
    {
        //
        return view('modes.show', compact('mode'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function edit(Mode $mode)
    {
        //
        return view('modes.edit', compact('mode'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mode $mode)
    {
        //
        $request->validate([
            'code' => 'required',
            'name' => 'required',
            'type' => '',
            'description' => '',
        ]);

        $mode->update($request->all());

        return redirect()->route('modes.index')
            ->with('success', 'mode is updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mode  $mode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mode $mode)
    {
        //
        $mode->delete();

        return redirect()->route('modes.index')
            ->with('success', 'mode is deleted successfully');
    }
}
