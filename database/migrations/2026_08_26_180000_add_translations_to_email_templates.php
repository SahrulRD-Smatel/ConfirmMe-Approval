<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Add multi-language translation columns to email_templates.
     *
     * Each column stores a JSON object keyed by locale:
     * {"en": "English text", "zh": "中文文本", "ja": "日本語テキスト"}
     *
     * The original subject/greeting/body columns remain as the Indonesian (default) version.
     */
    public function up(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->json('subject_translations')->nullable()->after('subject');
            $table->json('greeting_translations')->nullable()->after('greeting');
            $table->json('body_translations')->nullable()->after('body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('email_templates', function (Blueprint $table) {
            $table->dropColumn(['subject_translations', 'greeting_translations', 'body_translations']);
        });
    }
};
