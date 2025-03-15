<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Booking;
use App\Delivery;
use App\Manifest;
use App\Reason;
use App\ConsignmentReturn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Http\Helpers\AppHelper;
use App\Mail\ConsignmentDelivered;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class DeliveryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $deliveries = Cache::remember('deliveries_list', 600, function () {
            return Delivery::with(['booking', 'manifest', 'reason', 'consignmentReturn'])
                ->paginate(50);
        });

        return response()->json($deliveries);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validate the request
        $this->validate($request, [
            'consg_number' => 'required',
            'status' => 'required',
            'manifest_id' => 'required'
        ]);

        $consg_number = $request->input('consg_number');
        $manifest_id = $request->input('manifest_id');
        $status = $request->input('status');
        $delivery_user_id = $request->input('delivery_user_id');
        $tookstatus = $request->input('tookstatus');
        $receiver_name = $request->input('rec_name');

        if ($status == 'Delivered') {
            if ($request->file('receiver_photo') == null && $request->file('receiver_sign') == null && $request->file('receiver_voice') == null) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Customer Photo or Sign or voice is required',
                ], 403);
            }
        }

        try {
            // Check if already delivered using cache
            $cacheKey = "delivery_status_{$consg_number}";
            $isDelivered = Cache::remember($cacheKey, 300, function () use ($consg_number) {
                return $this->isDelivered($consg_number);
            });

            if ($isDelivered) {
                return response()->json([
                    'status' => 0,
                    'message' => 'This Consignment Already Delivered',
                ], 403);
            }

            DB::beginTransaction();

            // Find booking with cached query
            $bookingCacheKey = "booking_{$consg_number}";
            $booking = Cache::remember($bookingCacheKey, 300, function () use ($consg_number) {
                return Booking::where('consg_number', $consg_number)->first();
            });

            if (!$booking) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No data found for this consignment number',
                ], 403);
            }

            // Update booking status
            Booking::find($booking->id)->update(['status' => $status]);

            // Get delivery with caching
            $deliveryCacheKey = "delivery_for_booking_{$booking->id}";
            $delivery = Cache::remember($deliveryCacheKey, 300, function () use ($booking) {
                return Delivery::where('booking_id', $booking->id)->first();
            });

            // Update delivery details
            $delivery->delivery_status = $status;
            $delivery->delivery_user_id = $delivery_user_id;
            $delivery->delivery_datetime = $booking->updated_at;
            $delivery->tookstatus = $tookstatus;
            $delivery->rec_name = $receiver_name;

            if ($status == 'Cancelled' || $status == 'Returned') {
                $delivery->no_of_attempts = $delivery->no_of_attempts + 1;
            }
            $delivery->save();

            // Send SMS notifications
            if ($delivery->mobile_number || $booking->mobile_number) {
                if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
                    AppHelper::sendDeliveryMessage($booking->customer_name, $booking->mobile_number, $booking->consg_number);
                }
                if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
                    AppHelper::sendDeliveryMessage($delivery->receiver_name, $delivery->mobile_number, $booking->consg_number);
                }
            }

            // Process file uploads using Storage facade
            $file_photo = null;
            $file_sign = null;
            $file_voice = null;

            if ($request->hasFile('receiver_photo')) {
                $file = $request->file('receiver_photo');
                $name = $delivery->id . '_' . $file->getClientOriginalName();
                $path = 'delivery/photo/' . $name;

                // Use proper Storage facade instead of direct file manipulation
                Storage::disk('public')->putFileAs('delivery/photo', $file, $name);

                $file_photo = $delivery->files()->create([
                    'name' => $name,
                    'url' => $path,
                    'ext' => $file->extension(),
                    'type' => 'receiver_photo',
                    'alt' => $request->input('alt_text')
                ]);
            }

            if ($request->hasFile('receiver_sign')) {
                $file = $request->file('receiver_sign');
                $name = $delivery->id . '_' . $file->getClientOriginalName();
                $path = 'delivery/sign/' . $name;

                Storage::disk('public')->putFileAs('delivery/sign', $file, $name);

                $file_sign = $delivery->files()->create([
                    'name' => $name,
                    'url' => $path,
                    'ext' => $file->extension(),
                    'type' => 'receiver_sign',
                    'alt' => $request->input('alt_text')
                ]);
            }

            if ($request->hasFile('receiver_voice')) {
                $file = $request->file('receiver_voice');
                $name = $delivery->id . '_' . $file->getClientOriginalName();
                $path = 'delivery/voice/' . $name;

                Storage::disk('public')->putFileAs('delivery/voice', $file, $name);

                $file_voice = $delivery->files()->create([
                    'name' => $name,
                    'url' => $path,
                    'ext' => $file->extension(),
                    'type' => 'receiver_voice',
                    'alt' => $request->input('alt_text')
                ]);
            }

            // Queue email sending instead of blocking the request
            if ($booking->email) {
                Mail::queue(new ConsignmentDelivered($booking, $delivery, 'sender'));
            }
            if ($delivery->email) {
                Mail::queue(new ConsignmentDelivered($booking, $delivery, 'receiver'));
            }

            DB::commit();

            // Clear related caches
            Cache::forget($cacheKey);
            Cache::forget($bookingCacheKey);
            Cache::forget($deliveryCacheKey);

            return response()->json([
                'status' => 1,
                'message' => 'success',
                'receiver_photo' => $file_photo,
                'receiver_sign' => $file_sign,
                'receiver_voice' => $file_voice
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Delivery processing failed: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'Processing failed: ' . $e->getMessage()
            ], 500);
        }
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

    /**
     * Check if the consignment is already delivered
     *
     * @param string $consg_number
     * @return bool
     */
    public function isDelivered($consg_number)
    {
        return Booking::where('consg_number', $consg_number)
            ->where('status', 'Delivered')
            ->exists();
    }

    /**
     * Return Consignments
     */
    public function consignmentReturn(Request $request)
    {
        $consg_number = $request->input('consg_number');
        $reasonId = $request->input('reason_id');
        $returnMode = $request->input('return_mode');
        $userId = $request->input('user_id');

        try {
            DB::beginTransaction();

            // Find booking with cached query
            $bookingCacheKey = "booking_{$consg_number}";
            $booking = Cache::remember($bookingCacheKey, 300, function () use ($consg_number) {
                return Booking::where('consg_number', $consg_number)->first();
            });

            if (!$booking) {
                return response()->json([
                    'status' => 0,
                    'message' => 'No data found for this consignment number',
                ], 403);
            }

            if ($booking->status == 'Returned') {
                return response()->json([
                    'status' => 0,
                    'message' => 'Already Returned this consignment number',
                ], 403);
            }

            $status = "Returned";
            if ($returnMode == 'Cancelled') {
                $status = "Cancelled";
            }

            // Create return record
            $return = ConsignmentReturn::create([
                'consg_number' => $consg_number,
                'reason_id' => $reasonId,
                'return_mode' => $returnMode,
                'user_id' => $userId
            ]);

            // Find manifest with caching
            $manifestCacheKey = "manifest_delivery_{$consg_number}";
            $manifest = Cache::remember($manifestCacheKey, 300, function () use ($consg_number) {
                return Manifest::where('last_mile_delivery', 1)
                    ->where('manifest_type', 'D')
                    ->where('status', 'Out for Delivery')
                    ->where('manifest_number', $consg_number)
                    ->first();
            });

            // Update booking
            $booking->status = $status;
            $booking->save();

            // Update delivery
            $delivery = Delivery::where('booking_id', $booking->id)->first();
            $no_of_attempts = $delivery->no_of_attempts === null ? 1 : $delivery->no_of_attempts + 1;

            Delivery::where('booking_id', $booking->id)
                ->update([
                    'no_of_attempts' => $no_of_attempts,
                    'delivery_user_id' => $userId
                ]);

            // Create return manifest if needed
            if ($manifest) {
                $customer_view = 1;
                Manifest::create([
                    'manifest_type' => 'R',
                    'manifest_number' => $manifest->manifest_number,
                    'origin_branch_id' => $manifest->origin_branch_id,
                    'dest_branch_id' => $manifest->dest_branch_id,
                    'sender_id' => $manifest->sender_id,
                    'receiver_id' => $manifest->receiver_id,
                    'sender_type' => $manifest->sender_type,
                    'receiver_type' => $manifest->receiver_type,
                    'consg_number_id' => 0,
                    'last_mile_delivery' => $manifest->last_mile_delivery,
                    'delivery_user_id' => $userId,
                    'customer_view' => $customer_view,
                    'status' => 'Return to Destination Hub',
                    'user_id' => $userId,
                    'office_type' => $manifest->receiver_type,
                    'office_id' => $manifest->office_id,
                    'remarks' => ''
                ]);
            }

            DB::commit();

            // Clear caches
            Cache::forget($bookingCacheKey);
            Cache::forget($manifestCacheKey);
            Cache::forget("delivery_for_booking_{$booking->id}");

            return response()->json([
                'status' => 1,
                'message' => 'success',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Consignment return failed: ' . $e->getMessage());

            return response()->json([
                'status' => 0,
                'message' => 'Processing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getReasons(Request $request)
    {
        $type = $request->input('status');
        $reasonType = "return";

        if ($type == "Returned") {
            $reasonType = "return";
        }
        if ($type == "Cancelled") {
            $reasonType = "cancel";
        }

        // Cache reasons to improve performance
        $cacheKey = "reasons_{$reasonType}";
        $reasons = Cache::remember($cacheKey, 3600, function () use ($reasonType) {
            return Reason::where('type', $reasonType)->get();
        });

        if ($reasons) {
            return response()->json([
                'status' => 1,
                'message' => 'success',
                'data' => $reasons
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Reason Not Found',
        ], 403);
    }

    public function getBranchFranchisee()
    {
        // Cache branches and franchisees to improve performance
        $cacheKey = "branch_franchisee_list";
        $branches = Cache::remember($cacheKey, 3600, function () {
            $franchisees = DB::table("franchisees")->select("franchisees.id", "franchisees.code");
            return DB::table("branches")->select("branches.id", "branches.code")
                ->union($franchisees)
                ->whereNull('deleted_at')
                ->get();
        });

        if ($branches) {
            return response()->json([
                'status' => 1,
                'message' => 'success',
                'data' => $branches
            ]);
        }

        return response()->json([
            'status' => 0,
            'message' => 'Branch Not Found',
        ], 403);
    }
}
