<?php

namespace Database\Seeders;

use App\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run()
    {
        $subscriptions = [
            ['name' => 'Standard Delivery', 'price' => 50],
            ['name' => 'Express Delivery', 'price' => 100],
            ['name' => 'Overnight Delivery', 'price' => 150],
        ];

        foreach ($subscriptions as $data) {
            Subscription::updateOrCreate(
                ['name' => $data['name']],
                [
                    'price' => $data['price'],
                ]
            );
        }
    }
}
