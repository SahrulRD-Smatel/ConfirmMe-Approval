<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Update leave_requests for 4-step approval with auto-skip hierarchy.
     * 
     * New flow: Atasan langsung → Manager → Head/GM (HRD Coordinator) → HR Manager (final)
     * Auto-skip: Higher level requesters skip lower approval steps automatically.
     */
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->unsignedInteger('current_step')->default(1)->after('status');
            $table->unsignedBigInteger('current_approver_id')->nullable()->after('current_step');

            $table->foreign('current_approver_id')->references('id')->on('users')->nullOnDelete();
        });

        // Update status enum via raw SQL (MySQL ENUM modification)
        DB::statement("ALTER TABLE leave_requests MODIFY COLUMN status ENUM(
            'pending',
            'pending_supervisor',
            'pending_manager',
            'pending_hrd',
            'pending_hr',
            'pending_hr_final',
            'approved',
            'rejected'
        ) DEFAULT 'pending'");

        // Migrate existing 'pending_hr' data to match the new enum values (already compatible)
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropForeign(['current_approver_id']);
            $table->dropColumn(['current_step', 'current_approver_id']);
        });

        DB::statement("ALTER TABLE leave_requests MODIFY COLUMN status ENUM(
            'pending',
            'pending_hr',
            'approved',
            'rejected'
        ) DEFAULT 'pending'");
    }
};
