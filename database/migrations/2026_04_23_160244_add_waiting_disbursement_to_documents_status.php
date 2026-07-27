<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Adds 'waiting_disbursement' to the status ENUM for loan documents
     * that have been fully approved and are awaiting fund disbursement by Finance.
     */
    public function up(): void
    {
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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'supervisor_review',
            'manager_review',
            'hrd_review',
            'director_review',
            'finance_review',
            'presiden_review',
            'approved',
            'rejected',
            'completed',
            'paid'
        ) DEFAULT 'draft'");
    }
};
