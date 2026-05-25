<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        Department::insert([
            ['department_name' => 'Human Resource'],
            ['department_name' => 'Information Technology'],
            ['department_name' => 'Finance'],
            ['department_name' => 'Trademark'],
            ['department_name' => 'Patent'],
        ]);
    }
}