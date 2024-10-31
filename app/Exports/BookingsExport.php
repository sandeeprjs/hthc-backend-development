<?php

namespace App\Exports;

use App\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class BookingsExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
 
    protected $request;
    protected $consg_number;
    public function __construct($consg_number,$customer_id,$start_date,$end_date)
    {
        $this->consg_number = $consg_number;
        $this->customer_id = $customer_id;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
    }
    public function collection()
    {

        $consgNumber = $this->consg_number;
        $customer_id = $this->customer_id;
        $start_date = $this->start_date;
        $end_date = $this->end_date;

       
        $bookings =  Booking::when($consgNumber, function ($q) use ($consgNumber) {
            $q->where('consg_number', $consgNumber);
        })
        ->when($customer_id, function ($q) use ($customer_id) {
            $q->where('customer_id', $customer_id);
        })
        ->when($start_date && $end_date, function ($q) use ($start_date, $end_date) {
            $q->whereBetween('created_at', [$start_date, $end_date]);
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
                $result[] = [
                            $booking->consg_number,
                            $customer_code,
                            $booking_pincode,
                            $delivery_pincode,
                            $subscription,
                            $booking->consg_type,
                            date('d-m-Y H:i',strtotime($booking->created_at)),
                            $booking->status
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
            'Status'
        ];
    }
   
}
