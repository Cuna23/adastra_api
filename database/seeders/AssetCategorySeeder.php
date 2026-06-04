<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetCategory;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Laptop',
            'Desktop',
            'Monitor',
            'Printer',
            'Network Device',
            'Server',
            'Software License',
            'Mobile Device',
            'Accessory',
        ];

        foreach ($categories as $category) {
            AssetCategory::updateOrCreate([
                'name' => $category,
            ]);
        }
    }
}