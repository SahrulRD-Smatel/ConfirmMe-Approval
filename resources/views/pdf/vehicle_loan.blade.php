<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permohonan Peminjaman Mobil Operasional</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.5;
            color: #000;
        }

        .container {
            padding: 30px 50px;
        }

        /* Header Table */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 2px;
        }

        .header-table td {
            border: 1px solid #000;
            padding: 5px;
            vertical-align: middle;
        }
        
        .header-table .logo-col {
            width: 25%;
            text-align: center;
        }

        .header-table .title-col {
            width: 45%;
            text-align: center;
            font-size: 14px;
            font-weight: bold;
        }

        .header-table .meta-col {
            width: 30%;
            font-size: 9px;
            padding: 0;
        }

        .meta-inner-table {
            width: 100%;
            border-collapse: collapse;
        }

        .meta-inner-table td {
            border: none;
            border-bottom: 1px solid #000;
            padding: 2px 5px;
        }
        
        .meta-inner-table tr:last-child td {
            border-bottom: none;
        }

        .meta-inner-table td:first-child {
            width: 40%;
            border-right: 1px solid #000;
        }

        .doc-title {
            background-color: #e5e7eb;
            text-align: center;
            font-size: 14px;
            font-weight: normal;
            padding: 8px;
            border: 1px solid #000;
            border-top: none;
            margin-bottom: 30px;
        }
        
        .sub-title {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 30px;
        }

        /* Form Content */
        .intro-text {
            margin-bottom: 30px;
        }

        .form-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .form-table td {
            padding: 8px 5px;
            vertical-align: top;
        }

        .form-table td.label {
            width: 30%;
        }
        
        .disclaimer {
            margin-top: 20px;
            margin-bottom: 30px;
            text-align: justify;
        }

        /* Signatures */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 30px;
            text-align: center;
        }

        .sig-col {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
        }
        
        .sig-title {
            margin-bottom: 60px;
        }
        
        .sig-name {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 180px;
            padding-bottom: 2px;
            font-weight: bold;
        }
        
        .sig-nip {
            margin-top: 3px;
        }

        .date-right {
            text-align: left;
            padding-left: 66%;
            margin-bottom: 20px;
        }

        /* SOP Section (Page 2) */
        .page-break {
            page-break-before: always;
        }
        
        .sop-list {
            padding-left: 20px;
            text-align: justify;
            margin-bottom: 30px;
        }
        
        .sop-list li {
            margin-bottom: 5px;
        }
        
        .text-red {
            color: red;
        }

        .sop-signatures {
            display: table;
            width: 100%;
            margin-top: 30px;
        }
        
        .sop-sig-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .sop-sig-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 10%;
        }

    </style>
</head>

