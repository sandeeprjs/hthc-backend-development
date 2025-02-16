<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Manifest;
use App\Booking;
use App\Branch;
use App\Franchisee;
use App\Delivery;

class TrackingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tracking = null;
        $booking  = null;
        $error    = null;
        $bookingOffice = null;
        $delivery = null;
        if($request->consign_number !=""){

            $booking  = Booking::where('consg_number', '=', $request->consign_number )->first();
            if($booking){

                    $delivery  = Delivery::where('booking_id', '=', $booking->id )->first();

                    $bookingOffice =  $this->getOfficeDetails($booking->origin_office_type,$booking->origin_office_id);


                    $tracking = Manifest::where('manifest_number', '=', $request->consign_number)
                                ->where('customer_view', '=', 1)->get();
            }
            if(!$booking){
                $error = "Tracking Number does not exists";
            }

        }

        return view('tracking.form', compact('tracking','booking', 'delivery', 'bookingOffice','error', 'request'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {


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

    public function getOfficeDetails($officeType, $officeId){

        $office = null;
        if($officeType == 'HO' || $officeType == 'BR'){
            $office = Branch::where('id', '=', $officeId)->first();
        }
        if($officeType == 'FR'){
            $office = Franchisee::where('id', '=', $officeId)->first();
        }
        return $office;
    }
}
