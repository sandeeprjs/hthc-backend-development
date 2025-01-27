<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModesTableSeeder extends Seeder
{
    public function run()
    {
        // Clear existing data without breaking foreign key constraints
        DB::table('modes')->delete();

        // Insert seed data into the 'modes' table
        DB::table('modes')->insert([
            [
                'code' => 'AIR',
                'name' => 'Air Transport',
                'type' => 'Air',
                'description' => 'Fastest mode for long distances.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'ROAD',
                'name' => 'Road Transport',
                'type' => 'Road',
                'description' => 'Most commonly used for regional delivery.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'SEA',
                'name' => 'Sea Transport',
                'type' => 'Sea',
                'description' => 'Cost-effective for large shipments over long distances.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'RAIL',
                'name' => 'Rail Transport',
                'type' => 'Rail',
                'description' => 'Efficient and cost-effective for bulk goods.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