<body>
    @php
        $fields = $document->metadata ?? [];
        $peminjam = $fields['nama_peminjam'] ?? $document->user->name;
        $nip = $fields['nip'] ?? $document->user->employee_id ?? '-';
        $posisi = $fields['posisi_divisi'] ?? $document->user->position ?? '-';
        $dipakai = isset($fields['dipakai_tanggal']) ? \Carbon\Carbon::parse($fields['dipakai_tanggal'])->format('d/m/Y') : '-';
        $jamBerangkat = $fields['jam_berangkat'] ?? '-';
        $kembali = isset($fields['kembali_tanggal']) ? \Carbon\Carbon::parse($fields['kembali_tanggal'])->format('d/m/Y') : '-';
        $jamKembali = $fields['jam_kembali'] ?? '-';
        $jenisMobil = $fields['jenis_mobil'] ?? '-';
        $pengemudi = $fields['pengemudi'] ?? '-';
        $simA = $fields['nomor_sim_a'] ?? '-';
        $kebutuhan = $fields['kebutuhan_project'] ?? '-';
        
        $approvals = $document->approvals()->with('approver')->orderBy('approval_order')->get();
        $ga = $approvals->filter(fn($a) => $a->approver_role === 'role:ga')->last();
        $dirOps = $approvals->filter(fn($a) => $a->approver_role === 'role:direktur_operasional')->last();
        $dirFin = $approvals->filter(fn($a) => $a->approver_role === 'role:direktur_finance')->last();
    @endphp

    <!-- PAGE 1 -->
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-col">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 50px; max-width: 100px;">
                </td>
                <td class="title-col">Formulir Kerja</td>
                <td class="meta-col">
                    <table class="meta-inner-table">
                        <tr>
                            <td>No. Dokumen</td>
                            <td>: ISDU/HRGA/FK-20</td>
                        </tr>
                        <tr>
                            <td>Status Revisi</td>
                            <td>: 01</td>
                        </tr>
                        <tr>
                            <td>Tanggal Berlaku</td>
                            <td>: 29 April 2026</td>
                        </tr>
                        <tr>
                            <td>Halaman</td>
                            <td>: 01 dari 03</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="doc-title">
            PENGGUNAAN KENDARAAN OPERASIONAL
        </div>
        
        <div class="sub-title">
            PERMOHONAN PEMINJAMAN MOBIL OPERASIONAL
        </div>

        <div class="intro-text">
            Yang bertanda tangan di bawah ini mengajukan permohonan pinjam mobil operasional PT Info Solusindo Data Utama :
        </div>

        <table class="form-table">
            <tr>
                <td class="label">Nama Peminjam</td>
                <td>: {{ $peminjam }}</td>
            </tr>
            <tr>
                <td class="label">NIP</td>
                <td>: {{ $nip }}</td>
            </tr>
            <tr>
                <td class="label">Posisi/Divisi</td>
                <td>: {{ $posisi }}</td>
            </tr>
            <tr>
                <td class="label">Dipakai Tanggal</td>
                <td>: <span style="display:inline-block; width:120px; border-bottom:1px solid #000;">{{ $dipakai }}</span> Jam Berangkat: <span style="display:inline-block; width:80px; border-bottom:1px solid #000;">{{ $jamBerangkat }} WIB</span></td>
            </tr>
            <tr>
                <td class="label">Kembali Tanggal</td>
                <td>: <span style="display:inline-block; width:120px; border-bottom:1px solid #000;">{{ $kembali }}</span> Jam Kembali : <span style="display:inline-block; width:80px; border-bottom:1px solid #000;">{{ $jamKembali }} WIB</span></td>
            </tr>
            <tr>
                <td class="label">Jenis Mobil</td>
                <td>: {{ $jenisMobil }}</td>
            </tr>
            <tr>
                <td class="label">Pengemudi</td>
                <td>: {{ $pengemudi }}</td>
            </tr>
            <tr>
                <td class="label">Nomor SIM A</td>
                <td>: {{ $simA }}</td>
            </tr>
            <tr>
                <td class="label">Kebutuhan/Project</td>
                <td>: {{ $kebutuhan }}</td>
            </tr>
        </table>
        
        <div class="disclaimer">
            Peminjam bertanggung jawab atas pemeliharaan, perawatan selama dalam pemakaian ditanggung oleh peminjam dan mencucikan mobil operasional sebelum dikembalikan ke perusahaan. Dan segala kerusakan akan menjadi tanggung jawab peminjam untuk memperbaiki.
        </div>
        
        <div class="date-right">
            Jakarta, {{ $document->created_at->format('d F Y') }}
        </div>
        
        <div style="text-align: center; margin-bottom: 20px;">Disetujui,</div>

        <!-- Signatures -->
        <div class="signature-section" style="margin-top: 10px;">
            <div class="sig-col">
                <div class="sig-title" style="text-align: left;">Pemohon</div>
                <div style="text-align: left;">
                    <div class="sig-name">{{ $peminjam }}</div>
                    <div class="sig-nip">NIP. {{ $nip }}</div>
                </div>
            </div>
            <div class="sig-col">
                <div class="sig-title" style="text-align: center;">Direktur Operasional</div>
                <div style="text-align: center;">
                    <div class="sig-name">{{ $dirOps ? $dirOps->approver->name : '................................' }}</div>
                    <div class="sig-nip">NIP. {{ $dirOps ? $dirOps->approver->employee_id : '..................' }}</div>
                </div>
            </div>
            <div class="sig-col">
                <div class="sig-title" style="text-align: right;">Direktur Keuangan</div>
                <div style="text-align: right;">
                    <div class="sig-name">{{ $dirFin ? $dirFin->approver->name : '................................' }}</div>
                    <div class="sig-nip">NIP. {{ $dirFin ? $dirFin->approver->employee_id : '..................' }}</div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- PAGE 2 (SOP) -->
    <div class="page-break"></div>
    <div class="container">
        <!-- Header -->
        <table class="header-table">
            <tr>
                <td class="logo-col">
                    <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="max-height: 50px; max-width: 100px;">
                </td>
                <td class="title-col">Formulir Kerja</td>
                <td class="meta-col">
                    <table class="meta-inner-table">
                        <tr>
                            <td>No. Dokumen</td>
                            <td>: ISDU/HRGA/FK-20</td>
                        </tr>
                        <tr>
                            <td>Status Revisi</td>
                            <td>: 01</td>
                        </tr>
                        <tr>
                            <td>Tanggal Berlaku</td>
                            <td>: 29 April 2026</td>
                        </tr>
                        <tr>
                            <td>Halaman</td>
                            <td>: 02 dari 03</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="doc-title">
            PENGGUNAAN KENDARAAN OPERASIONAL
        </div>
        
        <div class="sub-title" style="margin-bottom: 20px; font-size: 16px;">
            STANDARD OPERASIONAL PROSEDUR ( SOP )<br>
            PENGGUNAAN KENDARAAN OPERASIONAL
        </div>
        
        <ol class="sop-list">
            <li>Peminjam menghubungi bagian General Affair untuk memastikan ketersediaan kendaraan operasional serta memperoleh Form Peminjaman Kendaraan Operasional.</li>
            <li><strong>Peminjam wajib mengisi Form Peminjaman Kendaraan Operasional secara lengkap dan mengajukan persetujuan kepada Direktur Operasional dan Direktur Keuangan.</strong></li>
            <li>Pada saat pengambilan kendaraan, peminjam wajib menunjukkan Form Peminjaman yang telah disetujui sebagai bukti. Setelah dilakukan pengecekan kendaraan bersama Driver Operasional, kunci kendaraan akan diserahkan kepada peminjam.</li>
            <li><strong>Waktu pengambilan dan pengembalian kendaraan harus sesuai dengan jadwal yang tercantum dalam Form Peminjaman Kendaraan Operasional.</strong></li>
            <li><strong>Pengemudi kendaraan operasional wajib merupakan karyawan aktif perusahaan.</strong></li>
            <li><strong>Biaya bahan bakar dan pengisian saldo e-money menjadi tanggung jawab peminjam dengan mengajukan biaya perjalanan dinas ke Perusahaan.</strong></li>
            <li class="text-red">Dilarang:<br>
                Merokok, menggunakan vape, atau rokok elektrik di dalam kendaraan.<br>
                Menyalakan AC saat kaca kendaraan dalam kondisi terbuka.<br>
                Menyalakan AC secara terus-menerus lebih dari 4 (empat) jam.
            </li>
            <li><strong>Pengemudi wajib mematikan AC sebelum mematikan mesin kendaraan.</strong></li>
            <li><strong>Kendaraan harus diistirahatkan dengan mematikan mesin minimal 30 (tiga puluh) menit setelah digunakan selama 4 (empat) jam berturut-turut.</strong></li>
            <li><strong>Kendaraan harus dikembalikan dalam kondisi bersih <em>body</em> luar, ban/kaki mobil dan interiornya pastikan tidak ada barang/sampah yang tertinggal di mobil. Biaya pembersihan luar dan dalam mobil dapat diajukan dalam klaim transportasi atau masuk dalam penyelesaian biaya perjalanan dinas.</strong></li>
            <li><strong>Segala risiko selama penggunaan kendaraan menjadi tanggung jawab peminjam. Kendaraan harus dikembalikan dalam kondisi yang sama seperti saat diterima dari General Affair.</strong></li>
            <li><strong>Apabila terjadi kerusakan selama masa peminjaman, peminjam/pengemudi wajib mengurus administrasi serta melakukan perbaikan sesuai dengan keputusan manajemen. Nilai penggantian biaya oleh pengemudi/peminjam ditetapkan oleh Direktur Keuangan dan itu adalah mutlak.</strong></li>
        </ol>
        
        <div class="sop-signatures">
            <div class="sop-sig-left">
                <div style="margin-bottom: 50px;">
                    Dibuat oleh :<br>
                    General Affair
                </div>
                <div class="sig-name">{{ $ga ? $ga->approver->name : '................................' }}</div>
                <div class="sig-nip">NIP : {{ $ga ? $ga->approver->employee_id : '..................' }}</div>
            </div>
            <div class="sop-sig-right">
                <div style="margin-bottom: 50px;">
                    Jakarta,<br>
                    Disepakati oleh :<br>
                    Peminjam
                </div>
                <div class="sig-name">{{ $peminjam }}</div>
                <div class="sig-nip">NIP : {{ $nip }}</div>
            </div>
        </div>
    </div>
</body>
</html>
