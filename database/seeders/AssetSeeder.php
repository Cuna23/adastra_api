<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetCategory;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $laptop = AssetCategory::where('name', 'Laptop')->first();

        Asset::updateOrCreate([
            'brand' => 'Dell',
            'model' => 'Latitude 5440',
            'category_id' => $laptop->id,
            'status' => 'Available',
            'asset_tag' => 'AST0001',
            'serial_number' => 'DL5440001',
            'assigned_to' => 'IT Department',
        ]);

        Asset::updateOrCreate([
            'brand' => 'HP',
            'model' => 'ProBook 450',
            'category_id' => $laptop->id,
            'status' => 'Assigned',
            'asset_tag' => 'ASB0003',
            'serial_number' => 'HP450002',
            'assigned_to' => 'John Doe',
        ]);
    }
}