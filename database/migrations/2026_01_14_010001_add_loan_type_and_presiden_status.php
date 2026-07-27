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
        // For SQLite, we need to recreate the table to modify enum
        // For MySQL, we can use DB::statement to modify enum

        // First, add the new status 'presiden_review' and 'manager_review' if not exists
        // Since SQLite doesn't support enum, we'll just ensure the model accepts these values

        // For documents table - update approval_type to include 'loan'
        // We'll handle this at model level since SQLite doesn't support enum modification

        // Create a status mapping table for cleaner management
        Schema::create('approval_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->string('color')->default('gray');
            $table->integer('order')->default(0);
            $table->timestamps();
        });

        // Seed the statuses
        DB::table('approval_statuses')->insert([
            ['code' => 'draft', 'label' => 'Draft', 'color' => 'gray', 'order' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'pending', 'label' => 'Menunggu Review', 'color' => 'yellow', 'order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'supervisor_review', 'label' => 'Review Supervisor', 'color' => 'yellow', 'order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'manager_review', 'label' => 'Review Manager', 'color' => 'yellow', 'order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'hrd_review', 'label' => 'Review HRD', 'color' => 'yellow', 'order' => 4, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'finance_review', 'label' => 'Review Finance', 'color' => 'yellow', 'order' => 5, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'director_review', 'label' => 'Review Direktur', 'color' => 'yellow', 'order' => 6, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'presiden_review', 'label' => 'Review Presiden Direktur', 'color' => 'yellow', 'order' => 7, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'approved', 'label' => 'Disetujui', 'color' => 'green', 'order' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'rejected', 'label' => 'Ditolak', 'color' => 'red', 'order' => 11, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'completed', 'label' => 'Selesai', 'color' => 'emerald', 'order' => 12, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'paid', 'label' => 'Dibayar', 'color' => 'purple', 'order' => 13, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Create approval types reference table
        Schema::create('approval_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('label');
            $table->text('description')->nullable();
            $table->boolean('requires_nominal')->default(false);
            $table->boolean('requires_presiden')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed approval types including new 'loan' type
        DB::table('approval_types')->insert([
            [
                'code' => 'only',
                'label' => 'Approval Only',
                'description' => 'Persetujuan non-finansial untuk legalitas dan dokumentasi internal',
                'requires_nominal' => false,
                'requires_presiden' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'po',
                'label' => 'Approval to PO',
                'description' => 'Persetujuan pembelian barang/jasa yang menghasilkan Purchase Order',
                'requires_nominal' => true,
                'requires_presiden' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'invoice',
                'label' => 'Approval to Invoice',
                'description' => 'Persetujuan pembayaran atas PO atau tagihan vendor',
                'requires_nominal' => true,
                'requires_presiden' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'loan',
                'label' => 'Pinjaman Karyawan',
                'description' => 'Pengajuan pinjaman karyawan - wajib persetujuan Presiden Direktur',
                'requires_nominal' => true,
                'requires_presiden' => true,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('approval_types');
        Schema::dropIfExists('approval_statuses');
    }
};
