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
        $this->middleware('auth')->except(['welcome']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function welcome()
    {
        return view('welcome');
    }

    public function index()
    {
        $user = auth()->user();

        // Get today's date
        $today = Carbon::today()->toDateString();

        // Get the current month and year
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Get bookings data for weekly chart (last 8 weeks)
        $startDate = Carbon::now()->subWeeks(7)->startOfWeek();
        $endDate = Carbon::now()->endOfWeek();

        $bookingsByWeek = Booking::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('count(*) as total, YEARWEEK(created_at) as week_number,
                         DATE_FORMAT(min(created_at), "%b %d") as start_date,
                         DATE_FORMAT(max(created_at), "%b %d") as end_date')
            ->groupBy('week_number')
            ->orderBy('week_number', 'ASC')
            ->get();

        // Prepare data for weekly chart
        $weekLabels = $bookingsByWeek->map(function ($item) {
            return $item->start_date . ' - ' . $item->end_date;
        })->toArray();

        $weekData = $bookingsByWeek->pluck('total')->toArray();

        // Ensure we have at least some data to display
        if (empty($weekLabels)) {
            $weekLabels = ['No data'];
            $weekData = [0];
        }

        // Create weekly bookings chart
        $bookingByDateChart = new BookingCharts();
        $bookingByDateChart->labels($weekLabels);
        $bookingByDateChart->dataset('Bookings', 'line', $weekData)
            ->color('#6C757D')
            ->backgroundColor('rgba(108, 117, 125, 0.2)')
            ->lineTension(0.3);

        // Get today's stats
        $bookingsToday = Booking::whereDate('created_at', $today)->count() ?: 0;
        $totalTransit = Booking::where('status', 'in transit')->whereDate('updated_at', $today)->count() ?: 0;
        $totalDelivered = Booking::where('status', 'delivered')->whereDate('updated_at', $today)->count() ?: 0;
        $returnedCancel = Booking::whereIn('status', ['returned', 'cancel'])->whereDate('updated_at', $today)->count() ?: 0;

        // Get top 5 customers for current month
        $topCustomer = Booking::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('count(*) as total, customer_id, customer_name')
            ->groupBy('customer_id', 'customer_name')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->pluck('total', 'customer_name');

        // Handle empty data scenario for customers
        if ($topCustomer->isEmpty()) {
            $topCustomer = collect(['No Data' => 0]);
        }

        // Create customer chart
        $customerChart = new BookingCharts();
        $customerChart->labels($topCustomer->keys())
            ->height(200)
            ->width(200);
        $customerChart->dataset('Top Customers', 'doughnut', $topCustomer->values())
            ->backgroundColor(collect(AppHelper::generateRandomColors($topCustomer->count())));
        $customerChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => ['position' => 'bottom', 'labels' => ['padding' => 10, 'fontSize' => 11]],
            'tooltips' => ['enabled' => true]
        ]);

        // Get top 5 branches for current month
        $topBranches = Booking::whereIn('origin_office_type', ['HO', 'BR'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('count(*) as total, origin_office_id')
            ->groupBy('origin_office_id')
            ->with('bookingBranch:id,code,branch_name')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        // Handle empty data scenario for branches
        if ($topBranches->isEmpty()) {
            $branchChart = new BookingCharts();
            $branchChart->labels(['No Data'])
                ->height(200)
                ->width(200);
            $branchChart->dataset('No Data Available', 'doughnut', [1])
                ->backgroundColor(['#f8f9fa']);
        } else {
            $bookingBranches = $topBranches->map(function($branch) {
                return $branch->bookingBranch ? $branch->bookingBranch->code : 'Unknown';
            })->toArray();

            $totalBranches = $topBranches->pluck('total')->toArray();

            $branchChart = new BookingCharts();
            $branchChart->labels($bookingBranches)
                ->height(200)
                ->width(200);
            $branchChart->dataset('Top Branches', 'doughnut', $totalBranches)
                ->backgroundColor(collect(AppHelper::generateRandomColors($topBranches->count())));
        }

        $branchChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => ['position' => 'bottom', 'labels' => ['padding' => 10, 'fontSize' => 11]],
            'tooltips' => ['enabled' => true]
        ]);

        // Get top 5 partners for current month
        $topPartners = Booking::where('origin_office_type', 'FR')
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('count(*) as total, origin_office_id')
            ->groupBy('origin_office_id')
            ->with('bookingFranchisee:id,code,enterprise_name')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        // Handle empty data scenario for partners
        if ($topPartners->isEmpty()) {
            $partnerChart = new BookingCharts();
            $partnerChart->labels(['No Data'])
                ->height(200)
                ->width(200);
            $partnerChart->dataset('No Data Available', 'doughnut', [1])
                ->backgroundColor(['#f8f9fa']);
        } else {
            $bookingPartners = $topPartners->map(function($partner) {
                return $partner->bookingFranchisee ? $partner->bookingFranchisee->code : 'Unknown';
            })->toArray();

            $totalPartners = $topPartners->pluck('total')->toArray();

            $partnerChart = new BookingCharts();
            $partnerChart->labels($bookingPartners)
                ->height(200)
                ->width(200);
            $partnerChart->dataset('Top Partners', 'doughnut', $totalPartners)
                ->backgroundColor(collect(AppHelper::generateRandomColors($topPartners->count())));
        }

        $partnerChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => ['position' => 'bottom', 'labels' => ['padding' => 10, 'fontSize' => 11]],
            'tooltips' => ['enabled' => true]
        ]);

        // Get top 5 subscription plans
        $topSubscriptions = Booking::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('count(*) as total, subscription_id')
            ->groupBy('subscription_id')
            ->orderBy('total', 'DESC')
            ->limit(5)
            ->get();

        // Handle empty data scenario for subscriptions
        if ($topSubscriptions->isEmpty()) {
            $subscriptionChart = new BookingCharts();
            $subscriptionChart->labels(['No Data']);
            $subscriptionChart->dataset('No Data Available', 'bar', [0])
                ->backgroundColor(['#f8f9fa']);
        } else {
            // Use subscription name if available, otherwise use IDs
            $subscriptionsList = $topSubscriptions->map(function($sub) {
                return $sub->subscription_id ? 'Plan ' . $sub->subscription_id : 'Unknown';
            })->toArray();

            $subscriptionCount = $topSubscriptions->pluck('total')->toArray();

            $subscriptionChart = new BookingCharts();
            $subscriptionChart->labels($subscriptionsList);
            $subscriptionChart->dataset('Top Plans', 'bar', $subscriptionCount)
                ->backgroundColor(collect(AppHelper::generateRandomColors($topSubscriptions->count())));
        }

        $subscriptionChart->options([
            'responsive' => true,
            'maintainAspectRatio' => false,
            'legend' => ['display' => false],
            'scales' => [
                'yAxes' => [['ticks' => ['beginAtZero' => true]]],
                'xAxes' => [['ticks' => ['autoSkip' => false]]]
            ]
        ]);

        return view('home', [
            'user' => $user,
            'bookingsToday' => $bookingsToday, // Use the variable you calculated earlier
            'totalTransit' => $totalTransit,
            'totalDelivered' => $totalDelivered,
            'returnedCancel' => $returnedCancel,
            'branchChart' => $branchChart,
            'partnerChart' => $partnerChart,
            'customerChart' => $customerChart,
            'bookingByDateChart' => $bookingByDateChart,
            'subscriptionChart' => $subscriptionChart,
        ]);

    }
}
