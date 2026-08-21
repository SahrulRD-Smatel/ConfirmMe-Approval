<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_features', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert initial features
        DB::table('system_features')->insert([
            [
                'key' => 'feature_2fa_otp',
                'name' => '2FA Security (OTP)',
                'description' => 'Mewajibkan input OTP dari email saat proses approval untuk peran Direktur dan Presiden Direktur.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'module_finance',
                'name' => 'Modul Keuangan (Finance)',
                'description' => 'Akses ke menu Budgets, Purchase Orders, dan Invoices.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'module_hris',
                'name' => 'Modul HRIS (Cuti & Absensi)',
                'description' => 'Akses ke menu pengajuan Cuti dan laporan Absensi.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'module_loan',
                'name' => 'Modul Pinjaman Karyawan',
                'description' => 'Akses ke menu pengajuan Pinjaman (Employee Loan).',
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
        Schema::dropIfExists('system_features');
    }
};
