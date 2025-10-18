<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        // User (pembeli)
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Pembeli',
                'password' => bcrypt('user123'),
                'role' => 'user',
            ]
        );

        // Dummy user pembanding
        User::updateOrCreate(
            ['email' => 'dummy@example.com'],
            [
                'name' => 'User Dummy',
                'password' => bcrypt('dummy123'),
                'role' => 'user',
            ]
        );
    }
}
