<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\DocumentTemplate;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. SURAT_IJIN_GUDANG: Konfigurasi Lengkap Form & Tanda Tangan PDF
        $gudang = DocumentTemplate::where('code', 'SURAT_IJIN_GUDANG')->first();
        if ($gudang) {
            $fields = $gudang->fields ?? [];

            // Dynamic fields sesuai 13 catatan resmi
            $fields['dynamic_fields'] = [
                [
                    'name' => 'jenis_transaksi',
                    'label' => 'Jenis Transaksi (Keluar / Masuk Barang)',
                    'type' => 'select',
                    'options' => 'Barang Keluar, Barang Masuk',
                    'required' => true,
                ],
                [
                    'name' => 'perusahaan',
                    'label' => 'Perusahaan',
                    'type' => 'text',
                    'required' => true,
                ],
                [
                    'name' => 'dokumen_po_do',
                    'label' => 'Dokumen P.O / D.O',
                    'type' => 'select',
                    'options' => 'Ada, Tidak',
                    'required' => true,
                ],
                [
                    'name' => 'no_po_do',
                    'label' => 'No. P.O / D.O',
                    'type' => 'text',
                    'required' => false,
                ],
                [
                    'name' => 'items',
                    'label' => 'Daftar Rincian Barang',
                    'type' => 'table',
                    'columns' => 'Nama Barang & Serial Number (SN), Project Awal Barang, Jumlah, Actual Project',
                    'required' => true,
                ],
                [
                    'name' => 'lokasi_tujuan',
                    'label' => 'Lokasi Tujuan / Asal Barang',
                    'type' => 'select',
                    'options' => 'Gudang Pusat, Gudang Project, Vendor / Supplier, Customer / Site, Kantor Operasional, Lainnya',
                    'required' => true,
                ],
                [
                    'name' => 'tanggal_keluar_masuk',
                    'label' => 'Tanggal Keluar / Masuk',
                    'type' => 'date',
                    'required' => true,
                ],
                [
                    'name' => 'jam_keluar_masuk',
                    'label' => 'Jam Keluar / Masuk',
                    'type' => 'time',
                    'required' => true,
                ],
                [
                    'name' => 'kesesuaian_project',
                    'label' => 'Apakah barang sesuai dengan peruntukkan Project awal?',
                    'type' => 'select',
                    'options' => 'Iya, Tidak',
                    'required' => true,
                ],
                [
                    'name' => 'alasan',
                    'label' => 'Alasan (Wajib diisi jika barang tidak sesuai peruntukkan project awal)',
                    'type' => 'textarea',
                    'required' => false,
                ],
            ];

            // Tanda tangan PDF tepat 3 kotak: Pemohon, Petugas (Admin OPS), Disetujui (Direktur OPS)
            $fields['workflow_signers'] = [
                ['show_in_pdf' => false, 'pdf_label' => null],
                ['show_in_pdf' => false, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => 'Petugas'],
                ['show_in_pdf' => true, 'pdf_label' => 'Disetujui'],
            ];

            $gudang->update([
                'fields' => $fields,
                'workflow' => ['role:manager', 'role:ga', 'role:admin_operasional', 'role:direktur_operasional'],
            ]);
        }

        // 2. VEHICLE_LOAN: Standarisasi Penandatangan PDF
        $vehicle = DocumentTemplate::where('code', 'VEHICLE_LOAN')->first();
        if ($vehicle) {
            $fields = $vehicle->fields ?? [];
            $fields['workflow_signers'] = [
                ['show_in_pdf' => true, 'pdf_label' => 'Atasan Langsung'],
                ['show_in_pdf' => false, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => null],
                ['show_in_pdf' => false, 'pdf_label' => null],
            ];
            $vehicle->update(['fields' => $fields]);
        }

        // 3. IT_EMAIL: Standarisasi Penandatangan PDF (Label IT Internal)
        $itEmail = DocumentTemplate::where('code', 'IT_EMAIL')->first();
        if ($itEmail) {
            $fields = $itEmail->fields ?? [];
            $fields['workflow_signers'] = [
                ['show_in_pdf' => true, 'pdf_label' => 'IT Internal'],
                ['show_in_pdf' => false, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => 'IT Internal'],
            ];
            $itEmail->update(['fields' => $fields]);
        }

        // 4. PEMBELIAN_BARANG: Standarisasi Penandatangan PDF (Label Purchasing)
        $pembelian = DocumentTemplate::where('code', 'PEMBELIAN_BARANG')->first();
        if ($pembelian) {
            $fields = $pembelian->fields ?? [];
            $fields['workflow_signers'] = [
                ['show_in_pdf' => true, 'pdf_label' => 'Purchasing'],
                ['show_in_pdf' => false, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => null],
                ['show_in_pdf' => true, 'pdf_label' => null],
            ];
            $pembelian->update(['fields' => $fields]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safe to keep templates
    }
};
