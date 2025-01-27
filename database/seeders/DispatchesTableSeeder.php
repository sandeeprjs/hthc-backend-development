<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DispatchesTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('dispatches')->insert([
            [
                'dispatch_code' => 'DISP0001',
                'org_office_type' => 'HO',
                'consg_number' => 'CN001',
                'org_office_id' => 1,
                'dest_office_type' => 'BR',
                'dest_office_id' => '2',
                'subscription_id' => 101,
                'mode_id' => 1,
                'vehicle_id' => 1,
                'vehicle_number' => 'KA01AB1234',
                'load_sender_user_id' => 1,
                'departure_datetime' => Carbon::now(),
                'arrival_datetime' => Carbon::now()->addHours(5),
                'baggage_cost' => '500',
                'baggage_weight' => '20kg',
                'trip_sheet_number' => 'TS001',
                'bag_manifest_number' => 'BM001',
                'length' => '100',
                'breadth' => '50',
                'height' => '30',
                'status' => 'In Transit',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'dispatch_code' => 'DISP0002',
                'org_office_type' => 'BR',
                'consg_number' => 'CN002',
                'org_office_id' => 2,
                'dest_office_type' => 'FR',
                'dest_office_id' => '3',
                'subscription_id' => 102,
                'mode_id' => 2,
                'vehicle_id' => 2,
                'vehicle_number' => 'KA02CD5678',
                'load_sender_user_id' => 2,
                'departure_datetime' => Carbon::now(),
                'arrival_datetime' => Carbon::now()->addHours(8),
                'baggage_cost' => '1000',
                'baggage_weight' => '30kg',
                'trip_sheet_number' => 'TS002',
                'bag_manifest_number' => 'BM002',
                'length' => '120',
                'breadth' => '60',
                'height' => '40',
                'status' => 'Dispatched',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
