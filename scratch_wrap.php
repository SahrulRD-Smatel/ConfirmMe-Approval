<?php

$file = 'resources/views/leave-requests/create.blade.php';
$content = file_get_contents($file);

$replacements = [
    'Ajukan Cuti' => "{{ __('Ajukan Cuti') }}",
    'Isi form berikut untuk mengajukan cuti' => "{{ __('Isi form berikut untuk mengajukan cuti') }}",
    'Sisa Cuti Tahunan Anda' => "{{ __('Sisa Cuti Tahunan Anda') }}",
    '{{ $leaveBalance }} hari' => "{{ $leaveBalance }} {{ __('hari') }}",
    'Saldo cuti<br>tahun berjalan' => "{!! __('Saldo cuti<br>tahun berjalan') !!}",
    'Form pengajuan' => "{{ __('Form pengajuan') }}",
    'Detail cuti' => "{{ __('Detail cuti') }}",
    'Lengkapi informasi berikut sebelum mengirim permintaan.' => "{{ __('Lengkapi informasi berikut sebelum mengirim permintaan.') }}",
    'value="Jenis Cuti"' => 'value="{{ __(\'Jenis Cuti\') }}"',
    '-- Pilih Jenis Cuti --' => "{{ __('-- Pilih Jenis Cuti --') }}",
    'Cuti Tahunan' => "{{ __('Cuti Tahunan') }}",
    'Sakit' => "{{ __('Sakit') }}",
    'Cuti Tanpa Gaji' => "{{ __('Cuti Tanpa Gaji') }}",
    'Lainnya' => "{{ __('Lainnya') }}",
    'Periode Cuti' => "{{ __('Periode Cuti') }}",
    'Pilih tanggal mulai dan selesai' => "{{ __('Pilih tanggal mulai dan selesai') }}",
    'value="Tanggal Mulai"' => 'value="{{ __(\'Tanggal Mulai\') }}"',
    'value="Tanggal Selesai"' => 'value="{{ __(\'Tanggal Selesai\') }}"',
    'Durasi cuti:' => "{{ __('Durasi cuti:') }}",
    "duration + ' hari' : 'Pilih tanggal'" => "duration + ' ' + '{{ __('hari') }}' : '{{ __('Pilih tanggal') }}'",
    'value="Alasan"' => 'value="{{ __(\'Alasan\') }}"',
    'placeholder="Jelaskan alasan pengajuan cuti Anda..."' => 'placeholder="{{ __(\'Jelaskan alasan pengajuan cuti Anda...\') }}"',
    'value="Lampiran (Opsional)"' => 'value="{{ __(\'Lampiran (Opsional)\') }}"',
    'Untuk cuti sakit, lampirkan surat dokter. Format: PDF, JPG, PNG (Max 5MB)' => "{{ __('Untuk cuti sakit, lampirkan surat dokter. Format: PDF, JPG, PNG (Max 5MB)') }}",
    '>Batal<' => ">{{ __('Batal') }}<",
    '>Ajukan Cuti<' => ">{{ __('Ajukan Cuti') }}<",
    'Pengajuan cuti' => "{{ __('Pengajuan cuti') }}",
    'Pastikan informasi yang Anda masukkan sudah benar sebelum dikirim untuk diproses.' => "{{ __('Pastikan informasi yang Anda masukkan sudah benar sebelum dikirim untuk diproses.') }}",
    '>Checklist<' => ">{{ __('Checklist') }}<",
    'Periksa kembali periode cuti.' => "{{ __('Periksa kembali periode cuti.') }}",
    'Gunakan alasan yang jelas.' => "{{ __('Gunakan alasan yang jelas.') }}",
    'Lampirkan dokumen jika diperlukan.' => "{{ __('Lampirkan dokumen jika diperlukan.') }}",
];

foreach ($replacements as $search => $replace) {
    // Avoid double wrapping if already wrapped
    if (strpos($content, $replace) === false) {
        $content = str_replace($search, $replace, $content);
    }
}

file_put_contents($file, $content);
echo "Replaced successfully!\n";
