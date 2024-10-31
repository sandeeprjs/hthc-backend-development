<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Pricing;
use App\Subscription;
use Illuminate\Http\Request;

class PricingController extends Controller
{
    public function __construct()
    {
         $this->middleware(['auth', 'role'])->except(['pricingDetails']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $pricings = Pricing::latest()->paginate(10);

        return view('pricing.index', compact('pricings'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('pricing.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        //
        $this->validate($request, [
            'from_weight_kgs' => 'required|numeric',
            'to_weight_kgs' => 'required|numeric',
            'price' => 'required|numeric',
            'addl_weight' => 'required_with:addl_price|nullable|numeric',
            'addl_price' => 'required_with:addl_weight|nullable|numeric',
            'consg_type' => 'required',
            'remarks' => 'nullable',
        ]);

        Pricing::create($request->all());

        return redirect()->route('pricing.index')->with('success', 'subscription created successfully.');
    }


    /**
     * Display the specified resource.
     *
     * @param \App\Pricing $pricing
     * @return \Illuminate\Http\Response
     */
    public function show(Pricing $pricing)
    {
        //
        return view('pricing.show', compact('pricing'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Pricing $pricing
     * @return \Illuminate\Http\Response
     */
    public function edit(Pricing $pricing)
    {
        //
        return view('pricing.edit', compact('pricing'));

    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Pricing $pricing
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pricing $pricing)
    {
        //
        $request->validate([
            'from_weight_kgs' => 'required|numeric',
            'to_weight_kgs' => 'required|numeric',
            'price' => 'required|numeric',
            'addl_weight' => 'nullable|numeric',
            'addl_price' => 'nullable|numeric',
            'consg_type' => 'required',
            'remarks' => 'nullable',
        ]);

        $pricing->update($request->all());

        return redirect()->route('pricing.index')
            ->with('success', 'subscription updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Pricing $pricing
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pricing = Pricing::find($id);

        $pricing->delete();

        return redirect()->route('pricing.index')->with('success', 'subscription deleted successfully');
    }

    public function pricingDetails(Request $request) {
        $this->validate($request, [
            'weight' => 'nullable',
            'subId' => 'nullable',
            'consgType' => 'nullable'
        ]);

        $weight = $request->input('weight');
        $docType = $request->input('consgType');
        $subscription = Subscription::where('id', $request->input('subId'))->first();

        $pricing = Pricing::select(['from_weight_kgs', 'to_weight_kgs', 'price', 'addl_weight', 'addl_price'])->orWhere('consg_type', $docType)->orWhere(function ($q) use ($weight) {
            $q->where('from_weight_kgs', '<=', $weight);
            $q->where('to_weight_kgs', '>=', $weight);
        })->first();

        $extraWeight = $weight - $pricing->to_weight_kgs;
        if ($extraWeight < 0) {
            $totalPrice = $pricing->price + $subscription->price;
        } elseif ($pricing->addl_weight) {
            $extWeightMultiple = ceil($extraWeight / $pricing->addl_weight);
            $addPrice = $pricing->addl_price * $extWeightMultiple;
            $totalPrice = $pricing->price + $subscription->price + $addPrice;
        }

        return response()->json([
            'pricing' => $pricing,
            'weights' => $extraWeight,
            'totalPrice' => $totalPrice ?? ''
        ], 200);
    }
}
