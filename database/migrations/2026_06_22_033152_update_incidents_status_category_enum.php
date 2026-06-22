<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normalize existing data dulu sebelum tukar enum constraint
        DB::statement("UPDATE incidents SET status = 'Open' WHERE status NOT IN ('Open','In Pending','Resolved','Review')");
        DB::statement("UPDATE incidents SET category = 'Others' WHERE category NOT IN ('Hardware','Software','Network','Others')");

        DB::statement("ALTER TABLE incidents MODIFY status ENUM('Open','In Pending','Resolved','Review') DEFAULT 'Open'");
        DB::statement("ALTER TABLE incidents MODIFY category ENUM('Hardware','Software','Network','Others')");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE incidents MODIFY status ENUM('Open','Assigned','In Progress','Pending User','Resolved','Closed','Rejected') DEFAULT 'Open'");
        DB::statement("ALTER TABLE incidents MODIFY category ENUM('Hardware','Software','Network','Email','Printer','Access/Login','Others')");
    }
};