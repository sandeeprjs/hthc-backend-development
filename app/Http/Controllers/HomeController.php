<?php

namespace App\Http\Controllers;

use App\Booking;
use App\Http\Helpers\AppHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Charts\BookingCharts;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth')->except(['welcome']);;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome(){ 
        return view('welcome');
    }


    public function index()
    {
        ///echo 'COMSNOSOS';
        $user = auth()->user();
        // if(!$user->isAdmin()){
        //     return view('welcome');
        // }
        $bookings = Booking::selectRaw('count(*) as total, DATE_FORMAT(created_at, "%u") as week')
            ->orderByRaw('WEEK(created_at) ASC')
            ->groupBy('week')
            ->get('total', 'week');

        $today = Carbon::today()->toDateString();
        $totalBookings = Booking::count();
        $totalTransit = Booking::where('status', 'in transit')->whereDate('updated_at', $today)->count();
        $totalDelivered = Booking::where('status', 'delivered')->whereDate('updated_at', $today)->count();
        $bookingsToday = Booking::whereDate('created_at', '=', $today)->count();
        $returnedCancel = Booking::whereIn('status', ['returned', 'cancel'])->whereDate('updated_at', $today)->count();

        $total = array_column($bookings->toArray(), 'total');
        $date = array_column($bookings->toArray(), 'week');
        $bookingByDateChart = new BookingCharts();
        $bookingByDateChart->labels(AppHelper::getStartEndDateFromWeek($date));
        $bookingByDateChart->loaderColor('blue');
        $bookingByDateChart->dataset('Booking Weekly', 'line', $total)
                    ->color('black');

        $topCustomer = Booking::whereMonth('created_at', Carbon::now()->month)->selectRaw('count(*) as total, customer_id, customer_name')
            ->groupBy('customer_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->pluck('total', 'customer_name');

        $customerChart = new BookingCharts();
        $customerChart->labels($topCustomer->keys())
                    ->height(200)
                    ->width(200);
        $customerChart->dataset('Top Customers', 'doughnut', $topCustomer->values())
                ->backgroundColor(collect(AppHelper::generateRandomColors($topCustomer->count())));
        $customerChart->minimalist(true);
        $customerChart->options(
            [
                'scales' => [
                    'xAxes' => [ [ 'display' => false, ], ], 'yAxes' => [ [ 'display' => false, ], ],
                ],
                'legends' => [
                    'display' => false
                ],

            ]
        );

        $topBranches = Booking::whereIn('origin_office_type', ['HO', 'BR'])->whereMonth('created_at', Carbon::now()->month)->selectRaw('count(*) as total, origin_office_id')
            ->groupBy('origin_office_id')
            ->with('bookingBranch:id,code')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get('total', 'origin_office_id');

        $bookingBranches = array_column(array_column($topBranches->toArray(), 'booking_branch'), 'code');
        $totalBranches = array_column($topBranches->toArray(), 'total');

        $branchChart = new BookingCharts();
        $branchChart->labels($bookingBranches)
                    ->displayAxes(false)
                    ->displayLegend(false)
                    ->height(200)
                    ->width(200);
        $branchChart->dataset('Top Branches', 'doughnut', $totalBranches)
                    ->backgroundColor(collect(AppHelper::generateRandomColors($topBranches->count())));

        $topPartners = Booking::where('origin_office_type', 'FR')->whereMonth('created_at', Carbon::now()->month)->selectRaw('count(*) as total, origin_office_id')
            ->groupBy('origin_office_id')
            ->with('bookingFranchisee:id,code')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get('total', 'origin_office_id');

        $bookingPartners = array_column(array_column($topPartners->toArray(), 'booking_franchisee'), 'code');
        $totalPartners = array_column($topPartners->toArray(), 'total');

        $partnerChart = new BookingCharts();
        $partnerChart->labels($bookingPartners)
                    ->height(200)
                    ->width(200);
        $partnerChart->dataset('Top Partners', 'doughnut', $totalPartners)
            ->backgroundColor(collect(AppHelper::generateRandomColors($topPartners->count())));
        $partnerChart->options(
            [ 'scales' =>
                [ 'xAxes' => [ [ 'display' => false, ], ], 'yAxes' => [ [ 'display' => false, ], ], ],
            ]
        );
        $partnerChart->minimalist(true);

        $topSubscriptions = Booking::selectRaw('count(*) as total, subscription_id')
            ->groupBy('subscription_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get('total', 'subscription_id');

        $subscriptionsList = array_column($topSubscriptions->toArray(), 'subs_name');
        $subscriptionCount = array_column($topSubscriptions->toArray(), 'total');

        $subscriptionChart = new BookingCharts();
        $subscriptionChart->labels($subscriptionsList)
                        ->dataset('Top Subscription', 'bar', $subscriptionCount)
                        ->backgroundColor(collect(AppHelper::generateRandomColors($topSubscriptions->count())));

       return view('home', compact(['bookingByDateChart', 'customerChart', 'branchChart', 'partnerChart', 'subscriptionChart', 'totalBookings', 'totalTransit', 'totalDelivered', 'bookingsToday', 'returnedCancel']));
       
    }
}
