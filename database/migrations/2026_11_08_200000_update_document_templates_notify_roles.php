<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\DocumentTemplate;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $configs = [
            'SURAT_IJIN_GUDANG' => [
                'notify_roles' => ['role:ga', 'role:admin_operasional'],
                'workflow' => ['role:manager', 'role:ga', 'role:admin_operasional', 'role:direktur_operasional'],
            ],
            'IT_EMAIL' => [
                'notify_roles' => ['role:it_internal'],
                'workflow' => ['role:manager', 'role:manager_hrga', 'role:it_internal'],
            ],
            'PEMBELIAN_BARANG' => [
                'notify_roles' => ['role:ga'],
                'workflow' => ['role:ga', 'role:manager_hrga', 'role:direktur_hr', 'role:direktur_operasional', 'role:direktur_finance'],
            ],
            'IT_HARDWARE' => [
                'notify_roles' => ['role:it_internal'],
                'workflow' => ['role:manager', 'role:it_internal', 'role:manager_hrga', 'role:direktur_finance'],
            ],
            'VEHICLE_LOAN' => [
                'notify_roles' => ['role:ga'],
                'workflow' => ['role:manager', 'role:ga', 'role:manager_hrga', 'role:direktur_operasional', 'role:direktur_finance'],
            ],
        ];

        foreach ($configs as $code => $config) {
            $template = DocumentTemplate::where('code', $code)->first();
            if ($template) {
                $fields = $template->fields ?? [];
                $fields['notify_roles'] = $config['notify_roles'];

                $template->update([
                    'fields' => $fields,
                    'workflow' => $config['workflow'],
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Keep templates as is
    }
};

