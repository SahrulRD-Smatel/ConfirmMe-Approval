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
        Schema::create('employee_loans', function (Blueprint $table) {
            $table->id();
            $table->string('loan_number')->unique();
            $table->foreignId('document_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('users');
            $table->decimal('loan_amount', 15, 2);
            $table->integer('installment_months')->default(12);
            $table->decimal('monthly_installment', 15, 2);
            $table->decimal('total_paid', 15, 2)->default(0);
            $table->decimal('outstanding_amount', 15, 2);
            $table->string('status')->default('pending_disbursement');
            // Status: pending_disbursement, disbursed, repaying, paid_off, cancelled
            $table->date('disbursement_date')->nullable();
            $table->string('disbursement_proof')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('disbursed_by')->nullable()->constrained('users');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_loans');
    }
};
