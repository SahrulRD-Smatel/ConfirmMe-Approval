<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('system_features')) {
            DB::table('system_features')->where('key', 'feature_2fa_otp')->update([
                'name' => 'Kebijakan 2FA Approval (Direktur & Eksekutif)',
                'description' => 'Mewajibkan verifikasi 2 langkah (via Google Authenticator atau OTP Email) saat proses persetujuan dokumen oleh peran Direktur dan Eksekutif.',
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('system_features')) {
            DB::table('system_features')->where('key', 'feature_2fa_otp')->update([
                'name' => '2FA Security (OTP)',
                'description' => 'Mewajibkan input OTP dari email saat proses approval untuk peran Direktur dan Presiden Direktur.',
                'updated_at' => now(),
            ]);
        }
    }
};
