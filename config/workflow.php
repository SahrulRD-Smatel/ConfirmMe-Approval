<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Approval Type Workflows
    |--------------------------------------------------------------------------
    |
    | Each approval type has its own workflow chain.
    | The order defines the sequence of approvals.
    | For 'only' type, the chain is DYNAMIC based on nominal (see approval_thresholds).
    |
    */

    'types' => [
        /*
        |----------------------------------------------------------------------
        | 1. APPROVAL ONLY (Dynamic by Nominal)
        |----------------------------------------------------------------------
        | Full chain (for large nominal). Smaller amounts will be auto-truncated
        | based on approval_thresholds configuration below.
        | Flow: Staff → Atasan → HRD → Finance → Presdir → kasir (pencairan)
        */
        'only' => [
            1 => 'supervisor',
            2 => 'hrd',
            3 => 'finance',
            4 => 'presiden_direktur',
        ],

        /*
        |----------------------------------------------------------------------
        | 2. APPROVAL TO PO - EXTERNAL (Purchase Order from Vendor)
        |----------------------------------------------------------------------
        | Untuk: Pembelian barang/jasa ke vendor/pihak ketiga
        | Flow: Atasan → Purchasing → Dir Sales → Dir IT → Dir Operasional
        |       → Dir Keuangan → Presdir → (post: Purchasing create PO)
        | Vendor: WAJIB
        */
        'po' => [
            1 => 'supervisor',
            2 => 'purchasing',
            3 => 'direktur_sales',
            4 => 'direktur_it',
            5 => 'direktur_operasional',
            6 => 'direktur_finance',
            7 => 'presiden_direktur',
        ],

        /*
        |----------------------------------------------------------------------
        | 2b. APPROVAL TO PO - INTERNAL (Antar Divisi)
        |----------------------------------------------------------------------
        | Untuk: Kebutuhan antar divisi (non-vendor)
        | Flow: Atasan → Purchasing → Dir Operasional → Dir Keuangan → Presdir
        |       → (post: Finance pencairan ke Purchasing)
        | Vendor: TIDAK PERLU
        */
        'po_internal' => [
            1 => 'supervisor',
            2 => 'purchasing',
            3 => 'direktur_operasional',
            4 => 'direktur_finance',
            5 => 'presiden_direktur',
        ],

        /*
        |----------------------------------------------------------------------
        | 3. APPROVAL TO INVOICE (Lanjutan PO)
        |----------------------------------------------------------------------
        | Untuk: Invoice vendor, pembayaran atas PO
        | Flow: (Finance create invoice) → Dir Operasional → Dir Finance → Presdir
        |       → (post: Finance pembayaran/arsip)
        | Note: Invoice dibuat oleh Finance setelah PO selesai
        */
        'invoice' => [
            1 => 'direktur_operasional',
            2 => 'direktur_finance',
            3 => 'presiden_direktur',
        ],

        /*
        |----------------------------------------------------------------------
        | 4. EMPLOYEE LOAN (Pinjaman Karyawan)
        |----------------------------------------------------------------------
        | Untuk: Pinjaman karyawan ke perusahaan
        | Flow: Atasan(Manager/Legal) → HR Manager → Direktur HR
        |       → Dir Operasional → Dir Finance → Presdir
        |       → (post: Finance pencairan)
        */
        'loan' => [
            1 => 'manager',
            2 => 'hrd',
            3 => 'direktur_hr',
            4 => 'direktur_operasional',
            5 => 'direktur_finance',
            6 => 'presiden_direktur',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Dynamic Approval Thresholds (for 'only' type)
    |--------------------------------------------------------------------------
    |
    | Determines the final approver role based on nominal amount.
    | The workflow chain is truncated at the specified final_role.
    | Note: Also stored in DB (approval_thresholds table) for admin editing.
    | DB values take priority over config values.
    |
    */
    'approval_thresholds' => [
        'only' => [
            ['max' => 10000000, 'final_role' => 'hrd'],          // ≤10jt → cukup sampai HRD
            ['max' => 100000000, 'final_role' => 'finance'],     // ≤100jt → sampai Finance
            ['max' => null, 'final_role' => 'presiden_direktur'], // >100jt → sampai Presdir
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Approval Type Access (Department-Based)
    |--------------------------------------------------------------------------
    |
    | Defines which departments can CREATE each approval type.
    | Use ['*'] to allow all departments.
    |
    */
    'type_access' => [
        'only' => ['*'],
        'po' => ['*'],
        'po_internal' => ['*'],
        'invoice' => ['Finance'],
        'loan' => ['*'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Status Mapping
    |--------------------------------------------------------------------------
    |
    | Maps each approver role to a document status.
    |
    */
    'status_mapping' => [
        'supervisor' => 'supervisor_review',
        'manager' => 'manager_review',
        'hrd' => 'hrd_review',
        'hrd_coordinator' => 'hrd_coordinator_review',
        'purchasing' => 'purchasing_review',
        'direktur' => 'director_review',
        'direktur_hr' => 'director_review',
        'direktur_finance' => 'director_review',
        'direktur_it' => 'director_review',
        'direktur_sales' => 'director_review',
        'direktur_operasional' => 'director_review',
        'finance' => 'finance_review',
        'presiden_direktur' => 'presiden_review',
    ],

    /*
    |--------------------------------------------------------------------------
    | Division Directors Mapping
    |--------------------------------------------------------------------------
    */
    // Fallback only — primary source is now the `departments` table via Department model
    'division_directors' => [
        'Human Resources' => 'direktur_hr',
        'Finance' => 'direktur_finance',
        'IT' => 'direktur_it',
        'Sales' => 'direktur_sales',
        'Operations' => 'direktur_operasional',
    ],

    /*
    |--------------------------------------------------------------------------
    | Role Labels (Indonesian)
    |--------------------------------------------------------------------------
    */
    'role_labels' => [
        'supervisor' => 'Supervisor',
        'manager' => 'Manager',
        'hrd' => 'HRD',
        'hrd_coordinator' => 'HRD Coordinator',
        'purchasing' => 'Purchasing',
        'direktur' => 'Direktur Divisi',
        'direktur_hr' => 'Direktur HR',
        'direktur_finance' => 'Direktur Finance',
        'direktur_it' => 'Direktur IT',
        'direktur_sales' => 'Direktur Sales',
        'direktur_operasional' => 'Direktur Operasional',
        'presiden_direktur' => 'Presiden Direktur',
        'finance' => 'Finance',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Type Labels
    |--------------------------------------------------------------------------
    */
    'type_labels' => [
        'only' => 'Approval Only',
        'po' => 'Approval to PO (External)',
        'po_internal' => 'Approval to PO (Internal)',
        'invoice' => 'Approval to Invoice',
        'loan' => 'Pinjaman Karyawan',
    ],

    /*
    |--------------------------------------------------------------------------
    | Approval Type Descriptions
    |--------------------------------------------------------------------------
    */
    'type_descriptions' => [
        'only' => 'Persetujuan umum — level approval dinamis berdasarkan nominal',
        'po' => 'Pengajuan pembelian barang/jasa ke vendor (external) — sampai Presiden Direktur',
        'po_internal' => 'Pengajuan kebutuhan antar divisi (internal) — tanpa vendor, approval lebih sederhana',
        'invoice' => 'Persetujuan pembayaran lanjutan PO — dimulai dari Finance',
        'loan' => 'Pengajuan pinjaman karyawan — melalui atasan, HRD, direktur, sampai Presiden',
    ],

    /*
    |--------------------------------------------------------------------------
    | Final Status per Type
    |--------------------------------------------------------------------------
    */
    'final_status' => [
        'only' => 'approved',
        'po' => 'approved',
        'po_internal' => 'approved',
        'invoice' => 'approved',
        'loan' => 'waiting_disbursement',
    ],

    /*
    |--------------------------------------------------------------------------
    | Requires Nominal
    |--------------------------------------------------------------------------
    */
    'requires_nominal' => [
        'only' => false,
        'po' => true,
        'po_internal' => true,
        'invoice' => true,
        'loan' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Requires Presiden Direktur Approval
    |--------------------------------------------------------------------------
    */
    'requires_presiden' => [
        'only' => false,
        'po' => true,
        'po_internal' => true,
        'invoice' => true,
        'loan' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | HRD Conditional Keywords
    |--------------------------------------------------------------------------
    */
    'hrd_keywords' => [
        'karyawan',
        'pegawai',
        'sdm',
        'hrd',
        'rekrutmen',
        'phk',
        'cuti',
        'izin',
        'lembur',
        'gaji',
        'tunjangan',
        'training',
        'pelatihan',
        'mutasi',
        'promosi',
        'demosi',
        'kontrak kerja',
    ],

    /*
    |--------------------------------------------------------------------------
    | Bulk Approval Settings
    |--------------------------------------------------------------------------
    */
    'bulk' => [
        'enabled' => true,
        'max_items' => 50,
        'allowed_types' => ['only', 'po', 'po_internal', 'invoice', 'loan'],
        'same_type_required' => true,
        'same_status_required' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Notification Settings
    |--------------------------------------------------------------------------
    */
    'email_notifications' => [
        'enabled' => env('ENABLE_EMAIL_NOTIFICATIONS', true),
        'queue' => env('QUEUE_EMAIL_NOTIFICATIONS', false),

        'special_recipients' => [
            'finance_director_email' => env('FINANCE_DIRECTOR_EMAIL', null),
            'presiden_direktur_email' => env('PRESIDEN_DIREKTUR_EMAIL', null),
        ],
    ],
];
