<?php

namespace App\Services;

use App\Booking;
use App\Customer;
use App\Exports\BookingsDetailedExport;
use App\Exports\BookingsExport;
use App\Exports\BookingsSummaryExport;
use App\Exports\CustomerSalesExport;
use App\Exports\BranchPerformanceExport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ReportService
{
    /**
     * Default pagination limit
     */
    const PAGINATION_LIMIT = 20;

    /**
     * Cache expiration time in minutes
     */
    const CACHE_EXPIRATION = 60;

    /**
     * Generate report based on request parameters
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function generateReport(Request $request)
    {
        $startDate = null;
        $endDate = null;
        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()->format('Y-m-d H:i:s');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()->format('Y-m-d H:i:s');
        }

        $customerId = $request->input('customer_id');
        $status = $request->input('status');
        $reportType = $request->input('report_type');

        // Generate export based on report type
        switch ($reportType) {
            case 'detailed':
                return Excel::download(
                    new BookingsDetailedExport($startDate, $endDate, $customerId, $status),
                    'detailed_bookings_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
            case 'summary':
                return Excel::download(
                    new BookingsSummaryExport($startDate, $endDate, $customerId, $status),
                    'summary_bookings_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
            default:
                return Excel::download(
                    new BookingsExport($startDate, $endDate, $customerId, $status),
                    'bookings_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
        }
    }

    /**
     * Search bookings with advanced filtering
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function searchBookings(Request $request)
    {
        $user = Auth::user();
        $searchTerm = $request->input('search_term');
        $dateRange = $request->input('date_range');
        $status = $request->input('status');
        $customerId = $request->input('customer_id');
        $perPage = $request->input('per_page', self::PAGINATION_LIMIT);

        $query = Booking::query();

        // Restrict non-admin users to only see bookings from their office
        if (!$user->isAdmin()) {
            $query->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        // Apply search term
        if ($searchTerm) {
            $query->where(function($q) use ($searchTerm) {
                $q->where('consg_number', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('customer_name', 'LIKE', "%{$searchTerm}%")
                    ->orWhereHas('delivery', function($subQuery) use ($searchTerm) {
                        $subQuery->where('receiver_name', 'LIKE', "%{$searchTerm}%")
                            ->orWhere('mobile_number', 'LIKE', "%{$searchTerm}%");
                    });
            });
        }

        // Apply date range
        if ($dateRange) {
            $dates = explode(' - ', $dateRange);
            if (count($dates) == 2) {
                $startDate = Carbon::createFromFormat('d/m/Y', $dates[0])->startOfDay();
                $endDate = Carbon::createFromFormat('d/m/Y', $dates[1])->endOfDay();
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        // Apply status filter
        if ($status) {
            $query->where('status', $status);
        }

        // Apply customer filter
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        // Get results with pagination
        return $query->select([
            'id', 'consg_number', 'customer_id', 'customer_name',
            'subscription_id', 'origin_office_type', 'origin_office_id',
            'status', 'created_at', 'updated_at', 'booked_amount'
        ])
            ->with([
                'subscription:id,name',
                'customer:id,code,customer_name',
                'delivery:id,booking_id,receiver_name,mobile_number'
            ])
            ->latest('id')
            ->paginate($perPage);
    }

    /**
     * Generate invoice PDF for a booking
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateInvoicePdf($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only access bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->with([
            'delivery',
            'subscription',
            'customer',
            'senderPincode',
            'delivery.receiverPincode'
        ])->find($id);

        if (!$booking) {
            return redirect(route('bookings.index'))->withError('Booking not found or you do not have permission to access it.');
        }

        // Generate PDF
        $pdf = PDF::loadView('reports.invoice', compact('booking'));
        return $pdf->download('invoice_' . $booking->consg_number . '.pdf');
    }

    /**
     * Generate delivery receipt PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateDeliveryReceiptPdf($id)
    {
        $user = Auth::user();
        $booking = Booking::query();

        // Restrict non-admin users to only access bookings from their office
        if (!$user->isAdmin()) {
            $booking->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $booking = $booking->with([
            'delivery',
            'delivery.receiverImageUrl',
            'delivery.receiverSignUrl'
        ])->find($id);

        if (!$booking) {
            return redirect(route('bookings.index'))->withError('Booking not found or you do not have permission to access it.');
        }

        // Generate PDF
        $pdf = PDF::loadView('reports.delivery_receipt', compact('booking'));
        return $pdf->download('delivery_receipt_' . $booking->consg_number . '.pdf');
    }

    /**
     * Generate manifest PDF for a batch of bookings
     *
     * @param int $batchId
     * @return \Illuminate\Http\Response
     */
    public function generateManifestPdf($batchId)
    {
        $user = Auth::user();
        $bookings = Booking::query();

        // Restrict non-admin users to only access bookings from their office
        if (!$user->isAdmin()) {
            $bookings->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $bookings = $bookings->where('batch_id', $batchId)
            ->with('delivery')
            ->get();

        if ($bookings->isEmpty()) {
            return redirect(route('bookings.index'))->withError('No bookings found for this batch or you do not have permission to access them.');
        }

        // Generate PDF
        $pdf = PDF::loadView('reports.manifest', compact('bookings', 'batchId'));
        return $pdf->download('manifest_batch_' . $batchId . '.pdf');
    }

    /**
     * Get customer sales report data
     *
     * @param Request $request
     * @return array
     */
    public function getCustomerSalesReport(Request $request)
    {
        $user = Auth::user();

        // Process date inputs
        $startDate = $request->input('start_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $customerId = $request->input('customer_id');

        // Build query with permission check
        $query = Booking::query();

        if (!$user->isAdmin()) {
            $query->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        }

        $query->whereBetween('created_at', [$startDate, $endDate]);

        if ($customerId) {
            $query->where('customer_id', $customerId);
        }

        // Cache the report data
        $cacheKey = 'customer_sales_report_' . md5($user->id . $startDate . $endDate . $customerId);

        $report = Cache::remember($cacheKey, self::CACHE_EXPIRATION, function() use ($query, $customerId) {
            // Get customer details if specific customer is selected
            $customer = null;
            if ($customerId) {
                $customer = Customer::find($customerId);
            }

            // Get sales summary by customer
            $salesByCustomer = $query->select(
                'customer_id',
                'customer_name',
                DB::raw('count(*) as booking_count'),
                DB::raw('sum(booked_amount) as total_amount')
            )
                ->groupBy('customer_id', 'customer_name')
                ->orderByDesc('total_amount')
                ->get();

            // Get sales trend (monthly or daily based on date range)
            $salesTrend = $query->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('count(*) as booking_count'),
                DB::raw('sum(booked_amount) as total_amount')
            )
                ->groupBy('date')
                ->orderBy('date')
                ->get();

            // Get subscription breakdown
            $subscriptionBreakdown = $query->select(
                'subscription_id',
                DB::raw('count(*) as booking_count'),
                DB::raw('sum(booked_amount) as total_amount')
            )
                ->with('subscription:id,name')
                ->groupBy('subscription_id')
                ->get();

            return [
                'customer' => $customer,
                'salesByCustomer' => $salesByCustomer,
                'salesTrend' => $salesTrend,
                'subscriptionBreakdown' => $subscriptionBreakdown,
                'totalBookings' => $salesByCustomer->sum('booking_count'),
                'totalRevenue' => $salesByCustomer->sum('total_amount')
            ];
        });

        return array_merge($report, [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y')
        ]);
    }

    /**
     * Get branch performance report data
     *
     * @param Request $request
     * @return array
     */
    public function getBranchPerformanceReport(Request $request)
    {
        $user = Auth::user();

        // Only admins can access branch performance reports
        if (!$user->isAdmin()) {
            return [
                'error' => 'You do not have permission to access this report.',
                'branches' => [],
                'branchPerformance' => [],
                'totalBookings' => 0,
                'totalRevenue' => 0
            ];
        }

        // Process date inputs
        $startDate = $request->input('start_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay()
            : Carbon::now()->subDays(30)->startOfDay();

        $endDate = $request->input('end_date')
            ? Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay()
            : Carbon::now()->endOfDay();

        $branchId = $request->input('branch_id');

        // Get all branches
        $branches = DB::table('branches')->select('id', 'name', 'code')->get();

        // Build query
        $query = Booking::whereBetween('created_at', [$startDate, $endDate]);

        if ($branchId) {
            $query->where(function($q) use ($branchId) {
                $q->where('origin_office_type', 'BR')
                    ->where('origin_office_id', $branchId);
            });
        }

        // Cache the report data
        $cacheKey = 'branch_performance_report_' . md5($startDate . $endDate . $branchId);

        $report = Cache::remember($cacheKey, self::CACHE_EXPIRATION, function() use ($query, $branches, $branchId) {
            // Get performance by branch
            $branchPerformance = $query->select(
                'origin_office_type',
                'origin_office_id',
                DB::raw('count(*) as booking_count'),
                DB::raw('sum(booked_amount) as total_amount')
            )
                ->where('origin_office_type', 'BR')
                ->groupBy('origin_office_type', 'origin_office_id')
                ->get()
                ->map(function($item) use ($branches) {
                    $branch = $branches->where('id', $item->origin_office_id)->first();
                    $item->branch_name = $branch ? $branch->name : 'Unknown Branch';
                    $item->branch_code = $branch ? $branch->code : 'N/A';
                    return $item;
                });

            // Get specific branch details if selected
            $selectedBranch = null;
            if ($branchId) {
                $selectedBranch = $branches->where('id', $branchId)->first();
            }

            return [
                'branches' => $branches,
                'selectedBranch' => $selectedBranch,
                'branchPerformance' => $branchPerformance,
                'totalBookings' => $branchPerformance->sum('booking_count'),
                'totalRevenue' => $branchPerformance->sum('total_amount')
            ];
        });

        return array_merge($report, [
            'startDate' => $startDate->format('d/m/Y'),
            'endDate' => $endDate->format('d/m/Y')
        ]);
    }

    /**
     * Export report to Excel
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function exportReportToExcel(Request $request)
    {
        $reportType = $request->input('report_type');
        $startDate = null;
        $endDate = null;

        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->startOfDay();
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->endOfDay();
        }

        $filterParams = $request->input('filter_params', []);

        switch ($reportType) {
            case 'customer_sales':
                return Excel::download(
                    new CustomerSalesExport($startDate, $endDate, $filterParams),
                    'customer_sales_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
            case 'branch_performance':
                return Excel::download(
                    new BranchPerformanceExport($startDate, $endDate, $filterParams),
                    'branch_performance_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
            default:
                return Excel::download(
                    new BookingsExport($startDate, $endDate, null, null),
                    'bookings_' . Carbon::now()->format('Y-m-d') . '.xlsx'
                );
        }
    }
}
