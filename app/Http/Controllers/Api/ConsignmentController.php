<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Manifest;
use App\Booking;
use App\Delivery;
use App\Pincode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ConsignmentController extends Controller
{
    public function __construct() {
        return $this->middleware('auth:api');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_id = $request->input('logged_user_id');

        // Cache the query results for better performance
        $cacheKey = "consignments_user_{$user_id}";

        $consignments = Cache::remember($cacheKey, 60, function () use ($user_id) {
            return Manifest::with('booking.delivery.pincode')
                ->where('status', 'Out for Delivery')
                ->where('manifest_type', 'D')
                ->where(function ($query) use ($user_id) {
                    $query->where('delivery_user_id', $user_id)
                        ->orWhereNull('delivery_user_id');
                })
                ->select('manifests.*')
                ->distinct()
                ->get();
        });

        $response = null;
        if($consignments->isNotEmpty()) {
            foreach($consignments as $key => $consignment) {
                $response[$key]['consign_number'] = $consignment->manifest_number;
                $response[$key]['status'] = $consignment->status;
                $response[$key]['receiver_name'] = $consignment->booking->delivery->receiver_name ?? null;
                $response[$key]['address'] = $consignment->booking->delivery->add_line_1 ?? null;
                $response[$key]['mobile'] = $consignment->booking->delivery->mobile_number ?? null;
                $response[$key]['area'] = $consignment->booking->delivery->add_line_2 ?? null;
                $response[$key]['city'] = $consignment->booking->delivery->city ?? null;
                $response[$key]['district'] = $consignment->booking->delivery->district ?? null;
                $response[$key]['state'] = $consignment->booking->delivery->state ?? null;
                if(isset($consignment->booking->delivery->pincode->pincode)) {
                    $response[$key]['pincode'] = $consignment->booking->delivery->pincode->pincode;
                }
                if(isset($consignment->booking->delivery->phone)) {
                    $response[$key]['landline'] = $consignment->booking->delivery->phone;
                }
                $response[$key]['manifest_id'] = $consignment->id;
                $response[$key]['consignment_date'] = $consignment->created_at->toDateString();
            }
        }

        if($response) {
            return response()->json([
                'status' => 1,
                'message' => 'Success',
                'data' => $response
            ], 200);
        }

        return response()->json([
            'status' => 0,
            'message' => 'No Consignment Found',
        ], 404);
    }

    public function indextestone(Request $request)
    {
        $user_id = $request->input('logged_user_id');

        // Optimized query with eager loading relationships
        $consignments = Manifest::with(['booking.delivery.pincode'])
            ->select('manifests.id', 'manifests.manifest_number', 'manifests.status',
                'manifests.created_at', 'deliveries.receiver_name',
                'deliveries.add_line_1', 'deliveries.add_line_2',
                'deliveries.city', 'deliveries.mobile_number',
                'pincodes.pincode')
            ->join('bookings', 'manifests.manifest_number', '=', 'bookings.consg_number')
            ->join('deliveries', 'bookings.id', '=', 'deliveries.booking_id')
            ->join('pincodes', 'deliveries.pincode_id', '=', 'pincodes.id')
            ->where('bookings.status', 'Out for Delivery')
            ->where('manifests.status', 'Out for Delivery')
            ->where('manifests.manifest_type', 'D')
            ->where('manifests.delivery_user_id', $user_id)
            ->groupBy('manifests.manifest_number', 'manifests.id', 'manifests.status',
                'manifests.created_at', 'deliveries.receiver_name',
                'deliveries.add_line_1', 'deliveries.add_line_2',
                'deliveries.city', 'deliveries.mobile_number',
                'pincodes.pincode')
            ->get();

        $response = null;
        if($consignments->isNotEmpty()) {
            foreach($consignments as $key => $consignment) {
                $response[$key]['manifest_id'] = $consignment->id;
                $response[$key]['consign_number'] = $consignment->manifest_number;
                $response[$key]['status'] = $consignment->status;
                $response[$key]['receiver_name'] = $consignment->receiver_name;
                $response[$key]['mobile'] = $consignment->mobile_number;
                $response[$key]['address'] = $consignment->add_line_1 ?? null;
                $response[$key]['area'] = $consignment->add_line_2 ?? null;
                $response[$key]['city'] = $consignment->city ?? null;
                $response[$key]['pincode'] = $consignment->pincode ?? null;
                $response[$key]['date'] = $consignment->created_at->toDateString();
            }

            if($response != null) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Success',
                    'data' => $response
                ], 200);
            }
        }

        return response()->json([
            'status' => 0,
            'message' => 'No Consignment Found',
        ], 200);
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

    public function addRunsheet(Request $request)
    {
        $consg_number = $request->input('consignmentNumber');
        $loggedOfficeId = $request->input('logged_office_id');
        $officeType = $request->input('office_type');

        // Check if it's an outgoing RTO
        $IsIncomingRTO = $this->isOutgoingRTO($consg_number, $loggedOfficeId, $officeType);
        if (!$IsIncomingRTO) {
            return response()->json([
                'status' => 0,
                'message' => 'No Return Incoming OR Outgoing Manifest for this consg number',
            ], 200);
        }

        // Check attempt count
        $no_of_attempts = $this->getNoOfAttempts($consg_number);
        if ($no_of_attempts > 4) {
            return response()->json([
                'status' => 0,
                'message' => 'Number of Attempts reached four times',
            ], 200);
        }

        // Check if already out for delivery
        $isAlreadyOutForDelivery = $this->isAlreadyOutForDelivery($consg_number, $loggedOfficeId, $officeType);
        if ($isAlreadyOutForDelivery) {
            return response()->json([
                'status' => 0,
                'message' => 'Already out for delivery this consignment number',
            ], 200);
        }

        // Check for outgoing manifest
        $isOutGoingManifest = $this->isOutGoingManifest($consg_number, $loggedOfficeId, $officeType);
        if (!$isOutGoingManifest) {
            return response()->json([
                'status' => 0,
                'message' => 'No outgoing entry for this consignment number',
            ], 200);
        }

        // Check if already delivered
        $isDelivered = $this->isDelivered($consg_number);
        if ($isDelivered) {
            return response()->json([
                'status' => 0,
                'message' => 'This Consignment Already Delivered',
            ], 200);
        }

        return response()->json([
            'status' => 1,
            'message' => 'Success',
        ], 200);
    }

    public function saveRunsheet(Request $request)
    {
        $consg_numbers = $request->input('consignmentNumbers');
        $loggedOfficeId = $request->input('logged_office_id');
        $officeType = $request->input('office_type');
        $delivery_user_id = $request->input('delivery_user_id');
        $customer_view = 1;

        try {
            DB::beginTransaction();

            foreach ($consg_numbers as $consg_number) {
                // Update booking status
                Booking::where('consg_number', $consg_number)
                    ->update(['status' => 'Out for Delivery']);

                // Process based on office type
                if ($officeType == 'BR' || $officeType == 'HO') {
                    $manifest = Manifest::where('last_mile_delivery', 1)
                        ->where('manifest_type', 'O')
                        ->where('status', 'Arrived to Destination Hub')
                        ->where('manifest_number', $consg_number)
                        ->first();
                } elseif ($officeType == 'FR') {
                    $manifest = Manifest::where('manifest_type', 'O')
                        ->where('receiver_type', $officeType)
                        ->where('receiver_id', $loggedOfficeId)
                        ->where('manifest_number', $consg_number)
                        ->first();

                    if ($manifest) {
                        $manifest->last_mile_delivery = 1;
                        Manifest::where('id', $manifest->id)
                            ->update(['status' => 'Arrived to Destination Hub']);
                    }
                }

                if (!$manifest) {
                    continue;
                }

                // Check if already out for delivery
                $isOutForDelivery = $this->isAlreadyOutForDelivery($consg_number, $loggedOfficeId, $officeType);
                if (!$isOutForDelivery) {
                    Manifest::create([
                        'manifest_type' => 'D',
                        'manifest_number' => $manifest->manifest_number,
                        'origin_branch_id' => $manifest->origin_branch_id,
                        'dest_branch_id' => $manifest->dest_branch_id,
                        'sender_id' => $manifest->sender_id,
                        'receiver_id' => $manifest->receiver_id,
                        'sender_type' => $manifest->sender_type,
                        'receiver_type' => $manifest->receiver_type,
                        'consg_number_id' => 0,
                        'last_mile_delivery' => $manifest->last_mile_delivery,
                        'delivery_user_id' => $delivery_user_id,
                        'customer_view' => $customer_view,
                        'status' => 'Out for Delivery',
                        'user_id' => $delivery_user_id,
                        'office_type' => $officeType,
                        'office_id' => $loggedOfficeId,
                        'remarks' => ''
                    ]);
                }
            }

            DB::commit();

            // Clear any cached data
            $this->clearConsignmentCache($user_id = $delivery_user_id);

            return response()->json([
                'status' => 1,
                'message' => 'Your run sheet has prepared',
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to save runsheet: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'Failed to process runsheet: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function clearConsignmentCache($user_id = null)
    {
        Cache::forget("consignments_user_{$user_id}");
    }

    public function getNoOfAttempts($consg_number)
    {
        $cacheKey = "delivery_attempts_{$consg_number}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number) {
            $booking = Booking::with('delivery')
                ->where('consg_number', $consg_number)
                ->first();

            if ($booking && isset($booking->delivery->no_of_attempts)) {
                return $booking->delivery->no_of_attempts;
            }

            return 0;
        });
    }

    public function isOutGoingManifest($consg_number, $loggedOfficeId, $officeType)
    {
        $cacheKey = "is_outgoing_manifest_{$consg_number}_{$loggedOfficeId}_{$officeType}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number, $loggedOfficeId, $officeType) {
            $manifest = Manifest::where('manifest_number', $consg_number)
                ->where('manifest_type', 'O')
                ->where('receiver_id', $loggedOfficeId)
                ->where('receiver_type', $officeType)
                ->where('sender_id', $loggedOfficeId)
                ->where('sender_type', $officeType)
                ->first();

            return $manifest ? true : false;
        });
    }

    public function isBooking($consg_number)
    {
        $cacheKey = "is_booking_{$consg_number}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number) {
            $booking = Booking::where('consg_number', $consg_number)->first();
            return $booking ? true : false;
        });
    }

    public function isDelivered($consg_number)
    {
        $cacheKey = "is_delivered_{$consg_number}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number) {
            $delivered = Booking::where('consg_number', $consg_number)
                ->where('status', 'Delivered')
                ->first();
            return $delivered ? true : false;
        });
    }

    public function isAlreadyOutForDelivery($consg_number, $loggedOfficeId, $officeType)
    {
        $cacheKey = "is_out_for_delivery_{$consg_number}_{$loggedOfficeId}_{$officeType}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number, $loggedOfficeId, $officeType) {
            $manifest = Manifest::join('bookings', 'manifests.manifest_number', '=', 'bookings.consg_number')
                ->where('bookings.status', 'Out for Delivery')
                ->where('manifests.status', 'Out for Delivery')
                ->where('manifests.manifest_type', 'D')
                ->where('manifests.receiver_id', $loggedOfficeId)
                ->where('manifests.receiver_type', $officeType)
                ->where('manifests.sender_id', $loggedOfficeId)
                ->where('manifests.sender_type', $officeType)
                ->where('manifests.manifest_number', $consg_number)
                ->first();
            return $manifest ? true : false;
        });
    }

    public function isOutgoingRTO($consg_number, $loggedOfficeId, $officeType)
    {
        $cacheKey = "is_outgoing_rto_{$consg_number}_{$loggedOfficeId}_{$officeType}";

        return Cache::remember($cacheKey, 300, function () use ($consg_number, $loggedOfficeId, $officeType) {
            $returned = Booking::where('consg_number', $consg_number)
                ->where('status', 'Returned')
                ->first();

            if ($returned) {
                $manifest = Manifest::where('manifest_number', $consg_number)
                    ->where('manifest_type', 'RO')
                    ->where('receiver_id', $loggedOfficeId)
                    ->where('receiver_type', $officeType)
                    ->where('sender_id', $loggedOfficeId)
                    ->where('sender_type', $officeType)
                    ->first();

                return $manifest ? true : false;
            }

            return true;
        });
    }

    public function deliveryCount(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $user_id = $request->input('logged_user_id');

        $cacheKey = "delivery_count_{$user_id}_{$today}";

        $deliveryCount = Cache::remember($cacheKey, 600, function () use ($user_id, $today) {
            return Manifest::where('status', 'Delivered')
                ->where('delivery_user_id', $user_id)
                ->whereDate('created_at', $today)
                ->count();
        });

        return response()->json([
            'status' => 1,
            'count' => $deliveryCount
        ], 200);
    }

    public function todayConsignmentCount(Request $request)
    {
        $today = Carbon::today()->toDateString();
        $user_id = $request->input('logged_user_id');
        $cacheKey = "today_counts_{$user_id}_{$today}";

        $counts = Cache::remember($cacheKey, 600, function () use ($user_id, $today) {
            // Total consignments
            $totalConsignment = Manifest::where('status', 'Out for Delivery')
                ->where('delivery_user_id', $user_id)
                ->whereDate('created_at', $today)
                ->count();

            // Delivered count
            $deliveryCount = Delivery::join('bookings', function ($join) {
                $join->on('bookings.id', '=', 'deliveries.booking_id');
                $join->on('bookings.status', '=', 'deliveries.delivery_status');
            })
                ->where('deliveries.delivery_status', 'Delivered')
                ->where('deliveries.delivery_user_id', $user_id)
                ->whereDate('deliveries.updated_at', $today)
                ->count();

            // Pending count
            $pendingCount = Manifest::join('bookings', 'manifests.manifest_number', '=', 'bookings.consg_number')
                ->where('bookings.status', 'Out for Delivery')
                ->where('manifests.status', 'Out for Delivery')
                ->where('manifests.manifest_type', 'D')
                ->where('manifests.delivery_user_id', $user_id)
                ->count();

            // Return count
            $returnCount = Manifest::where('status', 'Return to Destination Hub')
                ->where('delivery_user_id', $user_id)
                ->whereDate('created_at', $today)
                ->count();

            return [
                'total_count' => $totalConsignment,
                'delivered_count' => $deliveryCount,
                'pending_count' => $pendingCount,
                'returned_count' => $returnCount
            ];
        });

        return response()->json([
            'status' => 1,
            'total_count' => $counts['total_count'],
            'delivered_count' => $counts['delivered_count'],
            'pending_count' => $counts['pending_count'],
            'returned_count' => $counts['returned_count']
        ], 200);
    }
}
