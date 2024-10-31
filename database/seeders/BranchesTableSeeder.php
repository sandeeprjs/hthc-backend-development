<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Branch; // Ensure this model exists and is correctly referenced

class BranchesTableSeeder extends Seeder
{
    public function run()
    {
        Branch::insert([
            [
                'name' => 'Central Branch',
                'location' => 'Main Street, City Center',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'North Branch',
                'location' => 'North Avenue, Uptown',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
