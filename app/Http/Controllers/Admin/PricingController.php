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
            'weight' => 'nullable|numeric',
            'subId' => 'nullable|numeric',
            'consgType' => 'nullable|string'
        ]);

        $weight = $request->input('weight');
        $docType = $request->input('consgType');
        $subscription = Subscription::where('id', $request->input('subId'))->first();

        // Fix: Use proper where conditions
        $pricing = Pricing::where('consg_type', $docType)
            ->where('from_weight_kgs', '<=', $weight)
            ->where('to_weight_kgs', '>=', $weight)
            ->select(['from_weight_kgs', 'to_weight_kgs', 'price', 'addl_weight', 'addl_price'])
            ->first();

        // Fix: If no pricing slab is found, use the lowest available slab
        if (!$pricing) {
            $pricing = Pricing::orderBy('from_weight_kgs', 'asc')->first();
        }

        // Fix: Handle small weight scenarios properly
        $extraWeight = max(0, $weight - $pricing->to_weight_kgs);
        if ($extraWeight == 0) {
            $totalPrice = $pricing->price + ($subscription->price ?? 0);
        } elseif ($pricing->addl_weight && $pricing->addl_price) {
            $extWeightMultiple = ceil($extraWeight / $pricing->addl_weight);
            $addPrice = $pricing->addl_price * $extWeightMultiple;
            $totalPrice = $pricing->price + ($subscription->price ?? 0) + $addPrice;
        } else {
            $totalPrice = $pricing->price + ($subscription->price ?? 0);
        }

        return response()->json([
            'pricing' => $pricing,
            'weights' => $extraWeight,
            'totalPrice' => $totalPrice
        ], 200);
    }

}
