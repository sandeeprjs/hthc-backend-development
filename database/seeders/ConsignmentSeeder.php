<?php

namespace Database\Seeders;

use App\Consignment;
use Illuminate\Database\Seeder;

class ConsignmentSeeder extends Seeder
{
    public function run()
    {
        $consignments = [
            ['consg_number' => 'CN123456'],
            ['consg_number' => 'CN123457'],
            ['consg_number' => 'CN123458'],
            ['consg_number' => 'CN123459'],
        ];

        foreach ($consignments as $data) {
            Consignment::updateOrCreate(
                ['consg_number' => $data['consg_number']],
                $data
            );
        }
    }
}
