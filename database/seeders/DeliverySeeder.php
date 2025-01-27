<?php

namespace Database\Seeders;

use App\Delivery;
use Illuminate\Database\Seeder;

class DeliverySeeder extends Seeder
{
    public function run()
    {
        Delivery::insert([
            [
                'booking_id' => 1,
                'receiver_name' => 'Ravi Kumar',
                'add_line_1' => 'Koramangala Main Road',
                'add_line_2' => null, // Add null if the column exists but is optional
                'district' => null, // Add null if the column exists but is optional
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 1,
                'country_id' => 1,
                'mobile_number' => '9876543212',
                'phone_number' => null, // Add null for optional columns
                'email' => null,
                'office_type' => null,
                'office_id' => null,
                'remarks' => 'Delivered on time',
                'delivery_status' => 'Delivered',
                'delivery_datetime' => now(),
                'delivery_user_id' => 1,
                'no_of_attempts' => 1,
                'no_of_pieces' => 1,
                'penalty' => null,
                'tookstatus' => null,
                'rec_name' => null,
                'actual_delivery_charge' => null,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'booking_id' => 2,
                'receiver_name' => 'Anita Sharma',
                'add_line_1' => 'Whitefield Main Road',
                'add_line_2' => null,
                'district' => null,
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 2,
                'country_id' => 1,
                'mobile_number' => '9876543213',
                'phone_number' => null,
                'email' => null,
                'office_type' => null,
                'office_id' => null,
                'remarks' => 'Delivery scheduled for tomorrow',
                'delivery_status' => 'Pending',
                'delivery_datetime' => null,
                'delivery_user_id' => null,
                'no_of_attempts' => 0,
                'no_of_pieces' => 1,
                'penalty' => null,
                'tookstatus' => null,
                'rec_name' => null,
                'actual_delivery_charge' => null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
