<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->boolean('show_in_pdf')->default(true)->after('status');
            $table->string('pdf_label')->nullable()->after('show_in_pdf');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_approvals', function (Blueprint $table) {
            $table->dropColumn(['show_in_pdf', 'pdf_label']);
        });
    }
};
