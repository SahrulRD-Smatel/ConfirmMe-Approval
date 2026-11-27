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
        // 1. Announcements: cascade on delete when creator user is deleted
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                try {
                    $table->dropForeign(['created_by']);
                } catch (\Throwable $e) {}
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // 2. Leave Requests: cascade on user_id, null on approved_by
        if (Schema::hasTable('leave_requests')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                } catch (\Throwable $e) {}
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();

                try {
                    $table->dropForeign(['approved_by']);
                } catch (\Throwable $e) {}
                $table->foreign('approved_by')->references('id')->on('users')->nullOnDelete();
            });
        }

        // 3. Employee Loans: cascade on employee_id & created_by, null on disbursed_by
        if (Schema::hasTable('employee_loans')) {
            Schema::table('employee_loans', function (Blueprint $table) {
                try {
                    $table->dropForeign(['employee_id']);
                } catch (\Throwable $e) {}
                $table->foreign('employee_id')->references('id')->on('users')->cascadeOnDelete();

                try {
                    $table->dropForeign(['disbursed_by']);
                } catch (\Throwable $e) {}
                $table->foreign('disbursed_by')->references('id')->on('users')->nullOnDelete();

                try {
                    $table->dropForeign(['created_by']);
                } catch (\Throwable $e) {}
                $table->foreign('created_by')->references('id')->on('users')->cascadeOnDelete();
            });
        }

        // 4. Loan Repayments: cascade on recorded_by
        if (Schema::hasTable('loan_repayments')) {
            Schema::table('loan_repayments', function (Blueprint $table) {
                try {
                    $table->dropForeign(['recorded_by']);
                } catch (\Throwable $e) {}
                $table->foreign('recorded_by')->references('id')->on('users')->cascadeOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('announcements')) {
            Schema::table('announcements', function (Blueprint $table) {
                try {
                    $table->dropForeign(['created_by']);
                    $table->foreign('created_by')->references('id')->on('users');
                } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('leave_requests')) {
            Schema::table('leave_requests', function (Blueprint $table) {
                try {
                    $table->dropForeign(['user_id']);
                    $table->foreign('user_id')->references('id')->on('users');
                    $table->dropForeign(['approved_by']);
                    $table->foreign('approved_by')->references('id')->on('users');
                } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('employee_loans')) {
            Schema::table('employee_loans', function (Blueprint $table) {
                try {
                    $table->dropForeign(['employee_id']);
                    $table->foreign('employee_id')->references('id')->on('users');
                    $table->dropForeign(['disbursed_by']);
                    $table->foreign('disbursed_by')->references('id')->on('users');
                    $table->dropForeign(['created_by']);
                    $table->foreign('created_by')->references('id')->on('users');
                } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('loan_repayments')) {
            Schema::table('loan_repayments', function (Blueprint $table) {
                try {
                    $table->dropForeign(['recorded_by']);
                    $table->foreign('recorded_by')->references('id')->on('users');
                } catch (\Throwable $e) {}
            });
        }
    }
};
