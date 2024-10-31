<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Subscription;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct()
    {
         $this->middleware(['auth', 'role'])->except('subscriptionList');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subscriptions = Subscription::latest()->paginate(10);

        return view('subscriptions.index', compact('subscriptions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('subscriptions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:subscriptions,name,NULL,id,deleted_at,NULL',
            'consg_type' => 'required',
            'price' => 'required|numeric',
            'max_delivery_time' => 'nullable|numeric',
        ]);


        Subscription::create($request->all());

        return redirect()->route('subscriptions.index')
            ->with('success', 'plan created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param \App\subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function show(Subscription $subscription)
    {
        return view('subscriptions.show', compact('subscription'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function edit(subscription $subscription)
    {
        return view('subscriptions.edit', compact('subscription'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\subscription $subscription
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Subscription $subscription)
    {
        $request->validate([
            'name' => 'required|unique:subscriptions,name,'.$subscription->id.',id,deleted_at,NULL',
            'consg_type' => 'required',
            'price' => 'required|numeric',
            'max_delivery_time' => 'nullable|numeric',
        ]);

        $subscription->update($request->all());

        return redirect()->route('subscriptions.index')
            ->with('success', 'plan updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\subscription $subscription
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Subscription $subscription)
    {
        $subscription->forceDelete();

        return redirect()->route('subscriptions.index')
            ->with('success', 'plan deleted successfully');
    }


    public function subscriptionList(Request $request) {
        $this->validate($request, [
            'docType' => 'required'
        ]);

        $subscription = Subscription::select(['id', 'name'])->where('consg_type', '=', $request->input('docType'))->get();

        if (!$subscription) {
            return response()->json([
                'error' => 'not found'
            ]);
        }

        return response()->json($subscription);
    }
}
