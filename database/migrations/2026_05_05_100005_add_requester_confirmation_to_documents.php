<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Add requester confirmation fields
        Schema::table('documents', function (Blueprint $table) {
            $table->decimal('confirmed_amount', 15, 2)->nullable()->after('original_budget');
            $table->unsignedBigInteger('amount_changed_by')->nullable()->after('confirmed_amount');
            $table->timestamp('amount_changed_at')->nullable()->after('amount_changed_by');

            $table->foreign('amount_changed_by')->references('id')->on('users')->nullOnDelete();
        });

        // Add 'pending_requester_confirmation' to status ENUM
        // MySQL requires modifying the ENUM column to add new values
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft', 'pending',
            'supervisor_review', 'manager_review', 'hrd_review',
            'hrd_coordinator_review', 'purchasing_review',
            'director_review', 'finance_review', 'presiden_review',
            'waiting_disbursement',
            'revision',
            'pending_requester_confirmation',
            'approved', 'rejected', 'completed', 'paid'
        ) DEFAULT 'draft'");
    }

    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropForeign(['amount_changed_by']);
            $table->dropColumn(['confirmed_amount', 'amount_changed_by', 'amount_changed_at']);
        });

        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft', 'pending',
            'supervisor_review', 'manager_review', 'hrd_review',
            'hrd_coordinator_review', 'purchasing_review',
            'director_review', 'finance_review', 'presiden_review',
            'waiting_disbursement',
            'revision',
            'approved', 'rejected', 'completed', 'paid'
        ) DEFAULT 'draft'");
    }
};
