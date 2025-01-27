<?php

namespace Database\Seeders;

use App\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        Customer::insert([
            [
                'code' => 'C001',
                'customer_name' => 'Alpha Enterprises',
                'company_name' => 'Alpha Corp',
                'email' => 'contact@alpha.com',
                'mobile_number' => '9876543210',
                'add_line_1' => 'Koramangala Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'pincode_id' => 1,
                'subscription_id' => 1,
                'active' => 1,
                'remarks' => 'Key customer in Koramangala',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'code' => 'C002',
                'customer_name' => 'Beta Solutions',
                'company_name' => 'Beta Ltd',
                'email' => 'info@beta.com',
                'mobile_number' => '9876543211',
                'add_line_1' => 'Whitefield Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'pincode_id' => 2,
                'subscription_id' => 2,
                'active' => 1,
                'remarks' => 'Key customer in Whitefield',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
