<?php

namespace App\Imports\Manifest;

use App\Booking;
use App\Delivery;
use App\Franchisee;
use App\Manifest;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class IncomingImport implements ToCollection, WithHeadingRow
{
    /**
     * @var
     */
    private $data;
    private $total_row_count = 0;
    private $absolute_row_count = 0;

    /**
     * IncomingImport constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        $user = Auth::user();
        $data = $this->data;

        foreach ($rows as $row) {
            ++$this->total_row_count;

            if($row->filter()->isNotEmpty()){
                $booking = Booking::select(['id', 'consg_number', 'origin_office_type', 'origin_office_id', 'dest_branch_id'])->where('consg_number', $row['consg_number'])->first();
                if ($booking) {
                    if ($booking->origin_office_type == 'FR') {
                        $originBranchId = Franchisee::where('id', $booking->origin_office_id)->pluck('branch_id');
                    }
                    $delivery = Delivery::select(['id', 'pincode_id'])->where('booking_id', $booking->id)->first();

                    $existingManifest = Manifest::select('id', 'manifest_type', 'receiver_id')->where('manifest_number', $row['consg_number'])->orderBy('id', 'DESC')->first();

                    if (!$existingManifest || $existingManifest->manifest_type == 'O') {
                        ++$this->absolute_row_count;
                       
                        $destBranchId = $this->getDestBranchId($row['consg_number']);
                        $sender_id =  $existingManifest->receiver_id ?? $booking->origin_office_id;
                        $sender_type = $existingManifest->receiver_type ?? $booking->origin_office_type;
                        $receiver_id = $user->office_id;
                        $receiver_type = $user->office_type;

                        $status = "Arrived to Hub";
                      
                        if($destBranchId == $receiver_id){
                         
                                 $status = 'Arrived to Destination Hub';
                         
                        }
                        $manifest =  Manifest::create([
                            'manifest_number' => $row['consg_number'],
                            'manifest_type' => 'I',
                            'origin_branch_id' => $originBranchId[0] ?? $booking->origin_office_id,
                            'dest_branch_id' => $booking->dest_branch_id,
                            'dest_pincode_id' => $delivery->pincode_id,
                            'sender_id' => $existingManifest->receiver_id ?? $booking->origin_office_id,
                            'sender_type' => $existingManifest->receiver_type ?? $booking->origin_office_type,
                            'receiver_id' => $user->office_id,
                            'receiver_type' => $user->office_type,
                            'user_id' => $user->id,
                            'office_type' => $user->office_type,
                            'office_id' => $user->office_id,
                            'customer_view' => $data['customer_view'],
                            'status' => $status ?? null
                        ]);

                        if($manifest){
                            Booking::where('consg_number', $row['consg_number'])->update(array('status' => $status));
                        }
                        
                        
                        
                    }
                }
            }
        }
    }
   
    public function getDestBranchId($consg_number){
            
        $booking = Booking::where('consg_number', $consg_number )->first();
        if($booking){
            return $booking->dest_branch_id;
        }
        return null;
    }

    public function getTotalRowCount(): int
    {
        return $this->total_row_count;
    }

    public function getAbsoluteRowCount(): int
    {
        return $this->absolute_row_count;
    }
}
