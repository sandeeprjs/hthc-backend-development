<?php

namespace Database\Seeders;

use App\Booking;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class BookingSeeder extends Seeder
{
    public function run()
    {
        $bookings = [
            [
                'consg_number' => 'CN123456',
                'customer_id' => 1,
                'subscription_id' => 1, // Standard Delivery
                'status' => 'Booked',
                'weight' => 2.5,
                'pincode_id' => 1, // Koramangala
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'consg_number' => 'CN123457',
                'customer_id' => 2,
                'subscription_id' => 2, // Express Delivery
                'status' => 'Dispatched',
                'weight' => 1.2,
                'pincode_id' => 2, // Whitefield
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'consg_number' => 'CN123458',
                'customer_id' => 3,
                'subscription_id' => 3, // Overnight Delivery
                'status' => 'In Transit',
                'weight' => 3.0,
                'pincode_id' => 3, // Jayanagar
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'consg_number' => 'CN123459',
                'customer_id' => 4,
                'subscription_id' => 1,
                'status' => 'Delivered',
                'weight' => 1.8,
                'pincode_id' => 4, // Indiranagar
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($bookings as $data) {
            Booking::updateOrCreate(
                ['consg_number' => $data['consg_number']],
                $data
            );
        }
    }
}
