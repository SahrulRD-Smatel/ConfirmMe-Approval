<?php

namespace Database\Seeders;

use App\Models\EmailTemplate;
use Illuminate\Database\Seeder;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $templates = [
            // EML-01: Approval Only - Next Approver
            [
                'code' => 'EML-01',
                'name' => 'Approval Only - Next Approver',
                'workflow' => 'only',
                'target' => 'next_approver',
                'subject' => '[ConfirmMe] Mohon Review: {{doc_subject}}',
                'greeting' => 'Halo {{next_approver_name}},',
                'body' => 'Ada dokumen **{{doc_id}}** dari {{staff_name}} yang memerlukan review Anda.',
                'to_config' => ['type' => 'next_approver'],
                'cc_config' => ['include_requester' => true, 'include_previous_approvers' => false],
                'description' => 'Template untuk dokumen Approval Only ke approver berikutnya',
            ],

            // EML-02: Approval to PO - Next Approver
            [
                'code' => 'EML-02',
                'name' => 'PO - Next Approver',
                'workflow' => 'po',
                'target' => 'next_approver',
                'subject' => '[ConfirmMe] Persetujuan PO: {{vendor_name}}',
                'greeting' => 'Yth. {{next_approver_name}},',
                'body' => 'Pengajuan PO **{{doc_id}}** telah disetujui oleh {{prev_approver_name}} dan menunggu otorisasi Anda.',
                'to_config' => ['type' => 'next_approver'],
                'cc_config' => ['include_requester' => true, 'include_previous_approvers' => true],
                'description' => 'Template untuk PO ke approver berikutnya (bukan presiden)',
            ],

            // EML-03: Approval to PO - Presiden Direktur
            [
                'code' => 'EML-03',
                'name' => 'PO - Presiden Direktur',
                'workflow' => 'po',
                'target' => 'presiden',
                'subject' => '[ConfirmMe] Final Approval PO: {{doc_id}}',
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'body' => 'Dokumen PO senilai **{{amount}}** telah diverifikasi lengkap oleh Finance dan siap untuk persetujuan akhir.',
                'to_config' => ['type' => 'presiden_direktur'],
                'cc_config' => [
                    'include_requester' => true,
                    'include_previous_approvers' => true,
                    'include_finance_director' => true,
                    'include_division_director' => true,
                ],
                'description' => 'Template untuk PO ke Presiden Direktur (final approval)',
            ],

            // EML-04: Approval to PO - Final Approved (ke Staff)
            [
                'code' => 'EML-04',
                'name' => 'PO - Final Approved',
                'workflow' => 'po',
                'target' => 'completed',
                'subject' => '[ConfirmMe] PO {{doc_id}} APPROVED - Final',
                'greeting' => 'Selamat {{staff_name}},',
                'body' => 'PO Anda telah disetujui final oleh **Bpk. Kromo Semitro**. Silakan cek lampiran untuk dokumen ber-QR Code.',
                'to_config' => ['type' => 'requester'],
                'cc_config' => [
                    'include_previous_approvers' => true,
                    'include_finance' => true,
                ],
                'description' => 'Template notifikasi PO selesai disetujui ke pemohon',
            ],

            // EML-05: Invoice - Finance Verification
            [
                'code' => 'EML-05',
                'name' => 'Invoice - Finance',
                'workflow' => 'invoice',
                'target' => 'finance',
                'subject' => '[ConfirmMe] Verifikasi Dana Invoice: {{vendor_name}}',
                'greeting' => 'Halo Tim Finance,',
                'body' => 'Invoice **{{doc_id}}** telah diverifikasi Supervisor. Mohon lakukan pengecekan ketersediaan dana sebelum ke Direktur.',
                'to_config' => ['type' => 'finance'],
                'cc_config' => ['include_requester' => true, 'include_previous_approvers' => true],
                'description' => 'Template untuk Invoice ke Finance untuk verifikasi dana',
            ],

            // EML-06: Loan - HR/Finance Verification
            [
                'code' => 'EML-06',
                'name' => 'Loan - HR/Finance',
                'workflow' => 'loan',
                'target' => 'hr_finance',
                'subject' => '[ConfirmMe] Verifikasi Pinjaman: {{staff_name}}',
                'greeting' => 'Yth. Tim HR/Finance,',
                'body' => 'Terdapat permohonan pinjaman dari {{staff_name}}. Mohon verifikasi kelayakan sesuai kebijakan perusahaan.',
                'to_config' => ['type' => 'next_approver'],
                'cc_config' => ['include_requester' => true, 'include_previous_approvers' => true],
                'description' => 'Template untuk Loan ke HR dan Finance untuk verifikasi',
            ],

            // EML-07: Loan - Presiden Direktur
            [
                'code' => 'EML-07',
                'name' => 'Loan - Presiden Direktur',
                'workflow' => 'loan',
                'target' => 'presiden',
                'subject' => '[ConfirmMe] Final Approval Loan: {{staff_name}}',
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'body' => 'Permohonan pinjaman {{staff_name}} telah disetujui oleh Direktur HR dan Finance. Mohon keputusan akhir Anda.',
                'to_config' => ['type' => 'presiden_direktur'],
                'cc_config' => [
                    'include_requester' => true,
                    'include_previous_approvers' => true,
                    'include_hr_director' => true,
                    'include_finance' => true,
                ],
                'description' => 'Template untuk Loan ke Presiden Direktur (final approval)',
            ],

            // EML-08: All - Rejection
            [
                'code' => 'EML-08',
                'name' => 'Dokumen Ditolak',
                'workflow' => 'all',
                'target' => 'rejected',
                'subject' => '[ConfirmMe] Dokumen DITOLAK: {{doc_id}}',
                'greeting' => 'Halo {{staff_name}},',
                'body' => 'Dokumen Anda **{{doc_id}}** telah ditolak oleh {{rejected_by_name}}. Alasan: {{reason}}.',
                'to_config' => ['type' => 'requester'],
                'cc_config' => ['include_previous_approvers' => true],
                'description' => 'Template notifikasi penolakan dokumen untuk semua jenis workflow',
            ],

            // EML-09: Invoice - Presiden
            [
                'code' => 'EML-09',
                'name' => 'Invoice - Presiden Direktur',
                'workflow' => 'invoice',
                'target' => 'presiden',
                'subject' => '[ConfirmMe] Final Approval Invoice: {{doc_id}}',
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'body' => 'Invoice **{{doc_id}}** senilai **{{amount}}** telah diverifikasi oleh Finance dan Direktur. Mohon persetujuan akhir Anda.',
                'to_config' => ['type' => 'presiden_direktur'],
                'cc_config' => [
                    'include_requester' => true,
                    'include_previous_approvers' => true,
                    'include_finance_director' => true,
                ],
                'description' => 'Template untuk Invoice ke Presiden Direktur (final approval)',
            ],

            // EML-10: Generic Completed
            [
                'code' => 'EML-10',
                'name' => 'Dokumen Selesai',
                'workflow' => 'all',
                'target' => 'completed',
                'subject' => '[ConfirmMe] Dokumen {{doc_id}} - APPROVED',
                'greeting' => 'Selamat {{staff_name}},',
                'body' => 'Dokumen **{{doc_id}}** telah selesai diproses dan disetujui oleh semua pihak.',
                'to_config' => ['type' => 'requester'],
                'cc_config' => ['include_previous_approvers' => true],
                'description' => 'Template notifikasi dokumen selesai disetujui (generic)',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['code' => $template['code']],
                $template
            );
        }

        $this->command->info('Email templates seeded successfully!');
    }
}
