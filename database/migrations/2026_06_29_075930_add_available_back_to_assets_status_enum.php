<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE assets
            MODIFY status ENUM(
                'Pending',
                'Available',
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
                'In Process',
                'Resolved',
                'Maintenance',
                'Disposed'
            ) NOT NULL DEFAULT 'Pending'
        ");
    }
};