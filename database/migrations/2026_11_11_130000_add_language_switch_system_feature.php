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
        if (Schema::hasTable('system_features') && DB::table('system_features')->where('key', 'feature_language_switch')->doesntExist()) {
            DB::table('system_features')->insert([
                'key' => 'feature_language_switch',
                'name' => 'Multi-Language / Ganti Bahasa',
                'description' => 'Menampilkan dropdown pengubah bahasa (ID, EN, ZH, JA) di navigasi atas. Jika dinonaktifkan, sistem terkunci penuh ke Bahasa Indonesia.',
                'is_active' => false,
                'created_at' => now(),
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
            DB::table('system_features')->where('key', 'feature_language_switch')->delete();
        }
    }
};

