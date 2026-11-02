<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Super Admin
        User::updateOrCreate(
            ['email' => 'admin@gurabicha.com'],
            [
                'name' => 'Gura Bicha Super Admin',
                'password' => Hash::make('password'),
                'role' => User::ROLE_SUPER_ADMIN,
                'must_change_password' => false,
                'status' => 'ACTIVE',
            ]
        );

        // 2. Manager
        User::updateOrCreate(
            ['email' => 'manager@gurabicha.com'],
            [
                'name' => 'Dawit Venue Manager',
                'password' => Hash::make('password'),
                'role' => User::ROLE_MANAGER,
                'must_change_password' => false,
                'status' => 'ACTIVE',
            ]
        );

        // 3. Staff member (with force password change flag enabled!)
        User::updateOrCreate(
            ['email' => 'staff@gurabicha.com'],
            [
                'name' => 'Selam Floor Staff',
                'password' => Hash::make('password'),
                'role' => User::ROLE_STAFF,
                'must_change_password' => true,
                'status' => 'ACTIVE',
            ]
        );
    }
}
