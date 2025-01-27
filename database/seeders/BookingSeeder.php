<?php

namespace Database\Seeders;

use App\Booking;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run()
    {
        Booking::insert([
            [
                'consg_number' => 'CN001',
                'consg_type' => 'Standard',
                'customer_id' => 1,
                'customer_name' => 'Alpha Enterprises',
                'mobile_number' => '9876543210',
                'add_line_1' => 'Koramangala Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 1,
                'country_id' => 1,
                'weight' => '10kg',
                'booking_status' => 'Booked',
                'booking_date' => now(),
                'booked_amount' => '500',
                'payment_mode' => 'Online',
                'remarks' => 'First booking',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'consg_number' => 'CN002',
                'consg_type' => 'Express',
                'customer_id' => 2,
                'customer_name' => 'Beta Solutions',
                'mobile_number' => '9876543211',
                'add_line_1' => 'Whitefield Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 2,
                'country_id' => 1,
                'weight' => '5kg',
                'booking_status' => 'Booked',
                'booking_date' => now(),
                'booked_amount' => '1000',
                'payment_mode' => 'Cash',
                'remarks' => 'Urgent delivery',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
