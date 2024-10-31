<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ShipmentReportExport;
use App\Exports\SalesByPartnerReportExport;
use Carbon\Carbon;
use App\Booking;
use App\Delivery;
use App\Subscription;
use App\Customer;
use App\Franchisee;
use App\User;

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function shipmentReport(Request $request) 
    {
        //
       
        $user = auth()->user();
        //$bookings = Booking::get();

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $customerId = $request->input('customer_id');
        $subscriptionId = $request->input('subscription_id');
        $status = $request->input('status');
        // $frCode = $request->input('fr_id');
        $consg_type =  $request->input('consg_type');

        

        $fetch = null;
        if($startDate =='' && $customerId == '' && $subscriptionId == '' && $status == '' && $consg_type == ''){
             $fetch = 1;
        }
        if($request->get('btnSubmit') == 'export' && $fetch == null) {
            return Excel::download(new ShipmentReportExport($consg_type,$customerId,$startDate,$endDate,$status,$subscriptionId), 'ShipmentReport.xlsx');
        }

        $subscriptions = Subscription::select(['id', 'name'])->get();
        $bookingStatuses = Booking::distinct('status')->pluck('status');
        $customer = null;
        if($customerId){
            $customer = Customer::select(['id', 'code'])->where('id', $customerId)->first();
        }
       
        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        }
        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }
        $bookings=null;
        if($request->get('btnSubmit') == 'fetch' && $fetch == null) {
          
            $bookings = Booking::with('delivery','delivery.user')->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($customerId, function ($q) use ($customerId) {
                $q->where('customer_id', $customerId);
            })
            ->when($subscriptionId, function ($q) use ($subscriptionId) {
                $q->where('subscription_id', $subscriptionId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            // ->when($frCode, function ($q) use ($frCode) {
            //     $q->where('origin_office_type', 'FR')->where('origin_office_id', $frCode);
            // })
            ->when($consg_type, function ($q) use ($consg_type) {
                $q->where('consg_type', $consg_type);
            })
           ->latest('id')->paginate(10);
        }    
      ///  $bookings = Booking::paginate(10);
      // dd($bookings);
        return view('reports.shipmentGenerate',compact('bookings','bookingStatuses','subscriptions','customer'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function salesByPartnerReport(Request $request)
    {
        //
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $subscriptionId = $request->input('subscription_id');
        $status = $request->input('status');
        $frCode = $request->input('fr_id');
        $consg_type =  $request->input('consg_type');
        $fetch = null;
        if($startDate =='' && $subscriptionId == '' && $status == '' && $frCode == '' && $consg_type == ''){
             $fetch = 1;
        }
        if($request->get('btnSubmit') == 'export' && $fetch == null) {
            return Excel::download(new SalesByPartnerReportExport($consg_type,$frCode,$startDate,$endDate,$status), 'SalesByPartnerReport.xlsx');
        }

        $subscriptions = Subscription::select(['id', 'name'])->get();
        $bookingStatuses = Booking::distinct('status')->pluck('status');
        // if($customerId){
        //     $customer = Customer::select(['id', 'code'])->where('id', $customerId)->first();
        // }
        $franchisee = null;
        if($frCode){
            $franchisee = Franchisee::select(['id', 'code'])->where('id', $frCode)->first();
        }
       
        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        }
        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }
        $bookings=null;

        if($request->get('btnSubmit') == 'fetch' && $fetch == null) {
            $bookings = Booking::when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->when($subscriptionId, function ($q) use ($subscriptionId) {
                $q->where('subscription_id', $subscriptionId);
            })
            ->when($status, function ($q) use ($status) {
                $q->where('status', $status);
            })
            ->when($frCode, function ($q) use ($frCode) {
                $q->where('origin_office_type', 'FR')->where('origin_office_id', $frCode);
            })
            ->when($consg_type, function ($q) use ($consg_type) {
                $q->where('consg_type', $consg_type);
            })
            ->latest('id')->paginate(10);
        }   
        
        return view('reports.salesByPartner',compact('bookings','bookingStatuses','subscriptions','franchisee'));
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
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
