<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Announcements Table
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('type', ['info', 'warning', 'urgent', 'danger'])->default('info');
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Leave Requests Table
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->enum('type', ['annual', 'sick', 'unpaid', 'other'])->default('annual');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->string('attachment')->nullable(); // For sick leave doctor's note
            $table->timestamps();
            $table->softDeletes();
        });

        // 3. Update Users Table (Add Leave Balances)
        Schema::table('users', function (Blueprint $table) {
            $table->integer('annual_leave_balance')->default(12)->after('is_active');
            $table->integer('sick_leave_used')->default(0)->after('annual_leave_balance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse User Changes
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['annual_leave_balance', 'sick_leave_used']);
        });

        // Drop Tables
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('announcements');
    }
};
