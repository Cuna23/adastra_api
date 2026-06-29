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
                'department'    => 'IT',
                'approved_by'   => 'Ahmad Rahman',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'IT Department',
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
                'department'    => 'Finance',
                'approved_by'   => 'Nurul Huda',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'John Doe',
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
                'department'    => 'HR',
                'approved_by'   => 'Farid Iskandar',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Siti Aminah',
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
                'department'    => 'Administration',
                'approved_by'   => 'Roslan Karim',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Administration Department',
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
                'department'    => 'Sales',
                'approved_by'   => 'Azman Ali',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Farah Nabila',
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
                'department'    => 'IT',
                'approved_by'   => 'Ahmad Rahman',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Network Team',
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
                'department'    => 'Administration',
                'approved_by'   => 'Nurul Huda',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Admin Department',
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
                'department'    => 'Design',
                'approved_by'   => 'Roslan Karim',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Graphic Team',
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
                'department'    => 'Management',
                'approved_by'   => 'Farid Iskandar',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Management Team',
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
                'department'    => 'Operations',
                'approved_by'   => 'Ahmad Rahman',
                'purchased_by'  => 'Procurement Team',
                'assigned_to'   => 'Operations Team',
                'remark'        => 'Desktop workstation',
            ]
        );
    }
}