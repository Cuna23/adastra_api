<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->string('sr_number')->unique();
            $table->string('request_title');
            $table->enum('request_type', [
                'asset_request',
                'software_installation',
                'account_access',
                'other',
            ]);
            $table->string('category'); // depends on request_type, e.g. Laptop, MS Office, VPN Access
            $table->unsignedInteger('quantity')->default(1);
            $table->enum('priority', ['low', 'medium', 'high']);
            $table->text('description');
            $table->date('needed_by_date');
            $table->string('attachment')->nullable();
            $table->string('attachment_name')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
