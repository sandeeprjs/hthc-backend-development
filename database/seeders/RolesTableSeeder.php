<?php

namespace Database\Seeders;

use App\Role;
use App\Module;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            [
                'name' => 'Administrator',
                'description' => 'Full access to all features and modules'
            ],
            [
                'name' => 'Branch Manager',
                'description' => 'Manage branch operations and staff'
            ],
            [
                'name' => 'Partner Manager',
                'description' => 'Manage partner relationships and accounts'
            ],
            [
                'name' => 'Delivery Person',
                'description' => 'Handle delivery-related tasks'
            ],
            [
                'name' => 'Booking Person',
                'description' => 'Handle booking-related tasks'
            ],
        ];

        // Seed roles
        foreach ($roles as $roleData) {
            // Update or create role
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Assign permissions if role is Administrator
            if ($role->name === 'Administrator') {
                $modules = Module::all();

                if ($modules->isNotEmpty()) {
                    foreach ($modules as $module) {
                        $role->permissions()->updateOrCreate(
                            ['module_id' => $module->id],
                            [
                                'enabled' => 1,
                                'create' => 1,
                                'read' => 1,
                                'update' => 1,
                                'delete' => 1
                            ]
                        );
                    }
                } else {
                    $this->command->warn("No modules found to assign permissions for Administrator.");
                }
            }
        }
    }
}
