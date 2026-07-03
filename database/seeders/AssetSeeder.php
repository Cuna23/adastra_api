<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetCategory;

class AssetSeeder extends Seeder
{
    public function run(): void
    {
        $computer = AssetCategory::where('name', 'Computer')->first();

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0001'],
            [
                'brand'         => 'Dell',
                'model'         => 'Latitude 5440',
                'category_id'   => $computer->id,
                'status'        => 'Pending',
                'serial_number' => 'DL5440001',
                'emp_id'        => 'EMP001',
                'department'    => 'Information Technology', // [CHANGED] dari 'IT'
                'approved_by'   => 'Admin',
                'purchased_by'  => 'Admin',
                'assigned_to'   => 'Amin',
                'remark'        => 'New laptop for support team',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0002'],
            [
                'brand'         => 'HP',
                'model'         => 'ProBook 450 G10',
                'category_id'   => $computer->id,
                'status'        => 'Resolved',
                'serial_number' => 'HP450002',
                'emp_id'        => 'EMP002',
                'department'    => 'Finance', // [OK] dah match
                'approved_by'   => 'Super Admin',
                'purchased_by'  => 'Admin',
                'assigned_to'   => 'Iwani',
                'remark'        => 'Assigned to finance executive',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0003'],
            [
                'brand'         => 'Lenovo',
                'model'         => 'ThinkPad E14',
                'category_id'   => $computer->id,
                'status'        => 'Maintenance',
                'serial_number' => 'LNVE14003',
                'emp_id'        => 'EMP003',
                'department'    => 'Human Resource', // [CHANGED] dari 'HR'
                'approved_by'   => 'Super Admin',
                'purchased_by'  => 'Super Admin',
                'assigned_to'   => 'Abu',
                'remark'        => 'Under hardware maintenance',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0004'],
            [
                'brand'         => 'Acer',
                'model'         => 'TravelMate P2',
                'category_id'   => $computer->id,
                'status'        => 'Disposed',
                'serial_number' => 'ACP20004',
                'emp_id'        => 'EMP004',
                'department'    => 'Human Resource', // [CHANGED] dari 'Administration'
                'approved_by'   => 'Admin',
                'purchased_by'  => 'Ahmad',
                'assigned_to'   => 'Ainin',
                'remark'        => 'Disposed due to end-of-life',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0005'],
            [
                'brand'         => 'Samsung',
                'model'         => 'Galaxy A54',
                'category_id'   => AssetCategory::where('name', 'Phone')->first()->id,
                'status'        => 'In Process',
                'serial_number' => 'SAM54005',
                'emp_id'        => 'EMP005',
                'department'    => 'Trademark', // [CHANGED] dari 'Sales'
                'approved_by'   => 'Admin',
                'purchased_by'  => 'Super Admin',
                'assigned_to'   => 'Aminah',
                'remark'        => 'Company mobile device',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0006'],
            [
                'brand'         => 'Cisco',
                'model'         => 'Catalyst 9200',
                'category_id'   => AssetCategory::where('name', 'Network Device')->first()->id,
                'status'        => 'Maintenance',
                'serial_number' => 'CSC920006',
                'emp_id'        => 'EMP006',
                'department'    => 'Information Technology', // [CHANGED] dari 'IT'
                'approved_by'   => 'Super Admin',
                'purchased_by'  => 'Super Admin',
                'assigned_to'   => 'Admin',
                'remark'        => 'Firmware upgrade in progress',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0007'],
            [
                'brand'         => 'Brother',
                'model'         => 'HL-L8360CDW',
                'category_id'   => AssetCategory::where('name', 'Printer')->first()->id,
                'status'        => 'Resolved',
                'serial_number' => 'BRO836007',
                'emp_id'        => 'EMP007',
                'department'    => 'Human Resource', // [CHANGED] dari 'Administration'
                'approved_by'   => 'Super Admin',
                'purchased_by'  => 'Super Admin',
                'assigned_to'   => 'Ahmad',
                'remark'        => 'Color printer',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0008'],
            [
                'brand'         => 'Dell',
                'model'         => 'P2423D',
                'category_id'   => AssetCategory::where('name', 'Monitor')->first()->id,
                'status'        => 'Pending',
                'serial_number' => 'DEL242308',
                'emp_id'        => 'EMP008',
                'department'    => 'Trademark', // [CHANGED] dari 'Design'
                'approved_by'   => 'Admin',
                'purchased_by'  => 'Admin',
                'assigned_to'   => 'Siti',
                'remark'        => '24 inch monitor',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0009'],
            [
                'brand'         => 'Microsoft',
                'model'         => 'Office 365',
                'category_id'   => AssetCategory::where('name', 'Software')->first()->id,
                'status'        => 'In Process',
                'serial_number' => 'MS365009',
                'emp_id'        => 'EMP009',
                'department'    => 'Human Resource', // [CHANGED] dari 'Management'
                'approved_by'   => 'Admin',
                'purchased_by'  => 'Abu',
                'assigned_to'   => 'Aminah',
                'remark'        => 'Annual subscription',
            ]
        );

        Asset::updateOrCreate(
            ['asset_tag' => 'AST0010'],
            [
                'brand'         => 'Lenovo',
                'model'         => 'ThinkCentre M70',
                'category_id'   => AssetCategory::where('name', 'Computer')->first()->id,
                'status'        => 'Resolved',
                'serial_number' => 'LNV700010',
                'emp_id'        => 'EMP010',
                'department'    => 'Information Technology', // [CHANGED] dari 'Operations'
                'approved_by'   => 'Super Admin',
                'purchased_by'  => 'Super Admin',
                'assigned_to'   => 'Iwani',
                'remark'        => 'Desktop workstation',
            ]
        );
    }
}