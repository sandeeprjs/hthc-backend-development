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
            RolesTableSeeder::class,
            ModulesTableSeeder::class,
            CountriesTableSeeder::class,
            PincodeSeeder::class,
            BranchesTableSeeder::class,
            UsersTableSeeder::class,
            FranchiseeSeeder::class,
            CustomerSeeder::class,
            SubscriptionSeeder::class,
            BookingSeeder::class,
            DeliverySeeder::class,
            ConsignmentSeeder::class,
            ModesTableSeeder::class,
//            LargeDataSeeder::class,
        ]);
    }
}
