<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Add revision fields and PO internal/external support to documents table.
     * Also updates the status ENUM to include 'revision' and 'purchasing_review'.
     */
    public function up(): void
    {
        // 1. Update status ENUM to include new statuses
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'revision',
            'supervisor_review',
            'manager_review',
            'hrd_review',
            'hrd_coordinator_review',
            'purchasing_review',
            'director_review',
            'finance_review',
            'presiden_review',
            'waiting_disbursement',
            'approved',
            'rejected',
            'completed',
            'paid'
        ) DEFAULT 'draft'");

        // 2. Add revision fields
        Schema::table('documents', function (Blueprint $table) {
            $table->text('revision_notes')->nullable()->after('rejection_reason');
            $table->unsignedInteger('revision_count')->default(0)->after('revision_notes');
            $table->decimal('original_budget', 15, 2)->nullable()->after('revision_count');
            $table->unsignedBigInteger('revision_requested_by')->nullable()->after('original_budget');
            $table->timestamp('revision_requested_at')->nullable()->after('revision_requested_by');

            // PO Internal/External
            $table->enum('po_category', ['internal', 'external'])->default('external')->after('approval_type');

            // Foreign key
            $table->foreign('revision_requested_by')->references('id')->on('users')->nullOnDelete();
        });

        // 3. Add new approval statuses to the reference table (if exists)
        if (Schema::hasTable('approval_statuses')) {
            DB::table('approval_statuses')->insertOrIgnore([
                [
                    'code' => 'revision',
                    'label' => 'Perlu Direvisi',
                    'color' => 'orange',
                    'order' => 8,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'purchasing_review',
                    'label' => 'Review Purchasing',
                    'color' => 'yellow',
                    'order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'code' => 'hrd_coordinator_review',
                    'label' => 'Review HRD Coordinator',
                    'color' => 'yellow',
                    'order' => 4,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['revision_requested_by']);
            $table->dropColumn([
                'revision_notes',
                'revision_count',
                'original_budget',
                'revision_requested_by',
                'revision_requested_at',
                'po_category',
            ]);
        });

        // Revert status ENUM
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'supervisor_review',
            'manager_review',
            'hrd_review',
            'director_review',
            'finance_review',
            'presiden_review',
            'waiting_disbursement',
            'approved',
            'rejected',
            'completed',
            'paid'
        ) DEFAULT 'draft'");

        if (Schema::hasTable('approval_statuses')) {
            DB::table('approval_statuses')->whereIn('code', ['revision', 'purchasing_review', 'hrd_coordinator_review'])->delete();
        }
    }
};
