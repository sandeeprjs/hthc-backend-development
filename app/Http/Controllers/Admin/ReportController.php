<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    /**
     * Report service instance
     */
    protected $reportService;

    /**
     * Create a new controller instance.
     *
     * @param ReportService $reportService
     * @return void
     */
    public function __construct(ReportService $reportService)
    {
        $this->middleware(['auth', 'role']);
        $this->reportService = $reportService;
    }

    /**
     * Export bookings report with custom filters
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportReport(Request $request) {
        $this->validate($request, [
            'start_date' => 'nullable|date_format:d/m/Y',
            'end_date' => 'nullable|date_format:d/m/Y',
            'customer_id' => 'nullable|integer',
            'status' => 'nullable|string',
            'report_type' => 'required|in:standard,detailed,summary'
        ]);

        return $this->reportService->generateReport($request);
    }

    /**
     * Search bookings with advanced filtering (AJAX endpoint)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request) {
        $this->validate($request, [
            'search_term' => 'nullable|string|max:100',
            'date_range' => 'nullable|string',
            'status' => 'nullable|string',
            'customer_id' => 'nullable|integer',
            'per_page' => 'nullable|integer|min:10|max:100'
        ]);

        $bookings = $this->reportService->searchBookings($request);
        return response()->json($bookings);
    }

    /**
     * Generate invoice for a booking
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice($id) {
        return $this->reportService->generateInvoicePdf($id);
    }

    /**
     * Generate delivery receipt
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateDeliveryReceipt($id) {
        return $this->reportService->generateDeliveryReceiptPdf($id);
    }

    /**
     * Generate manifest for a batch of bookings
     *
     * @param int $batchId
     * @return \Illuminate\Http\Response
     */
    public function generateManifest($batchId) {
        return $this->reportService->generateManifestPdf($batchId);
    }

    /**
     * Sales report by customer
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function customerSalesReport(Request $request) {
        $this->validate($request, [
            'start_date' => 'nullable|date_format:d/m/Y',
            'end_date' => 'nullable|date_format:d/m/Y',
            'customer_id' => 'nullable|integer'
        ]);

        $data = $this->reportService->getCustomerSalesReport($request);
        return view('reports.customer_sales', $data);
    }

    /**
     * Branch performance report
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function branchPerformanceReport(Request $request) {
        $this->validate($request, [
            'start_date' => 'nullable|date_format:d/m/Y',
            'end_date' => 'nullable|date_format:d/m/Y',
            'branch_id' => 'nullable|integer'
        ]);

        $data = $this->reportService->getBranchPerformanceReport($request);
        return view('reports.branch_performance', $data);
    }

    /**
     * Generate report in Excel format
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function exportToExcel(Request $request) {
        $this->validate($request, [
            'report_type' => 'required|in:customer_sales,branch_performance,delivery_status,revenue',
            'start_date' => 'nullable|date_format:d/m/Y',
            'end_date' => 'nullable|date_format:d/m/Y',
            'filter_params' => 'nullable|array'
        ]);

        return $this->reportService->exportReportToExcel($request);
    }
}
