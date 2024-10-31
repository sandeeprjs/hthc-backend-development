<?php

namespace App\Exports;

use App\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;
class ShipmentReportExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
 
    protected $request;
    protected $consg_number;
    public function __construct($consg_type,$customer_id,$start_date,$end_date,$status,$subscriptionId)
    {
        $this->consg_type = $consg_type;
        $this->customer_id = $customer_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
        $this->subscriptionId = $subscriptionId;
    }
    public function collection()
    {

        $consg_type = $this->consg_type;
        $customerId = $this->customer_id;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $status = $this->status;
        $subscriptionId = $this->subscriptionId;
        $startDate = null;
        $endDate = null;

        if ($start_date) {
            $startDate = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        }
        if ($end_date) {
            $endDate = Carbon::createFromFormat('d/m/Y', $end_date)->addDay()->format('Y-m-d');
        }

        $bookings = Booking::when($customerId, function ($q) use ($customerId) {
            $q->where('customer_id', [$customerId]);
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
        ->when($consg_type, function ($q) use ($consg_type) {
            $q->where('consg_type', $consg_type);
        })
        ->orderBy('updated_at', 'desc')->get();
       
        $result = [];
       
        if($bookings){
           foreach($bookings as $key => $booking){
                $booking_pincode=null;
                $delivery_pincode=null;
                $customer_code = null;
                $subscription = null;
                if(isset($booking->pincode->pincode)){
                    $booking_pincode = $booking->pincode->pincode;
                }
                if(isset($booking->delivery->pincode->pincode)){
                    $delivery_pincode = $booking->delivery->pincode->pincode;
                }
                if(isset($booking->customer->code)){
                    $customer_code = $booking->customer->code;
                }
                if(isset($booking->subscription->name)){
                    $subscription = $booking->subscription->name;
                }
                if($booking->status == 'Delivered' || $booking->status == 'Returned' ){
                    $delivered_date =  date('d-m-Y H:i',strtotime($booking->delivery->updated_at));
                }else{
                    $delivered_date = " -- ";
                }
                $result[] = [
                            $booking->consg_number,
                            $customer_code,
                            $booking_pincode,
                            $delivery_pincode,
                            $subscription,
                            $booking->consg_type,
                            date('d-m-Y H:i',strtotime($booking->created_at)),
                            $delivered_date,
                            $booking->delivery->user['username'],
                            $booking->status,
                            $booking->weight
                ];
              
            }
       }
       return collect($result); 
    }
    public function headings(): array
    {
        return [
            'Consg Number',
            'Customer ID',
            'Origin Pincode',
            'Destination Pincode',
            'Subscription',
            'Booking Type',
            'Booking Date',
            'Delivery / Return Date',
            'EMP Code',
            'Status',
            'Weight (g)'
        ];
    }
   
}
