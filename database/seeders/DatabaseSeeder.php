<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Budget;
use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Document permissions
            'create-document',
            'view-document',
            'view-all-documents',
            'view-department-documents',
            'view-finance-documents',
            'edit-document',
            'delete-document',
            'submit-document',
            'approve-document',
            'reject-document',
            'print-document',

            // Finance permissions
            'manage-budget',
            'create-po',
            'create-invoice',
            'print-po',
            'print-invoice',

            // Purchasing permissions
            'manage-purchasing',

            // Admin permissions
            'manage-users',
            'manage-roles',
            'manage-workflow',
            'manage-templates',
            'view-audit-log',

            // Report permissions
            'view-reports',
            'export-reports',

            // Attendance permissions
            'manage-attendance',
            'view-attendance',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Create Roles with Permissions
        $roles = [
            'admin' => [
                'manage-users',
                'manage-roles',
                'manage-workflow',
                'manage-templates',
                'view-audit-log',
                'view-document',
                'view-all-documents',
                'view-reports',
                'export-reports',
            ],
            'staff' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
            ],
            'supervisor' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'view-reports',
            ],
            'hrd' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-department-documents',
                'view-all-documents',
                'view-reports',
                'export-reports',
                'manage-attendance',
                'view-attendance',
            ],
            'manager' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-department-documents',
                'view-reports',
            ],
            // Division Directors
            'direktur_hr' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-all-documents',
                'view-reports',
                'export-reports',
                'manage-attendance',
                'view-attendance',
            ],
            'direktur_finance' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'print-po',
                'print-invoice',
                'view-reports',
                'export-reports',
            ],
            'direktur_it' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-reports',
                'export-reports',
            ],
            'direktur_sales' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-reports',
                'export-reports',
            ],
            // President Director (highest level)
            'presiden_direktur' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'print-po',
                'print-invoice',
                'view-all-documents',
                'view-reports',
                'export-reports',
                'view-audit-log',
            ],
            'finance' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'manage-budget',
                'create-po',
                'create-invoice',
                'print-po',
                'print-invoice',
                'view-finance-documents',
                'view-department-documents',
                'view-reports',
                'export-reports',
            ],
            // NEW ROLES
            'purchasing' => [
                'create-document',
                'view-document',
                'edit-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'create-po',
                'print-po',
                'manage-purchasing',
                'view-reports',
            ],
            'direktur_operasional' => [
                'create-document',
                'view-document',
                'edit-document',
                'delete-document',
                'submit-document',
                'approve-document',
                'reject-document',
                'print-document',
                'view-reports',
                'export-reports',
            ],
            'hrd_coordinator' => [
                'create-document',
                'view-document',
                'approve-document',
                'reject-document',
                'view-reports',
                'manage-attendance',
                'view-attendance',
            ],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }

        // Create Users for each role - Indonesian names by division
        $users = [
            // === ADMIN ===
            [
                'name' => 'Rizky Pratama',
                'email' => 'admin@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'ADM001',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'System Administrator',
                'role' => 'admin',
            ],

            // === PRESIDEN DIREKTUR ===
            [
                'name' => 'Kromo Semitro',
                'email' => 'karsinem.k.semitro@gmail.com',
                'password' => Hash::make('password'),
                'employee_id' => 'PRS001',
                'department' => 'Board of Directors',
                'division' => null,
                'position' => 'Presiden Direktur',
                'role' => 'presiden_direktur',
            ],

            // === DIVISION DIRECTORS ===
            [
                'name' => 'Dewi Kartika',
                'email' => 'direktur.hr@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'DIR001',
                'department' => 'Human Resources',
                'division' => 'HR',
                'position' => 'Direktur HR',
                'role' => 'direktur_hr',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'direktur.finance@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'DIR002',
                'department' => 'Finance',
                'division' => 'Finance',
                'position' => 'Direktur Finance',
                'role' => 'direktur_finance',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'direktur.it@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'DIR003',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'Direktur IT',
                'role' => 'direktur_it',
            ],
            [
                'name' => 'Siti Rahma',
                'email' => 'direktur.sales@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'DIR004',
                'department' => 'Sales',
                'division' => 'Sales',
                'position' => 'Direktur Sales',
                'role' => 'direktur_sales',
            ],

            // === IT DIVISION ===
            [
                'name' => 'Fajar Nugroho',
                'email' => 'manager.it@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'MGR001',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'IT Manager',
                'role' => 'manager',
            ],
            [
                'name' => 'Dimas Saputra',
                'email' => 'supervisor.it@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'SPV001',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'IT Team Lead',
                'role' => 'supervisor',
            ],
            [
                'name' => 'Aditya Permana',
                'email' => 'staff.it@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF001',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'Software Developer',
                'role' => 'staff',
            ],
            [
                'name' => 'Rina Wulandari',
                'email' => 'staff.it2@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF002',
                'department' => 'IT',
                'division' => 'IT',
                'position' => 'UI/UX Designer',
                'role' => 'staff',
            ],

            // === HR DIVISION ===
            [
                'name' => 'Putri Handayani',
                'email' => 'manager.hrd@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'HRD001',
                'department' => 'Human Resources',
                'division' => 'HR',
                'position' => 'HR Manager',
                'role' => ['manager', 'hrd'],
            ],
            [
                'name' => 'Eka Susanti',
                'email' => 'supervisor.hr@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'SPV002',
                'department' => 'Human Resources',
                'division' => 'HR',
                'position' => 'HR Supervisor',
                'role' => 'supervisor',
            ],
            [
                'name' => 'Mega Lestari',
                'email' => 'staff.hr@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF003',
                'department' => 'Human Resources',
                'division' => 'HR',
                'position' => 'HR Staff',
                'role' => 'staff',
            ],

            // === FINANCE DIVISION ===
            [
                'name' => 'Hendra Gunawan',
                'email' => 'manager.finance@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'FIN001',
                'department' => 'Finance',
                'division' => 'Finance',
                'position' => 'Finance Manager',
                'role' => ['manager', 'finance'],
            ],
            [
                'name' => 'Yuni Astuti',
                'email' => 'supervisor.finance@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'SPV003',
                'department' => 'Finance',
                'division' => 'Finance',
                'position' => 'Finance Supervisor',
                'role' => 'supervisor',
            ],
            [
                'name' => 'Bayu Firmansyah',
                'email' => 'staff.finance@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF004',
                'department' => 'Finance',
                'division' => 'Finance',
                'position' => 'Accountant',
                'role' => 'staff',
            ],

            // === SALES DIVISION ===
            [
                'name' => 'Teguh Prasetyo',
                'email' => 'manager.sales@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'MGR002',
                'department' => 'Sales',
                'division' => 'Sales',
                'position' => 'Sales Manager',
                'role' => 'manager',
            ],
            [
                'name' => 'Indra Kusuma',
                'email' => 'supervisor.sales@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'SPV004',
                'department' => 'Sales',
                'division' => 'Sales',
                'position' => 'Sales Supervisor',
                'role' => 'supervisor',
            ],
            [
                'name' => 'Wati Setiawan',
                'email' => 'staff.sales@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF005',
                'department' => 'Sales',
                'division' => 'Sales',
                'position' => 'Sales Executive',
                'role' => 'staff',
            ],
            [
                'name' => 'Ahmad Hidayat',
                'email' => 'staff.sales2@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'STF006',
                'department' => 'Sales',
                'division' => 'Sales',
                'position' => 'Sales Representative',
                'role' => 'staff',
            ],

            // === NEW ROLES ===
            // Direktur Operasional (assigned to existing director-level person)
            [
                'name' => 'Rudi Hartono',
                'email' => 'direktur.operasional@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'DIR005',
                'department' => 'Operations',
                'division' => 'Operations',
                'position' => 'Direktur Operasional',
                'role' => 'direktur_operasional',
            ],

            // Purchasing (sub-bagian Finance)
            [
                'name' => 'Lina Marlina',
                'email' => 'purchasing@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'PRC001',
                'department' => 'Finance',
                'division' => 'Finance',
                'position' => 'Purchasing Officer',
                'role' => 'purchasing',
            ],

            // HRD Coordinator
            [
                'name' => 'Sari Dewi',
                'email' => 'hrd.coordinator@confirmme.com',
                'password' => Hash::make('password'),
                'employee_id' => 'HRD002',
                'department' => 'Human Resources',
                'division' => 'HR',
                'position' => 'HRD Coordinator',
                'role' => 'hrd_coordinator',
            ],
        ];

        foreach ($users as $userData) {
            $role = $userData['role'];
            unset($userData['role']);

            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );

            $user->assignRole($role);
        }

        // Create Document Templates
        $templates = [
            [
                'name' => 'Peminjaman Kendaraan Operasional',
                'code' => 'VEHICLE_LOAN',
                'description' => 'Template untuk permohonan peminjaman mobil operasional',
                'approval_type' => 'only',
                'fields' => [
                    'title' => 'Permohonan Peminjaman Mobil Operasional',
                    'description' => 'Permohonan untuk peminjaman mobil operasional perusahaan sesuai SOP.',
                    'priority' => 'normal',
                    'document_category' => 'peminjaman_aset',
                    'dynamic_fields' => [
                        ['name' => 'nama_peminjam', 'label' => 'Nama Peminjam', 'type' => 'text', 'required' => true],
                        ['name' => 'nip', 'label' => 'NIP', 'type' => 'text', 'required' => true],
                        ['name' => 'posisi_divisi', 'label' => 'Posisi/Divisi', 'type' => 'text', 'required' => true],
                        ['name' => 'dipakai_tanggal', 'label' => 'Dipakai Tanggal', 'type' => 'date', 'required' => true],
                        ['name' => 'jam_berangkat', 'label' => 'Jam Berangkat', 'type' => 'time', 'required' => true],
                        ['name' => 'kembali_tanggal', 'label' => 'Kembali Tanggal', 'type' => 'date', 'required' => true],
                        ['name' => 'jam_kembali', 'label' => 'Jam Kembali', 'type' => 'time', 'required' => true],
                        ['name' => 'jenis_mobil', 'label' => 'Jenis Mobil', 'type' => 'select', 'required' => true, 'options' => "Kijang Innova Plat Genap\nKijang Innova Plat Ganjil\nAvanza Plat Genap\nAvanza Plat Ganjil"],
                        ['name' => 'pengemudi', 'label' => 'Pengemudi', 'type' => 'text', 'required' => true],
                        ['name' => 'nomor_sim_a', 'label' => 'Nomor SIM A', 'type' => 'text', 'required' => true],
                        ['name' => 'kebutuhan_project', 'label' => 'Kebutuhan/Project', 'type' => 'textarea', 'required' => true],
                        ['name' => 'lampiran_sim', 'label' => 'Lampiran SIM A', 'type' => 'file', 'required' => true],
                        ['name' => 'data_penumpang', 'label' => 'Data Penumpang dalam Mobil', 'type' => 'table', 'columns' => 'Nama Penumpang', 'required' => false],
                    ],
                ],
            ],
            [
                'name' => 'Surat Ijin Keluar/Masuk Barang Gudang',
                'code' => 'SURAT_IJIN_GUDANG',
                'description' => 'Formulir untuk ijin keluar masuk barang dari gudang.',
                'approval_type' => 'only',
                'fields' => [
                    'title' => 'Surat Ijin Keluar/Masuk Barang Gudang',
                    'description' => 'Mohon ijin keluar/masuk barang.',
                    'priority' => 'normal',
                    'document_category' => 'lainnya',
                    'dynamic_fields' => [
                        ['name' => 'perusahaan', 'label' => 'Perusahaan', 'type' => 'text', 'required' => true],
                        ['name' => 'dokumen_po_do', 'label' => 'Dokumen P.O / D.O', 'type' => 'select', 'options' => 'Ada, Tidak', 'required' => true],
                        ['name' => 'no_po_do', 'label' => 'No. P.O / D.O', 'type' => 'text', 'required' => false],
                        ['name' => 'lokasi_tujuan', 'label' => 'Lokasi Tujuan / Asal Barang', 'type' => 'textarea', 'required' => true],
                        ['name' => 'tanggal_keluar_masuk', 'label' => 'Tanggal Keluar / Masuk', 'type' => 'date', 'required' => true],
                        ['name' => 'jam_keluar_masuk', 'label' => 'Jam Keluar / Masuk', 'type' => 'time', 'required' => true],
                        ['name' => 'alasan', 'label' => 'Alasan (Catatan Petugas)', 'type' => 'textarea', 'required' => false],
                        ['name' => 'items', 'label' => 'Daftar Barang', 'type' => 'table', 'columns' => 'Nama Barang, Project Awal Barang, Jumlah, Actual Project', 'required' => true]
                    ]
                ]
            ],
            [
                'name' => 'Permintaan Pembelian Barang',
                'code' => 'PEMBELIAN_BARANG',
                'description' => 'Formulir untuk permintaan pembelian ATK, P3K, atau Pantry.',
                'approval_type' => 'po',
                'fields' => [
                    'title' => 'Permintaan Pembelian Barang',
                    'description' => 'Pengajuan pembelian barang kebutuhan kantor.',
                    'priority' => 'normal',
                    'dynamic_fields' => [
                        ['name' => 'kategori_pembelian', 'label' => 'Kategori Pembelian', 'type' => 'select', 'options' => 'ALAT TULIS KANTOR, KEBUTUHAN P3K, KEBUTUHAN PANTRY, LAINNYA', 'required' => true],
                        ['name' => 'keterangan', 'label' => 'Keterangan/Projek', 'type' => 'text', 'required' => true],
                        ['name' => 'expected_delivery', 'label' => 'Perkiraan Tgl Penerimaan', 'type' => 'date', 'required' => true],
                        ['name' => 'items', 'label' => 'Daftar Barang', 'type' => 'table', 'columns' => 'Nama Barang, Keterangan, Max Perbulan, Stock Saat Ini, Permintaan (Qty), Harga Perk./Item', 'required' => true]
                    ]
                ]
            ]
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

        // Create Budgets
        $budgets = [
            [
                'name' => 'Anggaran IT 2026',
                'code' => 'IT-2026',
                'department' => 'IT',
                'fiscal_year' => 2026,
                'total_amount' => 500000000,
            ],
            [
                'name' => 'Anggaran HR 2026',
                'code' => 'HR-2026',
                'department' => 'Human Resources',
                'fiscal_year' => 2026,
                'total_amount' => 300000000,
            ],
            [
                'name' => 'Anggaran Operasional 2026',
                'code' => 'OPS-2026',
                'department' => 'Operations',
                'fiscal_year' => 2026,
                'total_amount' => 1000000000,
            ],
        ];

        foreach ($budgets as $budget) {
            Budget::firstOrCreate(
                ['code' => $budget['code']],
                [
                    ...$budget,
                    'created_by' => 1,
                    'is_active' => true,
                ]
            );
        }

        // Seed Email Templates
        $this->call(EmailTemplateSeeder::class);

        $this->command->info('Database seeded successfully!');
        $this->command->info('');
        $this->command->info('Default login credentials:');
        $this->command->info('Admin: admin@confirmme.com / password');
        $this->command->info('Staff IT: staff.it@confirmme.com / password');
        $this->command->info('Staff IT 2: staff.it2@confirmme.com / password');
        $this->command->info('Staff HR: staff.hr@confirmme.com / password');
        $this->command->info('Staff Finance: staff.finance@confirmme.com / password');
        $this->command->info('Staff Sales: staff.sales@confirmme.com / password');
        $this->command->info('Supervisor IT: supervisor.it@confirmme.com / password');
        $this->command->info('Manager IT: manager.it@confirmme.com / password');
        $this->command->info('Manager HRD: manager.hrd@confirmme.com / password');
        $this->command->info('Manager Finance: manager.finance@confirmme.com / password');
        $this->command->info('Direktur HR: direktur.hr@confirmme.com / password');
        $this->command->info('Direktur Finance: direktur.finance@confirmme.com / password');
        $this->command->info('Direktur IT: direktur.it@confirmme.com / password');
        $this->command->info('Direktur Sales: direktur.sales@confirmme.com / password');
        $this->command->info('Presiden Direktur: karsinem.k.semitro@gmail.com / password');
        $this->command->info('Direktur Operasional: direktur.operasional@confirmme.com / password');
        $this->command->info('Purchasing: purchasing@confirmme.com / password');
        $this->command->info('HRD Coordinator: hrd.coordinator@confirmme.com / password');
    }
}
