<?php

namespace App\Imports;

use App\BulkBooking;
use App\Consignment;
use App\Country;
use App\Http\Helpers\AppHelper;
use App\Pincode;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BookingImport implements ToCollection, WithHeadingRow
{
    /**
     * @var
     */
    private $bookingData;
    private $customerId;
    private $batchId = 0;

    public function __construct($bookingData, $customerId = null)
    {
        $this->bookingData = $bookingData;
        $this->customerId = $customerId;
    }

    /**
     * @inheritDoc
     */
    public function collection(Collection $rows)
    {
//        AppHelper::createBulkBookingsTable();

        $user = Auth::user();
        $bookingData = $this->bookingData;
        $lastBatch = Consignment::select(['batch_id'])->latest('id')->first();

        if ($lastBatch) {
            $this->batchId = ++$lastBatch->batch_id;
        } else {
            $this->batchId = 1;
        }
        $bulkBookingIds = BulkBooking::where('batch_id', $this->batchId)->pluck('id');
        if ($bulkBookingIds) {
            BulkBooking::destroy($bulkBookingIds);
        }

        foreach ($rows as $row) {
            if($row->filter()->isNotEmpty()){
                $pincode = null;
                if (!empty($row['pincode'])) {
                    $pincode = Pincode::select('id')->where('pincode', $row['pincode'])->first();
                }
                $country = Country::select('id')->where('name', $row['country'] ?? 'india')->first();

                $bulk = BulkBooking::create([
                    'consg_number' => '',
                    'consg_type' => 'dox',
                    'batch_id' => $this->batchId,
                    'subscription_id' => $bookingData['subscription_id'],
                    'customer_id' => $this->customerId,
                    'customer_name' => $bookingData['sender_name'],
                    'gender' => '',
                    'mobile_number' => $bookingData['sender_mobile_number'],
                    'phone_number' => $bookingData['sender_phone_number'],
                    'email' => $bookingData['sender_email'],
                    'add_line_1' => $bookingData['sender_address'],
                    'add_line_2' => $bookingData['sender_area'],
                    'landmark' => $bookingData['sender_landmark'] ?? null,
                    'district' => $bookingData['sender_district'],
                    'city' => $bookingData['sender_city'],
                    'state' => $bookingData['sender_state'],
                    'pincode_id' => $bookingData['sender_pincode_id'],
                    'country_id' => $bookingData['sender_country'],
                    'weight' => $bookingData['captured_weight'],
                    'booked_amount' => $bookingData['booked_amount'],
                    'amount_due' => $bookingData['amount_due'] ?? null,
                    'insured' => $bookingData['insured'] ?? null,
                    'declared_consg_value' => $bookingData['declared_consg_value'],
                    'sms_to_sender' => $bookingData['sender_sms'] ?? null,
                    'sms_to_receiver' => $bookingData['receiver_sms'] ?? null,
                    'origin_office_type' => $bookingData['origin_office_type'],
                    'origin_office_id' => $bookingData['fr_id'] ?? $user->office_id,

                    //receiver
                    'receiver_name' => $row['name'],
                    'receiver_add_line_1' => $row['add_1'] . ', ' . $row['add_2'],
                    'receiver_add_line_2' => $row['add_3'] . ', ' . $row['add_4'],
                    'receiver_district' => null,
                    'receiver_city' => $row['city'] ?? null,
                    'receiver_state' => $row['state'] ?? null,
                    'receiver_pincode_id' => $pincode->id ?? null,
                    'receiver_country_id' => $country->id ?? null,
                    'receiver_mobile_number' => $row['ph_no'] ?? null,
                    'receiver_email' => $row['email'] ?? null
                ]);
                if (!$pincode) {
                    $bulk->wrong_pincode = $row['pincode'];
                    $bulk->has_error = 1;
                    $bulk->save();
                } else {
                    $bulk->has_error = 0;
                    $bulk->save();
                }

            }
        }
    }

//    /**
//     * @inheritDoc
//     */
    public function getBatchId(): int
    {
        return $this->batchId;
    }
}
