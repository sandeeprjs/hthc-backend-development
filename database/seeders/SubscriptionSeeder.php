<?php

namespace Database\Seeders;

use App\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        Subscription::insert([
            [
                'name' => 'Basic Plan',
                'consg_type' => 'Standard',
                'price' => '500',
                'max_delivery_time' => '3 days',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Premium Plan',
                'consg_type' => 'Express',
                'price' => '1000',
                'max_delivery_time' => '1 day',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
