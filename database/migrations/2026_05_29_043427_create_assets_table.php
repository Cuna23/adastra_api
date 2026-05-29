<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();

            $table->string('asset_tag')->unique();
            $table->string('serial_number')->nullable();
            $table->string('model')->nullable();

            // relation
            $table->foreignId('category_id')
                ->constrained('asset_categories')
                ->cascadeOnDelete();

            $table->enum('status', [
                'pending',
                'available',
                'under repair',
                'dispose'
            ])->default('pending');

            $table->string('assigned_to')->nullable();

            // checkin / checkout
            $table->enum('asset_flow', [
                'checkin',
                'checkout'
            ])->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
