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
        // Add manager_review to the status enum
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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE documents MODIFY COLUMN status ENUM(
            'draft',
            'pending',
            'supervisor_review',
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
