<?php

namespace Database\Seeders;

use App\Pincode;
use Illuminate\Database\Seeder;

class PincodeSeeder extends Seeder
{
    public function run()
    {
        $pincodes = [
            ['pincode' => '560034', 'area_name' => 'Koramangala'],
            ['pincode' => '560066', 'area_name' => 'Whitefield'],
            ['pincode' => '560011', 'area_name' => 'Jayanagar'],
            ['pincode' => '560038', 'area_name' => 'Indiranagar'],
            ['pincode' => '560100', 'area_name' => 'Electronic City'],
        ];

        foreach ($pincodes as $data) {
            Pincode::updateOrCreate(
                ['pincode' => $data['pincode']],
                [
                    'area_name' => $data['area_name'],
                ]
            );
        }
    }
}
