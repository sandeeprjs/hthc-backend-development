<?php

namespace Database\Seeders;

use App\Pincode;
use Illuminate\Database\Seeder;

class PincodeSeeder extends Seeder
{
    public function run()
    {
        $pincodes = [
            [
                'pincode' => '560034',
                'area_name' => 'Koramangala',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'serviceable' => 1,
                'status' => 'A',
            ],
            [
                'pincode' => '560066',
                'area_name' => 'Whitefield',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'serviceable' => 1,
                'status' => 'A',
            ],
            [
                'pincode' => '560011',
                'area_name' => 'Jayanagar',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'serviceable' => 1,
                'status' => 'A',
            ],
            [
                'pincode' => '560038',
                'area_name' => 'Indiranagar',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'serviceable' => 1,
                'status' => 'A',
            ],
            [
                'pincode' => '560100',
                'area_name' => 'Electronic City',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'country_id' => 1,
                'serviceable' => 1,
                'status' => 'A',
            ],
        ];

        foreach ($pincodes as $data) {
            Pincode::updateOrCreate(
                ['pincode' => $data['pincode']],
                [
                    'area_name' => $data['area_name'],
                    'city' => $data['city'],
                    'state' => $data['state'],
                    'country_id' => $data['country_id'],
                ]
            );
        }
    }
}
