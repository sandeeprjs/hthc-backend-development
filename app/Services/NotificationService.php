<?php

namespace App\Services;

use App\Booking;
use App\Delivery;
use App\Http\Helpers\AppHelper;
use Illuminate\Support\Facades\Mail;
use App\Mail\ConsignmentBooked;
use App\Mail\BulkBookingMail;
use App\Mail\StatusUpdateMail;
use App\Mail\TestEmail;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Send tracking SMS
     *
     * @param string $customerName
     * @param string $mobileNumber
     * @param int $batchId
     * @return array
     */
    public function sendTrackingSMS($customerName, $mobileNumber, $batchId)
    {
        try {
            AppHelper::sendBulkTrackingMessage($customerName, $mobileNumber, $batchId);

            return [
                'success' => true,
                'message' => 'SMS sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send tracking SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send test SMS
     *
     * @param string $customerName
     * @param string $mobileNumber
     * @param string $awb
     * @return mixed
     */
    public function sendTestSMS($customerName, $mobileNumber, $awb)
    {
        try {
            $apiKey = urlencode(env('TEXTLOCAL_API_KEY', ''));
            $numbers = array($mobileNumber);
            $sender = urlencode('hthcin');
            $url = "hthc.co.in/sp/s-" . $awb;

            $message = "Dear $customerName, Your shipment is booked with HTHC Courier. Please check the shipper copy $url";
            $message = urlencode($message);
            $numbers = implode(',', $numbers);

            // Prepare data for POST request
            $data = array(
                'apikey' => $apiKey,
                'numbers' => $numbers,
                "sender" => $sender,
                "message" => $message
            );

            // Send the POST request with cURL
            $ch = curl_init('https://api.textlocal.in/send/');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);

            if (curl_errno($ch)) {
                throw new \Exception(curl_error($ch));
            }

            curl_close($ch);
            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to send test SMS: ' . $e->getMessage());
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Send emails for booking notifications
     *
     * @param Booking $booking
     * @param Delivery $delivery
     * @return array
     */
    public function sendBookingEmails($booking, $delivery)
    {
        try {
            if ($booking->email) {
                Mail::to($booking->email)->send(new ConsignmentBooked($booking, $delivery, 'sender'));
            }

            if ($delivery->email) {
                Mail::to($delivery->email)->send(new ConsignmentBooked($booking, $delivery, 'receiver'));
            }

            return [
                'success' => true,
                'message' => 'Emails sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send booking emails: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send emails: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send status update emails
     *
     * @param Booking $booking
     * @param Delivery $delivery
     * @return array
     */
    public function sendStatusUpdateEmails($booking, $delivery)
    {
        try {
            if ($booking->email) {
                Mail::to($booking->email)->send(new StatusUpdateMail($booking, $delivery, 'sender'));
            }

            if ($delivery->email) {
                Mail::to($delivery->email)->send(new StatusUpdateMail($booking, $delivery, 'receiver'));
            }

            return [
                'success' => true,
                'message' => 'Status update emails sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send status update emails: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send status update emails: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send bulk booking email
     *
     * @param int $batchId
     * @param string $senderName
     * @param string $senderEmail
     * @return array
     */
    public function sendBulkBookingEmail($batchId, $senderName, $senderEmail = null)
    {
        try {
            if ($senderEmail) {
                Mail::to($senderEmail)->send(new BulkBookingMail($batchId, $senderName));

                return [
                    'success' => true,
                    'message' => 'Bulk booking email sent successfully'
                ];
            }

            return [
                'success' => true,
                'message' => 'No email sent (email address not provided)'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send bulk booking email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send bulk booking email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send booking email to specific recipient
     *
     * @param string $email
     * @param int $bookingId
     * @param string $notificationType
     * @return array
     */
    public function sendBookingEmail($email, $bookingId, $notificationType)
    {
        try {
            $booking = Booking::with('delivery')->find($bookingId);

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }

            switch ($notificationType) {
                case 'booking':
                    Mail::to($email)->send(new ConsignmentBooked($booking, $booking->delivery, 'recipient'));
                    break;
                case 'update':
                    Mail::to($email)->send(new StatusUpdateMail($booking, $booking->delivery, 'recipient'));
                    break;
                case 'delivery':
                    Mail::to($email)->send(new ConsignmentBooked($booking, $booking->delivery, 'delivery_notification'));
                    break;
                default:
                    return [
                        'success' => false,
                        'message' => 'Invalid notification type'
                    ];
            }

            return [
                'success' => true,
                'message' => 'Email sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send booking email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send status update notifications (SMS and Email)
     *
     * @param Booking $booking
     * @param Delivery $delivery
     * @return array
     */
    public function sendStatusUpdateNotifications($booking, $delivery)
    {
        try {
            // Send SMS notifications
            if ($booking->sms_to_sender == 1 && $booking->mobile_number) {
                AppHelper::sendStatusUpdateMessage(
                    $booking->customer_name,
                    $booking->mobile_number,
                    $booking->consg_number,
                    $booking->status
                );
            }

            if ($booking->sms_to_receiver == 1 && $delivery->mobile_number) {
                AppHelper::sendStatusUpdateMessage(
                    $delivery->receiver_name,
                    $delivery->mobile_number,
                    $booking->consg_number,
                    $booking->status
                );
            }

            // Send email notifications
            $this->sendStatusUpdateEmails($booking, $delivery);

            return [
                'success' => true,
                'message' => 'Status update notifications sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send status update notifications: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send status update notifications: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send status update SMS
     *
     * @param int $bookingId
     * @param string $recipientType
     * @return array
     */
    public function sendStatusUpdateSMS($bookingId, $recipientType)
    {
        try {
            $booking = Booking::with('delivery')->find($bookingId);

            if (!$booking) {
                return [
                    'success' => false,
                    'message' => 'Booking not found'
                ];
            }

            $delivery = $booking->delivery;

            if ($recipientType == 'sender' || $recipientType == 'both') {
                if ($booking->mobile_number) {
                    AppHelper::sendStatusUpdateMessage(
                        $booking->customer_name,
                        $booking->mobile_number,
                        $booking->consg_number,
                        $booking->status
                    );
                }
            }

            if ($recipientType == 'receiver' || $recipientType == 'both') {
                if ($delivery && $delivery->mobile_number) {
                    AppHelper::sendStatusUpdateMessage(
                        $delivery->receiver_name,
                        $delivery->mobile_number,
                        $booking->consg_number,
                        $booking->status
                    );
                }
            }

            return [
                'success' => true,
                'message' => 'Status update SMS sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send status update SMS: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send status update SMS: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Send test email
     *
     * @param string $email
     * @return array
     */
    public function sendTestEmail($email)
    {
        try {
            $data = ['message' => 'This is a test email from HTHC Courier!'];
            Mail::to($email)->send(new TestEmail($data));

            return [
                'success' => true,
                'message' => 'Test email sent successfully'
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send test email: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ];
        }
    }
}
