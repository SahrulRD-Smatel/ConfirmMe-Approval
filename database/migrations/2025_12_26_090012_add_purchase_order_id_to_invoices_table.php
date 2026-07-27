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
        // Check if column already exists (might be defined in create table migration)
        if (!Schema::hasColumn('invoices', 'purchase_order_id')) {
            Schema::table('invoices', function (Blueprint $table) {
                $table->foreignId('purchase_order_id')->nullable()->after('document_id')->constrained('purchase_orders')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropForeign(['purchase_order_id']);
            $table->dropColumn('purchase_order_id');
        });
    }
};
