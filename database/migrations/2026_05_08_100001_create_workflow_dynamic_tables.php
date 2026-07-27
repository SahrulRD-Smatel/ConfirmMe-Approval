<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Workflow chains — stores approval step order per type
        Schema::create('workflow_chains', function (Blueprint $table) {
            $table->id();
            $table->string('approval_type', 50);  // 'only', 'po', 'po_internal', 'invoice', 'loan'
            $table->string('role', 50);            // 'supervisor', 'hrd', 'finance', etc
            $table->unsignedInteger('step_order');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['approval_type', 'is_active', 'step_order']);
        });

        // 2. Workflow settings — key/value store for dynamic config
        Schema::create('workflow_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->json('value');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Seed workflow chains from current config
        $this->seedChains();
        $this->seedSettings();
    }

    protected function seedChains(): void
    {
        $chains = [
            'only' => [
                1 => 'supervisor',
                2 => 'hrd',
                3 => 'finance',
                4 => 'presiden_direktur',
            ],
            'po' => [
                1 => 'supervisor',
                2 => 'purchasing',
                3 => 'direktur_sales',
                4 => 'direktur_it',
                5 => 'direktur_operasional',
                6 => 'direktur_finance',
                7 => 'presiden_direktur',
            ],
            'po_internal' => [
                1 => 'supervisor',
                2 => 'purchasing',
                3 => 'direktur_operasional',
                4 => 'direktur_finance',
                5 => 'presiden_direktur',
            ],
            'invoice' => [
                1 => 'direktur_operasional',
                2 => 'direktur_finance',
                3 => 'presiden_direktur',
            ],
            'loan' => [
                1 => 'manager',
                2 => 'hrd',
                3 => 'direktur_hr',
                4 => 'direktur_operasional',
                5 => 'direktur_finance',
                6 => 'presiden_direktur',
            ],
        ];

        $now = now();
        foreach ($chains as $type => $steps) {
            foreach ($steps as $order => $role) {
                DB::table('workflow_chains')->insert([
                    'approval_type' => $type,
                    'role' => $role,
                    'step_order' => $order,
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    protected function seedSettings(): void
    {
        $now = now();
        $settings = [
            [
                'key' => 'type_access',
                'value' => json_encode([
                    'only' => ['*'],
                    'po' => ['*'],
                    'po_internal' => ['*'],
                    'invoice' => ['Finance'],
                    'loan' => ['*'],
                ]),
                'description' => 'Departemen yang boleh membuat setiap tipe approval',
            ],
            [
                'key' => 'role_hierarchy',
                'value' => json_encode([
                    'staff' => 1,
                    'supervisor' => 2,
                    'purchasing' => 2,
                    'hrd_coordinator' => 2,
                    'manager' => 3,
                    'hrd' => 3,
                    'finance' => 3,
                    'direktur' => 4,
                    'direktur_hr' => 4,
                    'direktur_finance' => 4,
                    'direktur_it' => 4,
                    'direktur_sales' => 4,
                    'direktur_operasional' => 4,
                    'presiden_direktur' => 5,
                ]),
                'description' => 'Level hierarki setiap role (angka lebih tinggi = jabatan lebih tinggi)',
            ],
            [
                'key' => 'functional_roles',
                'value' => json_encode(['finance', 'hrd', 'purchasing']),
                'description' => 'Role fungsional yang tidak boleh di-skip oleh auto-skip hierarchy',
            ],
            [
                'key' => 'hrd_keywords',
                'value' => json_encode([
                    'karyawan', 'pegawai', 'sdm', 'hrd', 'rekrutmen', 'phk',
                    'cuti', 'izin', 'lembur', 'gaji', 'tunjangan', 'training',
                    'pelatihan', 'mutasi', 'promosi', 'demosi', 'kontrak kerja',
                ]),
                'description' => 'Keyword untuk mendeteksi dokumen HR-related pada Approval Only',
            ],
            [
                'key' => 'departments',
                'value' => json_encode([
                    'IT', 'Human Resources', 'Finance', 'Sales', 'Operations', 'Marketing', 'Legal',
                ]),
                'description' => 'Daftar departemen yang tersedia di sistem',
            ],
        ];

        foreach ($settings as $s) {
            DB::table('workflow_settings')->insert(array_merge($s, [
                'created_at' => $now,
                'updated_at' => $now,
            ]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_chains');
        Schema::dropIfExists('workflow_settings');
    }
};
