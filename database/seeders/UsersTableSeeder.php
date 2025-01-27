<?php

namespace Database\Seeders;

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        User::insert([
            [
                'username' => 'admin',
                'first_name' => 'System',
                'last_name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'user_type' => 'Admin',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'username' => 'john_doe',
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'john.doe@example.com',
                'password' => bcrypt('password123'),
                'user_type' => 'User',
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'username' => 'jane_doe',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'email' => 'jane.doe@example.com',
                'password' => bcrypt('password123'),
                'user_type' => 'User',
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
