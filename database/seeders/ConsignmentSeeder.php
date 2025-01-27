<?php

namespace Database\Seeders;

use App\Consignment;
use Illuminate\Database\Seeder;

class ConsignmentSeeder extends Seeder
{
    public function run()
    {
        Consignment::insert([
            [
                'consg_number' => 'CN001',
                'office_type' => 'Branch',
                'office_id' => 1, // Ensure this branch ID exists
                'batch_id' => 1, // Replace with a valid batch ID
                'sheet_id' => 'SHEET001', // Assuming sheet_id is required
                'expiry_date' => now()->addDays(30), // Example expiry date
                'used' => 0, // Marked as not used
                'status' => 'In Transit',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'consg_number' => 'CN002',
                'office_type' => 'Branch',
                'office_id' => 2, // Ensure this branch ID exists
                'batch_id' => 2, // Replace with a valid batch ID
                'sheet_id' => 'SHEET002', // Assuming sheet_id is required
                'expiry_date' => now()->addDays(30), // Example expiry date
                'used' => 1, // Marked as used
                'status' => 'Dispatched',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
