<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'superadmin@adastra.com.my'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password123'),
                'role' => 'super_admin',
                'department_id' => 2,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'admin@adastra.com.my'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'department_id' => 2,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'tm@adastra.com.my'],
            [
                'name' => 'TM Executive',
                'password' => Hash::make('password123'),
                'role' => 'trademark_executive',
                'department_id' => 4,
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'patent@adastra.com.my'],
            [
                'name' => 'Patent Executive',
                'password' => Hash::make('password123'),
                'role' => 'patent_executive',
                'department_id' => 5,
                'status' => 'inactive',
            ]
        );
    }
}