<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Create approval_thresholds table for dynamic approval flow by nominal.
     * 
     * Used primarily for 'only' type (Approval Only) where the approval chain
     * varies based on the transaction amount:
     * - Small nominal → ends at Manager/GM
     * - Medium nominal → ends at Direktur Operasional
     * - Large nominal → goes all the way to Presiden Direktur
     */
    public function up(): void
    {
        Schema::create('approval_thresholds', function (Blueprint $table) {
            $table->id();
            $table->string('approval_type'); // 'only', 'po', etc.
            $table->string('label');          // e.g. "Nominal Kecil", "Nominal Besar"
            $table->decimal('min_amount', 15, 2)->default(0);
            $table->decimal('max_amount', 15, 2)->nullable(); // null = unlimited
            $table->string('final_role');     // The highest role needed for this threshold
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['approval_type', 'is_active']);
        });

        // Seed default thresholds for 'only' type
        \Illuminate\Support\Facades\DB::table('approval_thresholds')->insert([
            [
                'approval_type' => 'only',
                'label' => 'Nominal Kecil (≤ 10 Juta)',
                'min_amount' => 0,
                'max_amount' => 10000000,
                'final_role' => 'manager',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'approval_type' => 'only',
                'label' => 'Nominal Menengah (10 Juta - 100 Juta)',
                'min_amount' => 10000001,
                'max_amount' => 100000000,
                'final_role' => 'direktur_operasional',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'approval_type' => 'only',
                'label' => 'Nominal Besar (> 100 Juta)',
                'min_amount' => 100000001,
                'max_amount' => null,
                'final_role' => 'presiden_direktur',
                'is_active' => true,
                'sort_order' => 3,
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
        Schema::dropIfExists('approval_thresholds');
    }
};
