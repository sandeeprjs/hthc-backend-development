<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminSeeder::class,
            BranchesTableSeeder::class,
            CountriesTableSeeder::class,
            FranchiseeSeeder::class,
            PincodeSeeder::class,
            CustomerSeeder::class,
            SubscriptionSeeder::class,
            BookingSeeder::class,
            DeliverySeeder::class,
            ConsignmentSeeder::class,
            ModulesTableSeeder::class,
            RolesTableSeeder::class,
            UsersTableSeeder::class,
        ]);
    }
}
