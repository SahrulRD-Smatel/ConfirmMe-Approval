<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\DocumentTemplate;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $stpd = DocumentTemplate::where('code', 'STPD')->first();
        if ($stpd) {
            $fields = $stpd->fields ?? [];
            $existingDynamic = $fields['dynamic_fields'] ?? [];

            // Check if section fields already exist
            $hasSection = false;
            foreach ($existingDynamic as $df) {
                if (($df['type'] ?? '') === 'section') {
                    $hasSection = true;
                    break;
                }
            }

            if (!$hasSection) {
                $newDynamic = [
                    ['name' => 'sec_pemberi_tugas', 'label' => 'Yang bertanda tangan di bawah ini,', 'type' => 'section', 'required' => false],
                    ['name' => 'pemberi_tugas_nama', 'label' => 'Nama Pemberi Tugas', 'type' => 'text', 'required' => true],
                    ['name' => 'pemberi_tugas_jabatan', 'label' => 'Jabatan Pemberi Tugas', 'type' => 'text', 'required' => true],
                    ['name' => 'pemberi_tugas_dept', 'label' => 'Dept/Div Pemberi Tugas', 'type' => 'text', 'required' => true],
                    ['name' => 'sec_penerima_tugas', 'label' => 'Menugaskan,', 'type' => 'section', 'required' => false],
                    ['name' => 'nama_yang_ditugaskan', 'label' => 'Nama Yang Ditugaskan', 'type' => 'text', 'required' => true],
                    ['name' => 'jabatan_yang_ditugaskan', 'label' => 'Jabatan', 'type' => 'text', 'required' => true],
                    ['name' => 'dept_div_yang_ditugaskan', 'label' => 'Dept/Div', 'type' => 'text', 'required' => true],
                    ['name' => 'sec_pelaksanaan_dinas', 'label' => 'Untuk melaksanakan perjalanan dinas', 'type' => 'section', 'required' => false],
                    ['name' => 'tugas', 'label' => 'Tugas / Keperluan', 'type' => 'textarea', 'required' => true],
                    ['name' => 'kota_tujuan', 'label' => 'Kota Tujuan', 'type' => 'text', 'required' => true],
                    ['name' => 'tanggal_berangkat', 'label' => 'Tanggal Berangkat', 'type' => 'date', 'required' => true],
                    ['name' => 'tanggal_kembali', 'label' => 'Tanggal Kembali', 'type' => 'date', 'required' => true],
                    ['name' => 'jenis_transportasi', 'label' => 'Jenis Transportasi', 'type' => 'select', 'options' => "Pesawat Udara [PU]\nKereta Api [KA]\nBis [BIS]\nMobil Operasional Kantor\nLainnya", 'required' => true],
                    ['name' => 'catatan', 'label' => 'Catatan Tambahan', 'type' => 'textarea', 'required' => false],
                ];

                $fields['dynamic_fields'] = $newDynamic;
                $stpd->update(['fields' => $fields]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};
