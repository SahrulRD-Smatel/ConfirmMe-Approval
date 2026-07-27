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
        // MODIFY COLUMN is dependent on DB driver. For simplicity and robustness in raw SQL for MySQL:
        DB::statement("ALTER TABLE leave_requests MODIFY COLUMN status ENUM('pending', 'pending_hr', 'approved', 'rejected') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting might lose data if there are 'pending_hr' rows, but for structure:
        DB::statement("ALTER TABLE leave_requests MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'");
    }
};
