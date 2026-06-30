<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Migrate any existing 'Review' tickets to 'Open' before tightening the enum
        DB::statement("UPDATE incidents SET status = 'Open' WHERE status = 'Review'");

        DB::statement("ALTER TABLE incidents MODIFY status ENUM('Open','In Pending','Resolved') DEFAULT 'Open'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE incidents MODIFY status ENUM('Open','In Pending','Resolved','Review') DEFAULT 'Open'");
    }
};