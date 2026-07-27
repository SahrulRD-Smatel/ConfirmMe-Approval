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
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // EML-01, EML-02, etc.
            $table->string('name'); // Nama template untuk display
            $table->string('workflow'); // approval_type: only, po, invoice, loan, all
            $table->string('target'); // Siapa target: next_approver, presiden, staff, finance, hr
            $table->string('subject'); // Subject email dengan placeholder
            $table->text('greeting'); // Greeting dengan placeholder
            $table->text('body'); // Body content dengan placeholder
            $table->json('to_config')->nullable(); // Konfigurasi TO recipient
            $table->json('cc_config')->nullable(); // Konfigurasi CC recipients
            $table->boolean('is_active')->default(true);
            $table->text('description')->nullable(); // Deskripsi untuk admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
