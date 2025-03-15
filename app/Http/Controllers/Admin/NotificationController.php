<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Notification service instance
     */
    protected $notificationService;

    /**
     * Create a new controller instance.
     *
     * @param NotificationService $notificationService
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->middleware(['auth', 'role']);
        $this->notificationService = $notificationService;
    }

    /**
     * Send SMS notification for tracking
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendSMS(Request $request) {
        $this->validate($request, [
            'mobile_number' => 'required|string|min:10|max:15',
            'customer_name' => 'required|string|max:255',
            'batch_id' => 'required|integer'
        ]);

        $result = $this->notificationService->sendTrackingSMS(
            $request->input('customer_name'),
            $request->input('mobile_number'),
            $request->input('batch_id')
        );

        return response()->json($result);
    }

    /**
     * Send detailed SMS test with proper error handling
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendSMSTest(Request $request) {
        $this->validate($request, [
            'mobile_number' => 'required|string|min:10|max:15',
            'customer_name' => 'required|string|max:255',
            'awb' => 'required|string|max:50'
        ]);

        return $this->notificationService->sendTestSMS(
            $request->input('customer_name'),
            $request->input('mobile_number'),
            $request->input('awb')
        );
    }

    /**
     * Send email notification
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendEmail(Request $request) {
        $this->validate($request, [
            'email' => 'required|email',
            'booking_id' => 'required|integer|exists:bookings,id',
            'notification_type' => 'required|in:booking,delivery,update'
        ]);

        $result = $this->notificationService->sendBookingEmail(
            $request->input('email'),
            $request->input('booking_id'),
            $request->input('notification_type')
        );

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message']);
        }
    }

    /**
     * Send bulk email notifications
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendBulkEmail(Request $request) {
        $this->validate($request, [
            'batch_id' => 'required|integer',
            'sender_name' => 'required|string|max:255',
            'sender_email' => 'required|email'
        ]);

        $result = $this->notificationService->sendBulkBookingEmail(
            $request->input('batch_id'),
            $request->input('sender_name'),
            $request->input('sender_email')
        );

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message']);
        }
    }

    /**
     * Send status update notification (SMS)
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function sendStatusUpdateSMS(Request $request) {
        $this->validate($request, [
            'booking_id' => 'required|integer|exists:bookings,id',
            'recipient_type' => 'required|in:sender,receiver,both'
        ]);

        $result = $this->notificationService->sendStatusUpdateSMS(
            $request->input('booking_id'),
            $request->input('recipient_type')
        );

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message']);
        }
    }

    /**
     * Test email sending
     *
     * @return \Illuminate\Http\Response
     */
    public function testMail(Request $request) {
        $this->validate($request, [
            'email' => 'required|email'
        ]);

        $result = $this->notificationService->sendTestEmail($request->input('email'));

        if ($request->ajax()) {
            return response()->json($result);
        }

        if ($result['success']) {
            return redirect()->back()->withSuccess($result['message']);
        } else {
            return redirect()->back()->withError($result['message']);
        }
    }
}
