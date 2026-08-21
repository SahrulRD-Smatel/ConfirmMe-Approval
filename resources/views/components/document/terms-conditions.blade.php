@props([
    'type' => 'only', // only, po, invoice, loan
    'companyName' => 'PT INFO SOLUSINDO DATA UTAMA',
])

@php
    $terms = match($type) {
        'po' => [
            'title' => 'SYARAT & KETENTUAN PEMESANAN:',
            'items' => [
                'Validitas: PO ini dianggap sah apabila telah ditandatangani secara lengkap sesuai alur approval ' . $companyName . ', berakhir di President Director.',
                'Kesesuaian Barang: Barang/jasa yang dikirim harus sesuai dengan spesifikasi yang tertera. Ketidaksesuaian dapat mengakibatkan penundaan pembayaran atau pembatalan sepihak.',
                'Penagihan: Invoice tagihan wajib melampirkan salinan PO ini yang sudah disetujui, Berita Acara Serah Terima (BAST), dan ditujukan kepada Departemen Finance.',
                'Perubahan: Segala bentuk perubahan harga atau spesifikasi setelah PO diterbitkan wajib melalui persetujuan ulang dari President Director.',
            ],
        ],
        'invoice' => [
            'title' => 'KETENTUAN PEMBAYARAN:',
            'items' => [
                'Jadwal Pembayaran: Pembayaran akan diproses dalam waktu 14 hari kerja setelah invoice diterima dengan lengkap dan benar oleh Finance.',
                'Kelengkapan Dokumen: Tanpa tanda tangan verifikasi dari Manager Divisi dan Finance, President Director berhak menangguhkan persetujuan pembayaran.',
                'Metode Pembayaran: Transfer hanya dilakukan ke rekening atas nama Vendor yang terdaftar. Perubahan rekening harus disertai surat resmi bermaterai.',
            ],
        ],
        'loan' => [
            'title' => 'PERNYATAAN & KETENTUAN PINJAMAN:',
            'items' => [
                'Persetujuan Akhir: Pinjaman bersifat diskresi perusahaan dan hanya berlaku jika telah disetujui secara final oleh President Director.',
                'Metode Pengembalian: Dengan menandatangani form ini, Karyawan memberikan kuasa kepada ' . $companyName . ' untuk memotong gaji bulanan sesuai nominal cicilan yang disepakati.',
                'Pelunasan Dipercepat: Apabila Karyawan berhenti bekerja (Resign/PHK) sebelum pinjaman lunas, maka sisa pinjaman akan dipotong langsung dari sisa gaji, uang kompensasi, atau hak-hak karyawan lainnya.',
                'Penggunaan: Pinjaman ini harus digunakan sesuai alasan yang diajukan dan tidak dapat dipindahtangankan.',
            ],
        ],
        default => null,
    };
@endphp

@if($terms)
<div class="terms-conditions mt-7 text-xs leading-6 text-gray-700 print:mt-4 print:text-black">
    <div class="rounded-2xl border border-gray-200 bg-gray-50/80 p-5 shadow-sm print:bg-white">
        <h4 class="font-bold mb-2">{{ $terms['title'] }}</h4>
        <ol class="list-decimal list-inside space-y-2">
            @foreach($terms['items'] as $item)
                <li class="text-justify leading-relaxed">{{ $item }}</li>
            @endforeach
        </ol>
    </div>
</div>
@endif
