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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Please Review: {{doc_subject}}',
                    'zh' => '[ConfirmMe] 请审核: {{doc_subject}}',
                    'ja' => '[ConfirmMe] レビューをお願いします: {{doc_subject}}',
                ],
                'greeting' => 'Halo {{next_approver_name}},',
                'greeting_translations' => [
                    'en' => 'Dear {{next_approver_name}},',
                    'zh' => '{{next_approver_name}} 您好，',
                    'ja' => '{{next_approver_name}} 様',
                ],
                'body' => 'Ada dokumen **{{doc_id}}** dari {{staff_name}} yang memerlukan review Anda.',
                'body_translations' => [
                    'en' => 'Document **{{doc_id}}** from {{staff_name}} requires your review.',
                    'zh' => '{{staff_name}} 提交的文件 **{{doc_id}}** 需要您的审核。',
                    'ja' => '{{staff_name}} から提出された文書 **{{doc_id}}** のレビューが必要です。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] PO Approval: {{vendor_name}}',
                    'zh' => '[ConfirmMe] 采购订单审批: {{vendor_name}}',
                    'ja' => '[ConfirmMe] PO承認: {{vendor_name}}',
                ],
                'greeting' => 'Yth. {{next_approver_name}},',
                'greeting_translations' => [
                    'en' => 'Dear {{next_approver_name}},',
                    'zh' => '{{next_approver_name}} 您好，',
                    'ja' => '{{next_approver_name}} 様',
                ],
                'body' => 'Pengajuan PO **{{doc_id}}** telah disetujui oleh {{prev_approver_name}} dan menunggu otorisasi Anda.',
                'body_translations' => [
                    'en' => 'PO **{{doc_id}}** has been approved by {{prev_approver_name}} and is awaiting your authorization.',
                    'zh' => '采购订单 **{{doc_id}}** 已获得 {{prev_approver_name}} 的批准，等待您的授权。',
                    'ja' => 'PO **{{doc_id}}** は {{prev_approver_name}} に承認され、あなたの承認を待っています。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Final Approval PO: {{doc_id}}',
                    'zh' => '[ConfirmMe] PO最终审批: {{doc_id}}',
                    'ja' => '[ConfirmMe] PO最終承認: {{doc_id}}',
                ],
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'greeting_translations' => [
                    'en' => 'Dear Mr. **Kromo Semitro**,',
                    'zh' => '尊敬的 **Kromo Semitro** 先生，',
                    'ja' => '**Kromo Semitro** 様',
                ],
                'body' => 'Dokumen PO senilai **{{amount}}** telah diverifikasi lengkap oleh Finance dan siap untuk persetujuan akhir.',
                'body_translations' => [
                    'en' => 'PO document worth **{{amount}}** has been fully verified by Finance and is ready for final approval.',
                    'zh' => '价值 **{{amount}}** 的PO文件已通过财务完整验证，等待最终批准。',
                    'ja' => '**{{amount}}** のPO文書は財務部門による検証が完了し、最終承認の準備が整いました。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] PO {{doc_id}} APPROVED - Final',
                    'zh' => '[ConfirmMe] PO {{doc_id}} 已批准 - 最终',
                    'ja' => '[ConfirmMe] PO {{doc_id}} 承認済み - 最終',
                ],
                'greeting' => 'Selamat {{staff_name}},',
                'greeting_translations' => [
                    'en' => 'Congratulations {{staff_name}},',
                    'zh' => '恭喜 {{staff_name}}，',
                    'ja' => 'おめでとうございます {{staff_name}} 様',
                ],
                'body' => 'PO Anda telah disetujui final oleh **Bpk. Kromo Semitro**. Silakan cek lampiran untuk dokumen ber-QR Code.',
                'body_translations' => [
                    'en' => 'Your PO has been given final approval by **Mr. Kromo Semitro**. Please check the attachment for the QR Code document.',
                    'zh' => '您的PO已获得 **Kromo Semitro** 先生的最终批准。请查看附件中的二维码文件。',
                    'ja' => 'あなたのPOは **Kromo Semitro** 様により最終承認されました。QRコード付き文書を添付ファイルでご確認ください。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Invoice Fund Verification: {{vendor_name}}',
                    'zh' => '[ConfirmMe] 发票资金验证: {{vendor_name}}',
                    'ja' => '[ConfirmMe] 請求書資金確認: {{vendor_name}}',
                ],
                'greeting' => 'Halo Tim Finance,',
                'greeting_translations' => [
                    'en' => 'Dear Finance Team,',
                    'zh' => '财务团队您好，',
                    'ja' => '財務チームの皆様',
                ],
                'body' => 'Invoice **{{doc_id}}** telah diverifikasi Supervisor. Mohon lakukan pengecekan ketersediaan dana sebelum ke Direktur.',
                'body_translations' => [
                    'en' => 'Invoice **{{doc_id}}** has been verified by Supervisor. Please check the fund availability before forwarding to Director.',
                    'zh' => '发票 **{{doc_id}}** 已通过主管验证。请在提交给董事之前检查资金可用性。',
                    'ja' => '請求書 **{{doc_id}}** は上司により確認済みです。ディレクターに転送する前に、資金の利用可能性をご確認ください。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Loan Verification: {{staff_name}}',
                    'zh' => '[ConfirmMe] 贷款验证: {{staff_name}}',
                    'ja' => '[ConfirmMe] ローン確認: {{staff_name}}',
                ],
                'greeting' => 'Yth. Tim HR/Finance,',
                'greeting_translations' => [
                    'en' => 'Dear HR/Finance Team,',
                    'zh' => '尊敬的人事/财务团队，',
                    'ja' => 'HR/財務チームの皆様',
                ],
                'body' => 'Terdapat permohonan pinjaman dari {{staff_name}}. Mohon verifikasi kelayakan sesuai kebijakan perusahaan.',
                'body_translations' => [
                    'en' => 'There is a loan request from {{staff_name}}. Please verify the eligibility according to company policy.',
                    'zh' => '{{staff_name}} 提交了贷款申请。请根据公司政策验证其资格。',
                    'ja' => '{{staff_name}} からローン申請があります。会社の方針に従って適格性をご確認ください。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Final Approval Loan: {{staff_name}}',
                    'zh' => '[ConfirmMe] 贷款最终审批: {{staff_name}}',
                    'ja' => '[ConfirmMe] ローン最終承認: {{staff_name}}',
                ],
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'greeting_translations' => [
                    'en' => 'Dear Mr. **Kromo Semitro**,',
                    'zh' => '尊敬的 **Kromo Semitro** 先生，',
                    'ja' => '**Kromo Semitro** 様',
                ],
                'body' => 'Permohonan pinjaman {{staff_name}} telah disetujui oleh Direktur HR dan Finance. Mohon keputusan akhir Anda.',
                'body_translations' => [
                    'en' => 'Loan request from {{staff_name}} has been approved by HR and Finance Directors. Your final decision is required.',
                    'zh' => '{{staff_name}} 的贷款申请已获得人事和财务董事的批准。需要您的最终决定。',
                    'ja' => '{{staff_name}} のローン申請はHRおよび財務ディレクターにより承認されました。最終決定をお願いいたします。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Document REJECTED: {{doc_id}}',
                    'zh' => '[ConfirmMe] 文件已拒绝: {{doc_id}}',
                    'ja' => '[ConfirmMe] 文書却下: {{doc_id}}',
                ],
                'greeting' => 'Halo {{staff_name}},',
                'greeting_translations' => [
                    'en' => 'Dear {{staff_name}},',
                    'zh' => '{{staff_name}} 您好，',
                    'ja' => '{{staff_name}} 様',
                ],
                'body' => 'Dokumen Anda **{{doc_id}}** telah ditolak oleh {{rejected_by_name}}. Alasan: {{reason}}.',
                'body_translations' => [
                    'en' => 'Your document **{{doc_id}}** has been rejected by {{rejected_by_name}}. Reason: {{reason}}.',
                    'zh' => '您的文件 **{{doc_id}}** 已被 {{rejected_by_name}} 拒绝。原因: {{reason}}。',
                    'ja' => 'あなたの文書 **{{doc_id}}** は {{rejected_by_name}} により却下されました。理由: {{reason}}。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Final Approval Invoice: {{doc_id}}',
                    'zh' => '[ConfirmMe] 发票最终审批: {{doc_id}}',
                    'ja' => '[ConfirmMe] 請求書最終承認: {{doc_id}}',
                ],
                'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
                'greeting_translations' => [
                    'en' => 'Dear Mr. **Kromo Semitro**,',
                    'zh' => '尊敬的 **Kromo Semitro** 先生，',
                    'ja' => '**Kromo Semitro** 様',
                ],
                'body' => 'Invoice **{{doc_id}}** senilai **{{amount}}** telah diverifikasi oleh Finance dan Direktur. Mohon persetujuan akhir Anda.',
                'body_translations' => [
                    'en' => 'Invoice **{{doc_id}}** worth **{{amount}}** has been verified by Finance and Director. Your final approval is required.',
                    'zh' => '价值 **{{amount}}** 的发票 **{{doc_id}}** 已通过财务和董事验证。需要您的最终批准。',
                    'ja' => '**{{amount}}** の請求書 **{{doc_id}}** は財務部門およびディレクターにより確認済みです。最終承認をお願いいたします。',
                ],
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
                'subject_translations' => [
                    'en' => '[ConfirmMe] Document {{doc_id}} - APPROVED',
                    'zh' => '[ConfirmMe] 文件 {{doc_id}} - 已批准',
                    'ja' => '[ConfirmMe] 文書 {{doc_id}} - 承認済み',
                ],
                'greeting' => 'Selamat {{staff_name}},',
                'greeting_translations' => [
                    'en' => 'Congratulations {{staff_name}},',
                    'zh' => '恭喜 {{staff_name}}，',
                    'ja' => 'おめでとうございます {{staff_name}} 様',
                ],
                'body' => 'Dokumen **{{doc_id}}** telah selesai diproses dan disetujui oleh semua pihak.',
                'body_translations' => [
                    'en' => 'Document **{{doc_id}}** has been fully processed and approved by all parties.',
                    'zh' => '文件 **{{doc_id}}** 已完成处理并获得所有相关方的批准。',
                    'ja' => '文書 **{{doc_id}}** はすべての関係者により処理・承認が完了しました。',
                ],
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
