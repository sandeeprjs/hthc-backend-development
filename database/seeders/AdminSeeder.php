<?php

namespace Database\Seeders;

use App\User;
use App\Role;
use App\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $adminDetails = [
            [
                'first_name' => 'HTHC',
                'last_name' => 'Admin',
                'email' => 'admin@hthc.com',
                'username' => 'ADMIN001',
                'password' => 'Admin@1234',
                'office_type' => 'HO',
                'office_id' => 1,
            ],
            [
                'first_name' => 'Netiapps',
                'last_name' => 'Support',
                'email' => 'support@netiapps.com',
                'username' => 'ADMIN002',
                'password' => 'Support@1234',
                'office_type' => 'HO',
                'office_id' => 2,
            ],
            [
                'first_name' => 'Netiapps',
                'last_name' => 'Support',
                'email' => 'support@netiapps.com',
                'username' => 'ADMIN002',
                'password' => 'Support@1234',
                'office_type' => 'HO',
                'office_id' => 2,
            ],
            [
                'first_name' => 'HTHC',
                'last_name' => 'Admin',
                'email' => 'admin@hthc.com',
                'username' => 'ADMIN001',
                'password' => 'Admin@1234',
                'office_type' => 'HO',
                'office_id' => 3, // H0001
            ],
            [
                'first_name' => 'Netiapps',
                'last_name' => 'Support',
                'email' => 'support@netiapps.com',
                'username' => 'ADMIN002',
                'password' => 'Support@1234',
                'office_type' => 'HO',
                'office_id' => 3, // H0001
            ],
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'centraladmin@example.com',
                'username' => 'CB001_ADMIN',
                'password' => 'Central@123',
                'office_type' => 'Main',
                'office_id' => 1, // CB001
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'northadmin@example.com',
                'username' => 'NB001_ADMIN',
                'password' => 'North@123',
                'office_type' => 'Secondary',
                'office_id' => 2, // NB001
            ],
        ];

        // Ensure the administrator role exists
        $role = Role::updateOrCreate(
            ['name' => 'administrator'],
            [
                'description' => 'Super User, having access to all sites.',
            ]
        );

        foreach ($adminDetails as $adminDetail) {
            // Find or create the admin user
            $admin = User::updateOrCreate(
                ['email' => $adminDetail['email']],
                [
                    'first_name' => $adminDetail['first_name'],
                    'last_name' => $adminDetail['last_name'],
                    'username' => $adminDetail['username'],
                    'password' => Hash::make($adminDetail['password']),
                    'office_type' => $adminDetail['office_type'],
                    'office_id' => $adminDetail['office_id'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );

            // Assign the administrator role to the user
            UserRole::updateOrCreate(
                ['user_id' => $admin->id, 'role_id' => $role->id],
                []
            );
        }
    }
}
