<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BulkBookingService;
use App\Services\CacheService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BulkBookingController extends Controller
{
    /**
     * Bulk Booking service instance
     */
    protected $bulkBookingService;

    /**
     * Cache service instance
     */
    protected $cacheService;

    /**
     * Create a new controller instance.
     *
     * @param BulkBookingService $bulkBookingService
     * @param CacheService $cacheService
     * @return void
     */
    public function __construct(BulkBookingService $bulkBookingService, CacheService $cacheService)
    {
        $this->middleware(['auth', 'role']);

        $this->bulkBookingService = $bulkBookingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Show the form for bulk booking
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkBooking(Request $request) {
        $user = Auth::user();
        $user = $user->only(['id', 'username', 'first_name', 'last_name', 'office_type']);

        $countryList = $this->cacheService->getCountriesList();

        return view('bookings.bulk', compact('countryList', 'user'));
    }

    /**
     * Import bulk bookings
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $this->bulkBookingService->validateImportData($request);

        $result = $this->bulkBookingService->importBookings($request);

        if ($result['success']) {
            return redirect(route('bookings.validate', [$result['batch_id'], 'has_error=0']))
                ->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message'])->withInput();
        }
    }

    /**
     * Validate the imported Excel data
     *
     * @param Request $request
     * @param int $batchId
     * @return \Illuminate\Http\Response
     */
    public function validateExcel(Request $request, $batchId)
    {
        $this->validate($request, [
            'has_error' => 'nullable|boolean'
        ]);

        $data = $this->bulkBookingService->getValidationData($request, $batchId);
        return view('bookings.validate', $data);
    }

    /**
     * Create bookings from validated bulk data
     *
     * @param int $batchId
     * @return \Illuminate\Http\Response
     */
    public function bulkCreate($batchId) {
        $result = $this->bulkBookingService->createBulkBookings($batchId);

        if (!$result['success']) {
            return redirect()->back()->withError($result['message']);
        }

        return redirect(route('bookings.index'))
            ->with($result['data']);
    }

    /**
     * Print bulk consignment details
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function printBulkConsg(Request $request) {
        $this->validate($request, [
            'batchId' => 'required|integer'
        ]);

        return $this->bulkBookingService->getConsignmentDetails($request->input('batchId'));
    }

    /**
     * Get bulk booking details for AJAX
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkBookingDetails(Request $request) {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $booking = $this->bulkBookingService->getBookingDetails($request->input('id'));

        if (!$booking) {
            return response()->json([
                'error' => 'Booking not found'
            ], 404);
        }

        return response()->json($booking);
    }

    /**
     * Update sheet data
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sheetUpdate(Request $request) {
        $this->validate($request, [
            'bulk_booking_id' => 'required|integer',
            'receiver_name' => 'nullable|string|max:255',
            'receiver_address' => 'nullable|string',
            'receiver_area' => 'nullable|string',
            'receiver_pincode_id' => 'required|exists:pincodes,id',
            'receiver_mobile' => 'nullable|regex:/^([0-9\s\-\+\(\)]*)$/|min:10|max:10'
        ]);

        $result = $this->bulkBookingService->updateBookingSheet($request);

        return response()->json([
            'message' => $result['message']
        ], $result['status_code']);
    }

    /**
     * Delete a row from bulk booking
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function rowDelete(Request $request) {
        $this->validate($request, [
            'deleteId' => 'required|integer'
        ]);

        $result = $this->bulkBookingService->deleteBookingRow($request->input('deleteId'));

        return response()->json([
            'message' => $result['message']
        ], $result['status_code']);
    }

    /**
     * Download bulk booking sample file
     *
     * @return \Illuminate\Http\Response
     */
    public function getBulkBookingSample() {
        return $this->bulkBookingService->downloadSampleFile();
    }

    /**
     * Get manifest sample file
     *
     * @return \Illuminate\Http\Response
     */
    public function getManifestSample() {
        return $this->bulkBookingService->downloadManifestSample();
    }

    /**
     * Process bulk status update for multiple bookings
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStatusUpdate(Request $request) {
        $this->validate($request, [
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'required|integer|exists:bookings,id',
            'status' => 'required|string',
            'remarks' => 'nullable|string'
        ]);

        $result = $this->bulkBookingService->updateBookingStatuses(
            $request->input('booking_ids'),
            $request->input('status'),
            $request->input('remarks')
        );

        return response()->json($result);
    }
}
