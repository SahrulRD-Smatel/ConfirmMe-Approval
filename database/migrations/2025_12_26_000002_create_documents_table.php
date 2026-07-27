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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('approval_type', ['only', 'po', 'invoice', 'bulk', 'loan'])->default('only');
            $table->enum('status', [
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
            ])->default('draft');
            $table->decimal('estimated_budget', 15, 2)->nullable();
            $table->decimal('approved_budget', 15, 2)->nullable();
            $table->string('priority')->default('normal'); // low, normal, high, urgent
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // requester
            $table->foreignId('current_approver_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('department')->nullable();
            $table->string('template')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('approval_hash')->nullable(); // for QR code validation
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
