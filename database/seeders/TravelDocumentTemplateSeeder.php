<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class TravelDocumentTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds for travel templates (STPD and HR_UMBPD).
     */
    public function run(): void
    {
        $templates = [
            [
                'name' => 'Surat Tugas Perjalanan Dinas',
                'code' => 'STPD',
                'description' => 'Surat penugasan resmi untuk melaksanakan perjalanan dinas.',
                'approval_type' => 'only',
                'workflow' => ['role:manager', 'role:direktur_operasional'],
                'fields' => [
                    'title' => 'Surat Tugas Perjalanan Dinas',
                    'description' => 'Untuk melaksanakan perjalanan dinas sesuai ketentuan dan penugasan resmi perusahaan.',
                    'priority' => 'normal',
                    'document_category' => 'Surat Tugas Perjalanan Dinas',
                    'pdf_layout' => 'formulir_kerja',
                    'notify_roles' => ['role:hrd_coordinator', 'role:ga'],
                    'dynamic_fields' => [
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
                    ],
                ],
            ],
            [
                'name' => 'Pemesanan Tiket & Permintaan Uang Muka Perjalanan Dinas',
                'code' => 'HR_UMBPD',
                'description' => 'Formulir pemesanan tiket perjalanan dinas dan permohonan uang muka operasional (Form 01A/HR-UMBPD/05).',
                'approval_type' => 'only',
                'workflow' => ['role:manager', 'role:hrd_coordinator', 'role:finance'],
                'fields' => [
                    'title' => 'Pemesanan Tiket & Permintaan Uang Muka Perjalanan Dinas',
                    'description' => 'Formulir pemesanan tiket perjalanan dinas dan permohonan uang muka operasional sesuai Form 01A/HR-UMBPD/05.',
                    'priority' => 'normal',
                    'document_category' => 'Form 01A/HR-UMBPD/05',
                    'pdf_layout' => 'formulir_kerja',
                    'terms_and_conditions' => "*) Keterangan:\nJenis Transportasi: [BIS] Bis; [PU] Pesawat Udara; [KA] Kereta Api\nKelas: [FC] Bisnis; [EKO] Ekonomi\nTanggal perkiraan kembali WAJIB dicantumkan meskipun belum dapat dipastikan.",
                    'notify_roles' => ['role:ga'],
                    'dynamic_fields' => [
                        ['name' => 'no_stpd', 'label' => 'No. Referensi STPD', 'type' => 'text', 'required' => true],
                        ['name' => 'nama_pemohon', 'label' => 'Nama Pemohon', 'type' => 'text', 'required' => true],
                        ['name' => 'divisi_dept', 'label' => 'Divisi / Dept', 'type' => 'text', 'required' => true],
                        ['name' => 'kota_tujuan', 'label' => 'Kota Tujuan', 'type' => 'text', 'required' => true],
                        ['name' => 'keperluan', 'label' => 'Keperluan', 'type' => 'textarea', 'required' => true],
                        ['name' => 'tgl_berangkat', 'label' => 'Tgl Berangkat', 'type' => 'date', 'required' => true],
                        ['name' => 'tgl_kembali', 'label' => 'Tgl Kembali', 'type' => 'date', 'required' => true],
                        ['name' => 'pemesanan_tiket', 'label' => 'Pemesanan Tiket Perjalanan', 'type' => 'table', 'columns' => 'Tanggal, Dari, Tujuan, Jam, Jenis Transportasi, Kelas, Estimasi Harga Tiket', 'required' => true],
                        ['name' => 'catatan_tiket', 'label' => 'Catatan Pemesanan Tiket', 'type' => 'textarea', 'required' => false],
                        ['name' => 'um_airport_tax', 'label' => '1. Airport Tax (Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_akomodasi', 'label' => '2. Akomodasi Hotel / Mess (Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_transport_antar_kota', 'label' => '3a. Transportasi Antar Kota / Taxi (Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_transport_dalam_kota', 'label' => '3b. Transportasi Dalam Kota (Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_uang_saku', 'label' => '4. Uang Saku x Hari (Total Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_insidental', 'label' => '5. Insidental x Hari (Total Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'um_biaya_tak_terduga', 'label' => '6a. Biaya Tak Terduga di Kota Tujuan (Rp)', 'type' => 'number', 'required' => false],
                        ['name' => 'total_estimasi_pemohon', 'label' => 'TOTAL (Estimasi dari Pemohon) (Rp)', 'type' => 'number', 'required' => true],
                    ],
                ],
            ],
        ];

        foreach ($templates as $template) {
            DocumentTemplate::updateOrCreate(
                ['code' => $template['code']],
                [
                    ...$template,
                    'created_by' => 1,
                    'is_active' => true,
                ]
            );
        }
    }
}
