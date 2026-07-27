<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company', function (Blueprint $table) {
            $table->id();
            $table->string('type');                    // 'org_chart', 'floor_map', 'vision', 'mission', 'about' (future)
            $table->string('title')->nullable();        // e.g. "Level 3" for floor_map, null for org_chart
            $table->string('image_path')->nullable();    // nullable — future types (vision/mission) might be text-only
            $table->longText('content')->nullable();     // future-proof: text content for vision/mission/about
            $table->integer('sort_order')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company');
    }
};
