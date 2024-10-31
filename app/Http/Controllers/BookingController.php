<?php

namespace App\Http\Controllers;

use App\Franchisee;
use App\Branch;
use App\Booking;
use App\Delivery;
use App\Customer;
use App\Manifest;
use App\Subscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $batchId)
    {
        if (!is_numeric($batchId)) {
            abort(404, 'Invalid batch ID');
        }

        $encryptionKey = env('ENC_KEY');
        if (!$encryptionKey) {
            abort(500, 'Encryption key not set');
        }

        $batchId = intdiv($batchId, $encryptionKey);
        $query = Booking::where('batch_id', $batchId);

        // Apply filters
        if ($request->filled('consg_number')) {
            $query->where('consg_number', $request->consg_number);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            try {
                $startDate = Carbon::createFromFormat('d/m/Y', $request->start_date)->subDay()->format('Y-m-d');
                $endDate = Carbon::createFromFormat('d/m/Y', $request->end_date)->addDay()->format('Y-m-d');
                $query->whereBetween('created_at', [$startDate, $endDate]);
            } catch (\Exception $e) {
                return back()->withErrors(['date' => 'Invalid date format.']);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->orderBy('updated_at', 'desc')->paginate(10);

        return view('bookings.track', [
            'bookings' => $bookings,
            'subscriptions' => Subscription::select(['id', 'name'])->get(),
            'customer' => $request->filled('customer_id') ? Customer::find($request->customer_id) : null,
            'bookingStatuses' => Booking::distinct('status')->pluck('status'),
        ]);
    }

    public function create()
    {
        return view('bookings.create');
    }

    public function tracking(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'consg_number' => 'nullable|exists:App\Booking,consg_number'
        ]);

        if ($validator->fails()) {
            return redirect(route('public.tracking'))
                ->withErrors($validator)
                ->withInput();
        }

        $consg_number = $request->input('consg_number');
        $tracking = $booking = $bookingOffice = $delivery = null;

        if ($consg_number) {
            $booking = Booking::where('consg_number', $consg_number)->first();

            if ($booking) {
                $bookingOffice = $this->getOfficeDetails($booking->origin_office_type, $booking->origin_office_id);
                $delivery = Delivery::where('booking_id', '=', $booking->id)->first();
                $tracking = Manifest::where('manifest_number', '=', $consg_number)
                    ->where('customer_view', '=', 1)->get();
            } else {
                abort(404, 'Booking not found');
            }
        }

        return view('tracking.show', compact('tracking', 'booking', 'bookingOffice', 'delivery'));
    }


    // public function shipperCopy(Request $request){
    //     echo $consg_number = $_REQUEST['c'];

    // }

    /**
     * @param $eCode
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function acknowledgement($eCode, Request $request)
    {

        $batchId = null;
        $consg_number = null;

        $firstChar = substr($eCode, 0, 1);
        $decCode = substr($eCode, 2);

        if ($firstChar == 's' && is_numeric($decCode)) {
             $batchId = intdiv($decCode, env('ENC_KEY'));

        } elseif ($firstChar == 's' && !is_numeric($decCode)) {
            $consg_number = $decCode;
        } elseif ($firstChar == 'r') {
            $consg_number = $decCode;
        } else {
            abort(404);
        }



        $bookings = Booking::when($firstChar == 's' && is_numeric($decCode), function ($q) use ($batchId) {
            $q->where('batch_id', $batchId);
        })->when($firstChar == 's' && !is_numeric($decCode), function ($q) use ($consg_number) {
            $q->where('consg_number', $consg_number);
        })->when($firstChar == 'r', function ($q) use ($consg_number) {
            $q->where('consg_number', $consg_number);
        })->with('delivery', 'office', 'delivery.receiverImageUrl', 'delivery.receiverSignUrl')->get();
        return view('bookings.acknowledgement', compact('bookings'));
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
