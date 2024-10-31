<?php

namespace App\Http\Controllers\Admin;

use App\Booking;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) {
        $this->validate($request, [
            'month_year' => 'nullable'
        ]);
        if ($request->input('month_year')) {
            $date = Carbon::createFromFormat('m/Y', $request->input('month_year'));
            $month = $date->month;
            $year = $date->year;
        } else {
            $month = Carbon::now()->month;
            $year = Carbon::now()->year;
        }
        $type = $request->input('type');

        $bookings = Booking::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->when($type == 'branch', function ($q) {
                $q->whereIn('origin_office_type', ['HO', 'BR'])
                    ->selectRaw('count(*) as total, origin_office_id')
                    ->groupBy('origin_office_id')
                    ->orderBy('total', 'DESC')
                    ->with('bookingBranch:id,code,branch_name');
            })
            ->when($type == 'partner', function ($q) {
                $q->where('origin_office_type', 'FR')
                    ->selectRaw('count(*) as total, origin_office_id')
                    ->groupBy('origin_office_id')
                    ->orderBy('total', 'DESC')
                    ->with('bookingFranchisee:id,code,enterprise_name');
            })
            ->when($type == 'customer', function ($q) {
                $q->selectRaw('count(*) as total, customer_id, customer_name')
                    ->with('customer:id,code')
                    ->orderBy('total', 'DESC')
                    ->groupBy('customer_id');
            })
            ->paginate(10);

        return view('overview', compact('bookings'));
    }
}
