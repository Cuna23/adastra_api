<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Update existing rows that used the removed 'Available' status
        DB::table('assets')->where('status', 'Available')->update(['status' => 'Pending']);

        DB::statement("
            ALTER TABLE assets
            MODIFY status ENUM(
                'Pending',
                'In Process',
                'Resolved',
                'Maintenance',
                'Disposed'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE assets
            MODIFY status ENUM(
                'Pending',
                'Available',
                'Maintenance',
                'Disposed'
            ) NOT NULL
        ");
    }
};