<?php

$keys = [
    '2FA Security (OTP)',
    'Mewajibkan input OTP dari email saat proses approval untuk peran Direktur dan Presiden Direktur.',
    'Modul Keuangan (Finance)',
    'Akses ke menu Budgets, Purchase Orders, dan Invoices.',
    'Modul HRIS (Cuti & Absensi)',
    'Akses ke menu pengajuan Cuti dan laporan Absensi.',
    'Modul Pinjaman Karyawan',
    'Akses ke menu pengajuan Pinjaman (Employee Loan).'
];

foreach (['lang/en.json', 'lang/zh.json', 'lang/ja.json'] as $file) {
    $data = json_decode(file_get_contents($file), true);
    foreach ($keys as $k) {
        if (!isset($data[$k])) {
            $data[$k] = $k;
        }
    }
    file_put_contents($file, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}
echo "Keys synced!\n";
