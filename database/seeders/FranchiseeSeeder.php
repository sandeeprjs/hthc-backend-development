<?php

namespace Database\Seeders;

use App\Franchisee;
use Illuminate\Database\Seeder;

class FranchiseeSeeder extends Seeder
{
    public function run()
    {
        Franchisee::insert([
            [
                'branch_id' => 1, // Ensure this corresponds to an existing branch ID
                'code' => 'F001',
                'enterprise_name' => 'Koramangala Franchise',
                'contact_person_name' => 'Rajesh Kumar',
                'contact_person_gender' => 'Male',
                'contact_person_mobile' => '9876543210',
                'contact_person_email' => 'rajesh.kumar@example.com',
                'mobile_number' => '9876543210',
                'email' => 'koramangala.franchise@example.com',
                'add_line_1' => 'Koramangala Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'pincode_id' => 1, // Ensure this matches a valid pincode ID
                'active' => 1,
                'opening_time' => '09:00:00',
                'closing_time' => '18:00:00',
                'remarks' => 'Key franchise for Koramangala area',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'branch_id' => 2, // Ensure this corresponds to an existing branch ID
                'code' => 'F002',
                'enterprise_name' => 'Whitefield Franchise',
                'contact_person_name' => 'Suresh Reddy',
                'contact_person_gender' => 'Male',
                'contact_person_mobile' => '9876543211',
                'contact_person_email' => 'suresh.reddy@example.com',
                'mobile_number' => '9876543211',
                'email' => 'whitefield.franchise@example.com',
                'add_line_1' => 'Whitefield Main Road',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'pincode_id' => 2, // Ensure this matches a valid pincode ID
                'active' => 1,
                'opening_time' => '09:00:00',
                'closing_time' => '18:00:00',
                'remarks' => 'Key franchise for Whitefield area',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
