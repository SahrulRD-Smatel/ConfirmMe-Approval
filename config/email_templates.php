<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Email Templates Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi template email untuk setiap skenario workflow.
    | Berdasarkan dokumen konfigurasi PT INFO SOLUSINDO DATA UTAMA.
    |
    */

    'templates' => [
        /*
        |----------------------------------------------------------------------
        | EML-01: Approval Only - Next Approver
        |----------------------------------------------------------------------
        */
        'approval_only_pending' => [
            'id' => 'EML-01',
            'subject' => '[ConfirmMe] Mohon Review: {{doc_subject}}',
            'greeting' => 'Halo {{next_approver_name}},',
            'body' => 'Ada dokumen **{{doc_id}}** dari {{staff_name}} yang memerlukan review Anda.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-02: Approval to PO - Next Approver
        |----------------------------------------------------------------------
        */
        'po_pending' => [
            'id' => 'EML-02',
            'subject' => '[ConfirmMe] Persetujuan PO: {{vendor_name}}',
            'greeting' => 'Yth. {{next_approver_name}},',
            'body' => 'Pengajuan PO **{{doc_id}}** telah disetujui oleh {{prev_approver_name}} dan menunggu otorisasi Anda.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-03: Approval to PO - Presiden Direktur
        |----------------------------------------------------------------------
        */
        'po_presiden' => [
            'id' => 'EML-03',
            'subject' => '[ConfirmMe] Final Approval PO: {{doc_id}}',
            'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
            'body' => 'Dokumen PO senilai **{{amount}}** telah diverifikasi lengkap oleh Finance dan siap untuk persetujuan akhir.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-04: Approval to PO - Final Approved (ke Staff)
        |----------------------------------------------------------------------
        */
        'po_approved_final' => [
            'id' => 'EML-04',
            'subject' => '[ConfirmMe] PO {{doc_id}} APPROVED - Final',
            'greeting' => 'Selamat {{staff_name}},',
            'body' => 'PO Anda telah disetujui final oleh **Bpk. Kromo Semitro**. Silakan cek lampiran untuk dokumen ber-QR Code.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-05: Invoice - Finance Verification
        |----------------------------------------------------------------------
        */
        'invoice_finance' => [
            'id' => 'EML-05',
            'subject' => '[ConfirmMe] Verifikasi Dana Invoice: {{vendor_name}}',
            'greeting' => 'Halo Tim Finance,',
            'body' => 'Invoice **{{doc_id}}** telah diverifikasi Supervisor. Mohon lakukan pengecekan ketersediaan dana sebelum ke Direktur.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-06: Loan - HR/Finance Verification
        |----------------------------------------------------------------------
        */
        'loan_hr_finance' => [
            'id' => 'EML-06',
            'subject' => '[ConfirmMe] Verifikasi Pinjaman: {{staff_name}}',
            'greeting' => 'Yth. Tim HR/Finance,',
            'body' => 'Terdapat permohonan pinjaman dari {{staff_name}}. Mohon verifikasi kelayakan sesuai kebijakan perusahaan.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-07: Loan - Presiden Direktur
        |----------------------------------------------------------------------
        */
        'loan_presiden' => [
            'id' => 'EML-07',
            'subject' => '[ConfirmMe] Final Approval Loan: {{staff_name}}',
            'greeting' => 'Yth. Bpk **Kromo Semitro** ,',
            'body' => 'Permohonan pinjaman {{staff_name}} telah disetujui oleh Direktur HR dan Finance. Mohon keputusan akhir Anda.',
        ],

        /*
        |----------------------------------------------------------------------
        | EML-08: All - Rejection
        |----------------------------------------------------------------------
        */
        'rejected' => [
            'id' => 'EML-08',
            'subject' => '[ConfirmMe] Dokumen DITOLAK: {{doc_id}}',
            'greeting' => 'Halo {{staff_name}},',
            'body' => 'Dokumen Anda **{{doc_id}}** telah ditolak oleh {{rejected_by_name}}. Alasan: {{reason}}.',
        ],

        /*
        |----------------------------------------------------------------------
        | Generic - Completed
        |----------------------------------------------------------------------
        */
        'completed' => [
            'id' => 'EML-COMP',
            'subject' => '[ConfirmMe] Dokumen {{doc_id}} - APPROVED',
            'greeting' => 'Selamat {{staff_name}},',
            'body' => 'Dokumen **{{doc_id}}** telah selesai diproses dan disetujui oleh semua pihak.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Placeholder Definitions
    |--------------------------------------------------------------------------
    */
    'placeholders' => [
        '{{doc_id}}' => 'document.document_number',
        '{{doc_subject}}' => 'document.title',
        '{{staff_name}}' => 'document.user.name',
        '{{staff_email}}' => 'document.user.email',
        '{{next_approver_name}}' => 'next_approver.name',
        '{{next_approver_email}}' => 'next_approver.email',
        '{{prev_approver_name}}' => 'previous_approver.name',
        '{{amount}}' => 'document.estimated_budget_formatted',
        '{{vendor_name}}' => 'document.metadata.vendor_name',
        '{{rejected_by_name}}' => 'actor.name',
        '{{reason}}' => 'document.rejection_reason',
    ],

    /*
    |--------------------------------------------------------------------------
    | Special Recipients
    |--------------------------------------------------------------------------
    */
    'special_recipients' => [
        'presiden_direktur' => [
            'name' => 'Kromo Semitro',
            'email' => env('PRESIDEN_EMAIL', 'karsinem.k.semitro@gmail.com'),
        ],
        'finance_director' => [
            'name' => 'Fajar Trimuri',
            'email' => env('FINANCE_DIRECTOR_EMAIL', 'fajar.trimuri@infosolusindo.co.id'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Company Info
    |--------------------------------------------------------------------------
    */
    'company' => [
        'name' => 'PT INFO SOLUSINDO DATA UTAMA',
        'app_name' => 'ConfirmMe',
        'app_url' => env('APP_URL', 'http://localhost:8000'),
    ],
];
