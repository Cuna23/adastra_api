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
                'emp_id' => 'EMP-SA01',  
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
                'emp_id' => 'EMP-AD01',  
                'status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'Ainin@adastra.com.my'],
            [
                'name' => 'Ainin',
                'password' => Hash::make('password123'),
                'role' => 'staff',
                'department_id' => 2,
                'emp_id' => 'EMP-0101',    
                'status' => 'active',
            ]
        );
    }
}