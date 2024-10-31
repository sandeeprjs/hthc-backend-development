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
                'description' => null
            ],
            [
                'name' => 'Branch Manager',
                'description' => null
            ],
            [
                'name' => 'Partner Manager',
                'description' => null
            ],
            [
                'name' => 'Delivery Person',
                'description' => null
            ],
            [
                'name' => 'Booking Person',
                'description' => null
            ],
        ];

        // Seed roles
        foreach ($roles as $roleData) {
            $role = Role::updateOrCreate(
                ['name' => $roleData['name']],
                ['description' => $roleData['description']]
            );

            // Assign permissions if Administrator
            if ($role->name === 'Administrator') {
                $modules = Module::all();

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
            }
        }
    }
}
