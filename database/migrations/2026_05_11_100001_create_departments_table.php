<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();         // e.g. "Human Resources"
            $table->string('code', 20)->unique();      // e.g. "HR"
            $table->string('director_role')->nullable(); // e.g. "direktur_hr"
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Seed initial departments
        $departments = [
            ['name' => 'IT', 'code' => 'IT', 'director_role' => 'direktur_it', 'sort_order' => 1],
            ['name' => 'Human Resources', 'code' => 'HR', 'director_role' => 'direktur_hr', 'sort_order' => 2],
            ['name' => 'Finance', 'code' => 'FIN', 'director_role' => 'direktur_finance', 'sort_order' => 3],
            ['name' => 'Sales', 'code' => 'SLS', 'director_role' => 'direktur_sales', 'sort_order' => 4],
            ['name' => 'Operations', 'code' => 'OPS', 'director_role' => 'direktur_operasional', 'sort_order' => 5],
            ['name' => 'Board of Directors', 'code' => 'BOD', 'director_role' => 'presiden_direktur', 'sort_order' => 6],
        ];

        $now = now();
        foreach ($departments as $dept) {
            \DB::table('departments')->insert(array_merge($dept, [
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
