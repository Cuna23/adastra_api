<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

        // remove
        $table->dropColumn('email_verified_at');

        // add
        $table->string('microsoft_id')->nullable();

        $table->foreignId('department_id')
            ->nullable()
            ->constrained('departments')
            ->nullOnDelete();

        $table->string('status')
            ->default('active');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
