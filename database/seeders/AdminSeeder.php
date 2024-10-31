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
                'email' => 'sudhilal@netiapps.com',
                'password' => 'dEvOps1234$$',
                'office_type' => 'HO',
                'office_id' => 1,
            ],
        ];

        $role = Role::updateOrCreate(
            ['name' => 'administrator'],
            [
                'name' => 'administrator',
                'description' => 'Super User, having access to all sites.'
            ]
        );

        foreach ($adminDetails as $adminDetail) {
            $admin = User::where('email', $adminDetail['email'])->first();

            if (!$admin) {
                $admin = User::factory()->create([
                    'first_name' => $adminDetail['first_name'],
                    'last_name' => $adminDetail['last_name'],
                    'email' => $adminDetail['email'],
                    'username' => 'HO001',
                    'password' => Hash::make($adminDetail['password']),
                    'office_type' => $adminDetail['office_type'],
                    'office_id' => $adminDetail['office_id']
                ]);

                UserRole::create([
                    'user_id' => $admin->id,
                    'role_id' => $role->id
                ]);
            }
        }
    }
}
