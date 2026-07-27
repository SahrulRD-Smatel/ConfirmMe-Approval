<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('loan_fund_settings', function (Blueprint $table) {
            $table->integer('fiscal_year')->after('id');
            $table->decimal('total_fund', 15, 2)->default(0)->after('fiscal_year');
            $table->decimal('used_fund', 15, 2)->default(0)->after('total_fund');
            $table->decimal('reserved_fund', 15, 2)->default(0)->after('used_fund');
            $table->decimal('max_loan_per_employee', 15, 2)->default(0)->after('reserved_fund');
            $table->integer('max_installment_months')->default(12)->after('max_loan_per_employee');
            $table->boolean('is_active')->default(true)->after('max_installment_months');
            $table->text('notes')->nullable()->after('is_active');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete()->after('notes');

            $table->unique(['fiscal_year'], 'loan_fund_settings_fiscal_year_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loan_fund_settings', function (Blueprint $table) {
            $table->dropUnique('loan_fund_settings_fiscal_year_unique');
            $table->dropForeign(['updated_by']);
            $table->dropColumn([
                'fiscal_year', 'total_fund', 'used_fund', 'reserved_fund',
                'max_loan_per_employee', 'max_installment_months',
                'is_active', 'notes', 'updated_by',
            ]);
        });
    }
};
