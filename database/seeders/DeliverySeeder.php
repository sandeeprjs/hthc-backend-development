<?php

namespace Database\Seeders;

use App\Delivery;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DeliverySeeder extends Seeder
{
    public function run()
    {
        $deliveries = [
            [
                'booking_id' => 1, // Link to Booking ID
                'receiver_name' => 'Arjun Rao',
                'add_line_1' => '123 Koramangala St',
                'city' => 'Bangalore',
                'pincode_id' => '2',
                'mobile_number' => '9876543210',
                'delivery_status' => 'Out for Delivery',
                'delivery_datetime' => Carbon::now()->subHours(4),
            ],
            [
                'booking_id' => 2,
                'receiver_name' => 'Smita Sharma',
                'add_line_1' => '456 Whitefield St',
                'city' => 'Bangalore',
                'pincode_id' => '2',
                'mobile_number' => '9876543211',
                'delivery_status' => 'In Transit',
                'delivery_datetime' => Carbon::now()->subHours(12),
            ],
            [
                'booking_id' => 3,
                'receiver_name' => 'Vijay Kumar',
                'add_line_1' => '789 Jayanagar St',
                'city' => 'Bangalore',
                'pincode_id' => '2',
                'mobile_number' => '9876543212',
                'delivery_status' => 'Delivered',
                'delivery_datetime' => Carbon::now()->subDay(),
            ],
            [
                'booking_id' => 4,
                'receiver_name' => 'Meera Singh',
                'add_line_1' => '101 Indiranagar St',
                'city' => 'Bangalore',
                'pincode_id' => '1',
                'mobile_number' => '9876543213',
                'delivery_status' => 'Delivered',
                'delivery_datetime' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($deliveries as $data) {
            Delivery::updateOrCreate(
                ['booking_id' => $data['booking_id']],
                $data
            );
        }
    }
}
