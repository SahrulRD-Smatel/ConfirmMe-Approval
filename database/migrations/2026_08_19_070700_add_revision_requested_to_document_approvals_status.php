<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE `document_approvals` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'skipped', 'revision_requested') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE `document_approvals` MODIFY COLUMN `status` ENUM('pending', 'approved', 'rejected', 'skipped') NOT NULL DEFAULT 'pending'");
    }
};
