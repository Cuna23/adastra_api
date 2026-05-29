<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            $table->foreignId('category_id')
                ->constrained('asset_categories')
                ->cascadeOnDelete();

            $table->enum('status', [
                'pending',
                'available',
                'under repair',
                'dispose'
            ])->default('pending');

            $table->string('asset_tag')->unique();

            $table->string('serial_number')->nullable();

            $table->string('emp_id')->nullable();

            $table->string('department')->nullable();

            $table->string('approved_by')->nullable();

            $table->string('purchased_by')->nullable();

            $table->string('assigned_to')->nullable();

            $table->text('remark')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};