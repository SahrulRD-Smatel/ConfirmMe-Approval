<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Create attendance system tables.
     * 
     * office_locations: Company office locations with GPS coordinates for geofencing.
     * attendances: Employee check-in/check-out records with selfie & GPS.
     */
    public function up(): void
    {
        Schema::create('office_locations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('radius_meters')->default(100);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->enum('type', ['check_in', 'check_out', 'break_start', 'break_end']);
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('location_name')->nullable();
            $table->foreignId('office_location_id')->nullable()->constrained()->nullOnDelete();
            $table->string('photo')->nullable(); // selfie photo path
            $table->string('device_info')->nullable();
            $table->enum('status', ['on_time', 'late', 'early_leave', 'wfh', 'invalid_location'])->default('on_time');
            $table->boolean('is_wfh')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'date']);
            $table->index(['date', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('office_locations');
    }
};
