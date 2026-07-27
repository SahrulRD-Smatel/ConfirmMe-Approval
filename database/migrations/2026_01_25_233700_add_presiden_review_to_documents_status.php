<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // For MySQL, we need to alter the ENUM to include all values
        // Using raw SQL because Laravel doesn't support modifying ENUM easily
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to previous ENUM values if needed
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'supervisor_review',
            'manager_review',
            'hrd_review',
            'director_review',
            'finance_review',
            'approved',
            'rejected',
            'completed',
            'paid'
        ) DEFAULT 'draft'");
    }
};
