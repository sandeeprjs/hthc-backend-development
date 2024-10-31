<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'johndoe@example.com',
                'password' => Hash::make('password123'),
                'username' => 'johndoe',
                'office_type' => 'HO',
                'office_id' => 1,
            ],
            [
                'first_name' => 'Jane',
                'last_name' => 'Smith',
                'email' => 'janesmith@example.com',
                'password' => Hash::make('password123'),
                'username' => 'janesmith',
                'office_type' => 'Branch',
                'office_id' => 2,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'password' => $userData['password'],
                    'username' => $userData['username'],
                    'office_type' => $userData['office_type'],
                    'office_id' => $userData['office_id'],
                ]
            );
        }
    }
}
