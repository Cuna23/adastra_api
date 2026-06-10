<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            $table->string('ticket_no')->unique();

            // Reporter
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('subject');

            $table->text('description');

            $table->enum('category', [
                'Hardware',
                'Software',
                'Network',
                'Email',
                'Printer',
                'Access/Login',
                'Others'
            ]);

            $table->enum('priority', [
                'Low',
                'Medium',
                'High',
                'Critical'
            ])->default('Medium');

            $table->string('attachment')->nullable();

            $table->enum('status', [
                'Open',
                'Assigned',
                'In Progress',
                'Pending User',
                'Resolved',
                'Closed',
                'Rejected'
            ])->default('Open');

            // IT Admin assigned
            $table->foreignId('assigned_to')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('Resolution')->nullable();

            // Dates
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};