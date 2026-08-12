<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            'Human Resource',
            'Information Technology',
            'Finance',
            'Trademark',
            'Patent',
            'Director',
            'Valuation',
            'Business Development',
            'Commercialization',
        ];

        foreach ($departments as $name) {
            Department::updateOrCreate(
                ['department_name' => $name]
            );
        }
    }
}