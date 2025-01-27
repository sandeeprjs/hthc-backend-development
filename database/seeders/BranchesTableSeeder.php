<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Branch;
use App\Country;
use App\Pincode;

class BranchesTableSeeder extends Seeder
{
    public function run()
    {
        // Fetch country and pincode references dynamically
        $india = Country::where('name', 'India')->first();
        $koramangalaPincode = Pincode::where('pincode', '560034')->first();
        $whitefieldPincode = Pincode::where('pincode', '560066')->first();

        Branch::insert([
            [
                'branch_name' => 'Central Branch',
                'branch_type' => 'Main',
                'code' => 'CB001',
                'location' => 'Main Street, City Center',
                'mobile_number' => '1234567890',
                'email' => 'central@example.com',
                'city' => 'City Center',
                'state' => 'Karnataka',
                'country_id' => $india ? $india->id : null,
                'pincode_id' => $koramangalaPincode ? $koramangalaPincode->id : null,
                'incharge_name' => 'John Doe',
                'latitude' => 12.971598,
                'longitude' => 77.594566,
                'active' => 1,
                'opening_time' => '09:00:00',
                'closing_time' => '18:00:00',
                'closing_days' => 'Sunday',
                'remarks' => 'Central operations hub',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'branch_name' => 'North Branch',
                'branch_type' => 'Secondary',
                'code' => 'NB001',
                'location' => 'North Avenue, Uptown',
                'mobile_number' => '0987654321',
                'email' => 'north@example.com',
                'city' => 'Uptown',
                'state' => 'Karnataka',
                'country_id' => $india ? $india->id : null,
                'pincode_id' => $whitefieldPincode ? $whitefieldPincode->id : null,
                'incharge_name' => 'Jane Smith',
                'latitude' => 13.0827,
                'longitude' => 80.2707,
                'active' => 1,
                'opening_time' => '09:00:00',
                'closing_time' => '18:00:00',
                'closing_days' => 'Saturday, Sunday',
                'remarks' => 'Regional operations hub',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
