<?php

namespace App\Exports;

use App\Booking;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class SalesByPartnerReportExport implements FromCollection,WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
 
    protected $request;
    protected $consg_number;
    public function __construct($consg_type,$fr_code,$start_date,$end_date,$status)
    {
        $this->consg_type = $consg_type;
        $this->fr_code = $fr_code;
        $this->start_date = $start_date;
        $this->end_date = $end_date;
        $this->status = $status;
    }
    public function collection()
    {

        $consg_type = $this->consg_type;
        $frCode = $this->fr_code;
        $start_date = $this->start_date;
        $end_date = $this->end_date;
        $status = $this->status;
        $startDate = null;
        $endDate = null;
        if ($start_date) {
            $startDate = Carbon::createFromFormat('d/m/Y', $start_date)->format('Y-m-d');
        }
        if ($end_date) {
            $endDate = Carbon::createFromFormat('d/m/Y', $end_date)->addDay()->format('Y-m-d');
        }
        $bookings =  Booking::when($consg_type, function ($q) use ($consg_type) {
            $q->where('consg_type', $consg_type);
        })
        ->when($frCode, function ($q) use ($frCode) {
            $q->where('origin_office_type', 'FR')->where('origin_office_id', $frCode);
        })
        ->when($status, function ($q) use ($status) {
            $q->where('status', $status);
        })
        ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
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
                            $booking->BookingFranchisee,
                            $booking_pincode,
                            $delivery_pincode,
                            $subscription,
                            $booking->consg_type,
                            date('d-m-Y H:i',strtotime($booking->created_at)),
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
            'Partner Name',
            'Origin Pincode',
            'Destination Pincode',
            'Subscription',
            'Booking Type',
            'Booking Date',
            'Status',
            'Weight (g)'
        ];
    }
   
}
