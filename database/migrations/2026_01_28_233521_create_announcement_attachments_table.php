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
        Schema::create('announcement_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('announcement_id')->constrained()->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_type')->default('document'); // image, document
            $table->string('original_name')->nullable();
            $table->timestamps();
        });

        // Migrate existing data
        $announcements = \Illuminate\Support\Facades\DB::table('announcements')->whereNotNull('attachment')->get();
        foreach ($announcements as $announcement) {
            $ext = pathinfo($announcement->attachment, PATHINFO_EXTENSION);
            $type = in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'document';

            \Illuminate\Support\Facades\DB::table('announcement_attachments')->insert([
                'announcement_id' => $announcement->id,
                'file_path' => $announcement->attachment,
                'file_type' => $type,
                'original_name' => basename($announcement->attachment),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Drop old column
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('announcement_attachments');
    }
};
