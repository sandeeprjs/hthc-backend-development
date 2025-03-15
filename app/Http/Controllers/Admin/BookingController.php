<?php

namespace App\Http\Controllers\Admin;
use App\Consignment;
use Carbon\Carbon;

use App\Booking;
use App\Customer;
use App\Franchisee;
use App\Http\Controllers\Controller;
use App\Services\BookingService;
use App\Services\CacheService;
use App\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BookingsExport;

class BookingController extends Controller
{
    /**
     * Booking service instance
     */
    protected $bookingService;

    /**
     * Cache service instance
     */
    protected $cacheService;

    /**
     * Create a new controller instance.
     *
     * @param BookingService $bookingService
     * @param CacheService $cacheService
     * @return void
     */
    public function __construct(BookingService $bookingService, CacheService $cacheService)
    {
        $this->middleware(['auth', 'role'])->except([
            'calculateVolumetricWeight',
            'sms',
            'getBulkBookingSample',
            'getManifestSample'
        ]);

        $this->bookingService = $bookingService;
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource with permission restrictions
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Validate request inputs
        $this->validate($request, [
            'start_date' => 'nullable',
            'end_date' => 'nullable',
            'consg_number' => 'nullable',
            'customer_id' => 'nullable',
            'subscription_id' => 'nullable',
            'status' => 'nullable',
            'fr_id' => 'nullable'
        ]);

        // Process date inputs
        $startDate = null;
        $endDate = null;
        if ($request->input('start_date')) {
            $startDate = Carbon::createFromFormat('d/m/Y', $request->input('start_date'))->format('Y-m-d');
        }

        if ($request->input('end_date')) {
            $endDate = Carbon::createFromFormat('d/m/Y', $request->input('end_date'))->addDay()->format('Y-m-d');
        }

        $consgNumber = $request->input('consg_number');
        $customerId = $request->input('customer_id');
        $subscriptionId = $request->input('subscription_id');
        $status = $request->input('status');
        $frCode = $request->input('fr_id');

        // Handle export request
        if($request->get('btnSubmit') == 'export') {
            return Excel::download(new BookingsExport($consgNumber, $customerId, $startDate, $endDate), 'bookings.xlsx');
        }

        $user = Auth::user();

        // Query building
        $bookings = Booking::when(!$user->isAdmin(), function ($q) use ($user) {
            $q->where('origin_office_type', $user->office_type)
                ->where('origin_office_id', $user->office_id);
        })
            ->when($consgNumber, function ($q) use ($consgNumber) {
                $q->where('consg_number', $consgNumber);
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
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
            ->when($frCode, function ($q) use ($frCode) {
                $q->where('origin_office_type', 'FR')->where('origin_office_id', $frCode);
            })
            ->latest('id')->paginate(10);

        // Get supporting data
        $subscriptions = Subscription::select(['id', 'name'])->get();

        // Important: Explicitly load customer and franchisee data for filters
        $customer = null;
        if ($customerId) {
            $customer = Customer::select(['id', 'code'])->find($customerId);
        }

        $franchisee = null;
        if ($frCode) {
            $franchisee = Franchisee::select(['id', 'code'])->find($frCode);
        }

        $bookingStatuses = Booking::distinct('status')->pluck('status');

        return view('bookings.index', compact(['bookings', 'customer', 'subscriptions', 'bookingStatuses', 'franchisee']));
    }

    public function validateConsignment(Request $request)
    {
        $consgNumber = $request->input('consg_number');

        // Validate that the consignment number exists
        $consignment = Consignment::where('consg_number', $consgNumber)->first();

        if (!$consignment) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid Consignment Number'
            ]);
        }

        // Validate that the consignment belongs to current office
        $user = Auth::user();
        if ($user->office_type != $consignment->office_type || $user->office_id != $consignment->office_id) {
            return response()->json([
                'valid' => false,
                'message' => 'Cannot book this consignment. It was generated for a different office.'
            ]);
        }

        // Validate that the consignment isn't already used
        $existing = Booking::where('consg_number', $consgNumber)
            ->whereNull('deleted_at')
            ->first();

        if ($existing) {
            return response()->json([
                'valid' => false,
                'message' => 'This consignment number is already in use.'
            ]);
        }

        return response()->json([
            'valid' => true,
            'message' => 'Consignment number is valid.'
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = $this->bookingService->getCreateFormData();
        return view('bookings.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->bookingService->validateBookingData($request);

        $result = $this->bookingService->createBooking($request);

        if ($result['success']) {
            return redirect(route('bookings.index'))->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message'])->withInput();
        }
    }

    /**
     * Display the specified resource with permission check
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return $this->view($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $data = $this->bookingService->getEditFormData($request, $id);

        if (!$data['success']) {
            return redirect(route('bookings.index'))->withError($data['message']);
        }

        return view('bookings.edit', $data['data']);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $result = $this->bookingService->updateBooking($request, $id);

        if ($result['success']) {
            return redirect(route('bookings.index'))->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $result = $this->bookingService->deleteBooking($id);

        if ($result['success']) {
            return redirect(route('bookings.index'))->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message']);
        }
    }

    /**
     * Display a specific booking with permission check
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function view($id) {
        $data = $this->bookingService->getBookingDetails($id);

        if (!$data['success']) {
            return redirect(route('bookings.index'))->withError($data['message']);
        }

        return view('bookings.view', ['booking' => $data['booking']]);
    }

    /**
     * Calculate volumetric weight
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function calculateVolumetricWeight(Request $request) {
        $this->validate($request, [
            'length' => 'required|numeric',
            'width' => 'required|numeric',
            'height' => 'required|numeric'
        ]);

        $volWeight = $this->bookingService->calculateVolumetricWeight(
            $request->input('length'),
            $request->input('width'),
            $request->input('height')
        );

        return response()->json([
            'message' => 'success',
            'volWeight' => $volWeight
        ], 200);
    }

    /**
     * Get booking history with status timeline
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function history($id) {
        $result = $this->bookingService->getBookingHistory($id);

        if (!$result['success']) {
            return response()->json(['error' => $result['message']], 404);
        }

        return response()->json($result['data']);
    }

    /**
     * Dashboard statistics for bookings
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function dashboardStats(Request $request) {
        $stats = $this->bookingService->getDashboardStats($request);
        return response()->json($stats);
    }

    /**
     * Generate booking acknowledgement PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function generateAcknowledgement($id) {
        return $this->bookingService->generateAcknowledgementPdf($id);
    }
}
