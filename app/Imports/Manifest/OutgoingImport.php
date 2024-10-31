<?php

namespace App\Imports\Manifest;

use App\Booking;
use App\Delivery;
use App\Franchisee;
use App\Manifest;
use App\Branch;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class OutgoingImport implements ToCollection, WithHeadingRow
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

        $delivery_user_id = null;
        $last_mile_delivery = 0;
        $customer_view = 0;

        echo '<pre>';
        print_r($data);
        //exit;
      
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

                    if ($existingManifest && $existingManifest->manifest_type == 'I') {
                        ++$this->absolute_row_count;

                        $officeDetails = $this->getOfficeDetails($data['receiver_id']);

                        $sender_id = $user->office_id;;
                        $sender_type = $user->office_type;
                        $receiver_id = $data['receiver_id'];
                        $receiver_type = $data['office_type'];
                        
                        if($sender_id == $receiver_id && $sender_type == $receiver_type){
                            $status = 'Arrived to Destination Hub';
                            $delivery_user_id = 1;
                            $last_mile_delivery = 1 ;
                            $customer_view = 1;
                            $this->updateStatusOfIncomingManifest($row['consg_number'],$sender_id);
                        }
                                           
                        $status = 'In Transit';
                        if($delivery_user_id != null){
                            $status = 'Arrived to Destination Hub';
                        }
                        Booking::where('consg_number', $row['consg_number'])->update(array('status' => $status));
                       
                        $manifest = Manifest::create([
                            'manifest_number' => $row['consg_number'],
                            'manifest_type' => 'O',
                            'origin_branch_id' => $originBranchId[0] ?? $booking->origin_office_id,
                            'dest_branch_id' => $booking->dest_branch_id,
                            'dest_pincode_id' => $delivery->pincode_id,
                            'sender_id' => $user->office_id,
                            'sender_type' => $user->office_type,
                            'receiver_id' => $data['receiver_id'],
                            'receiver_type' => $data['office_type'],
                            'user_id' => $user->id,
                            'office_type' => $user->office_type,
                            'office_id' => $user->office_id,
                            'customer_view' => $data['customer_view'],
                            'status' => $status ?? null,
                            'last_mile_delivery' => $last_mile_delivery
                        ]);
                       
                    }
                }
            }
        }
    }

    public function updateStatusOfIncomingManifest($manifest_number, $receiver_id){

        $manifest =  Manifest::where('manifest_number', '=', $manifest_number)
                      ->where('manifest_type', '=', 'I')
                      ->where('receiver_id', '=', $receiver_id)
                      ->update(['customer_view' => '0']);

    }

    public function getManifestStatus($manifest_number, $manifest_type){

        $count = Manifest::where('manifest_number', '=', $manifest_number)->count();

        if($manifest_type == "I"){
            $status = "Arrived to Hub";
        }
        if($manifest_type == "O"){
            $status = "In Transit";
        }
        // if($count == 0){
        //     $status = "Booked & Dispatched";
        // }
        return $status;
}

    public function getTotalRowCount(): int
    {
        return $this->total_row_count;
    }

    public function getAbsoluteRowCount(): int
    {
        return $this->absolute_row_count;
    }

    public function getOfficeDetails($br_code){
        $branch = Branch::where('code', '=', $br_code)->first();
        if($branch){
             $branch['office_type'] = $branch->branch_type;
             return $branch;
        }
        $franchisee = Franchisee::where('code', '=', $br_code)->first();
        if($franchisee){
            $franchisee['office_type'] = 'FR';
            return $franchisee;
        }
        return false;
    }
}
