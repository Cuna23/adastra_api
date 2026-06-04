<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Software',
            'Computer',
            'Network Device',
            'Phone',
            'Printer',
            'Monitor',
        ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate([
                'name' => $category,
            ]);
        }
    }
}