<?php

namespace Database\Seeders;

use App\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $customers = [
            [
                'code' => 'CUST001',
                'customer_name' => 'John Doe',
                'company_name' => 'Doe Logistics',
                'add_line_1' => '123 Koramangala St',
                'city' => 'Bangalore',
                'state' => 'Karnataka',

                'pincode_id' => 1,
                'email' => 'john.doe@example.com',
                'mobile_number' => '9876543210',
                'subscription_id' => 1,
                'active' => true,
                'remarks' => 'Preferred customer in Koramangala region',
            ],
            [
                'code' => 'CUST002',
                'customer_name' => 'Jane Smith',
                'company_name' => 'Smith Enterprises',
                'add_line_1' => '456 Whitefield St',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 2,
                'email' => 'jane.smith@example.com',
                'mobile_number' => '9876543211',
                'subscription_id' => 2,
                'active' => true,
                'remarks' => 'Regular customer in Whitefield',
            ],
            [
                'code' => 'CUST003',
                'customer_name' => 'Raj Kumar',
                'company_name' => 'Kumar Imports',
                'add_line_1' => '789 Jayanagar St',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 3,
                'email' => 'raj.kumar@example.com',
                'mobile_number' => '9876543212',
                'subscription_id' => 1,
                'active' => false,
                'remarks' => 'Inactive customer, used only during peak season',
            ],
            [
                'code' => 'CUST004',
                'customer_name' => 'Priya Patel',
                'company_name' => 'Patel Distributors',
                'add_line_1' => '101 Indiranagar St',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'pincode_id' => 4,
                'email' => 'priya.patel@example.com',
                'mobile_number' => '9876543213',
                'subscription_id' => 2,
                'active' => true,
                'remarks' => 'VIP customer in Indiranagar',
            ],
        ];

        foreach ($customers as $data) {
            Customer::updateOrCreate(
                ['code' => $data['code']],
                $data
            );
        }
    }
}
