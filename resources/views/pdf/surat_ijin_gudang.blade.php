<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Ijin Keluar / Masuk Barang Gudang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }

        .container {
            padding: 20px 40px;
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
            background-color: #d1d5db;
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            padding: 5px;
            border: 1px solid #000;
            border-top: none;
            margin-bottom: 20px;
        }

        /* Form Content */
        .doc-meta {
            display: table;
            width: 100%;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .doc-meta-left {
            display: table-cell;
            width: 60%;
        }

        .doc-meta-right {
            display: table-cell;
            width: 40%;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .identity-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }

        .identity-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .identity-table td.bullet {
            width: 15px;
        }

        .identity-table td.label {
            width: 150px;
        }

        .identity-table td.colon {
            width: 10px;
        }
        
        .highlight {
            background-color: #bfdbfe; /* Light blue highlight matching original */
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
            text-align: center;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .items-table th {
            font-weight: bold;
        }
        
        .catatan-petugas {
            margin-bottom: 20px;
            margin-top: 10px;
        }

        /* Signatures */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
            text-align: center;
        }

        .sig-col {
            display: table-cell;
            width: 33.33%;
            vertical-align: top;
        }
        
        .sig-title {
            margin-bottom: 50px;
        }
        
        .sig-name {
            display: inline-block;
            border-bottom: 1px dotted #000;
            min-width: 150px;
            padding-bottom: 2px;
        }

        /* Footer */
        .lampiran {
            margin-top: 30px;
        }

        .footer-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 40px;
            font-size: 9px;
            border: 1px solid #000;
        }

        .footer-table td {
            border: 1px solid #000;
            padding: 3px 5px;
        }

    </style>
</head>

<body>
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
                            <td>: ISDU/HRGA/FK-15</td>
                        </tr>
                        <tr>
                            <td>Status Revisi</td>
                            <td>: 00</td>
                        </tr>
                        <tr>
                            <td>Tanggal Berlaku</td>
                            <td>: 11 November 2024</td>
                        </tr>
                        <tr>
                            <td>Halaman</td>
                            <td>: 01 dari 01</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <div class="doc-title">
            SURAT IJIN KELUAR / MASUK BARANG GUDANG
        </div>

        @php
            $fields = $document->metadata ?? [];
            $items = $fields['items'] ?? [];
            
            // Format dynamic fields properly
            $perusahaan = $fields['perusahaan'] ?? $document->user->company ?? 'PT Info Solusindo Data Utama';
            $departemen = $document->user->department ?? '-';
            $jabatan = $document->user->position ?? '-';
            $dokumenPoDo = $fields['dokumen_po_do'] ?? 'Tidak';
            $noPoDo = $fields['no_po_do'] ?? '-';
            $lokasiTujuan = $fields['lokasi_tujuan'] ?? '-';
            
            $tgl = isset($fields['tanggal_keluar_masuk']) ? \Carbon\Carbon::parse($fields['tanggal_keluar_masuk'])->format('d/m/Y') : '-';
            $jam = $fields['jam_keluar_masuk'] ?? '-';
            $alasan = $fields['alasan'] ?? '-';
        @endphp

        <!-- Meta -->
        <div class="doc-meta">
            <div class="doc-meta-left">
                No. Dokumen : {{ $document->document_number }}
            </div>
            <div class="doc-meta-right">
                <span class="highlight">Tanggal Dok : {{ $document->created_at->format('d/m/Y') }}</span>
            </div>
        </div>

        <!-- A. Identitas Pemohon -->
        <div class="section-title">A. Identitas Pemohon</div>
        <table class="identity-table">
            <tr>
                <td class="bullet">•</td>
                <td class="label">Nama Pemohon</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $document->user->name }}</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td class="label">Perusahaan</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $perusahaan }}</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td class="label">Departemen / Divisi</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $departemen }}</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td class="label">Jabatan</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $jabatan }}</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td class="label">Dokumen P.O / D.O</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $dokumenPoDo }}* &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; No : {{ $noPoDo }}</td>
            </tr>
        </table>

        <!-- B. Rincian Barang -->
        <div class="section-title">B. Rincian Barang (Lampirkan detail jika tidak cukup)</div>
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 40%;">Nama Barang</th>
                    <th style="width: 25%;">Project Awal Barang</th>
                    <th style="width: 10%;">Jumlah</th>
                    <th style="width: 20%;">Actual Project</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td style="text-align: left;">{{ $item['Nama Barang'] ?? '-' }}</td>
                    <td>{{ $item['Project Awal Barang'] ?? '-' }}</td>
                    <td>{{ $item['Jumlah'] ?? '-' }}</td>
                    <td>{{ $item['Actual Project'] ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="padding: 20px;">Belum ada barang ditambahkan.</td>
                </tr>
                @endforelse
                <!-- Add empty rows for filler if less than 3 items -->
                @for($i = count($items); $i < 3; $i++)
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- C. Lokasi Tujuan -->
        <div class="section-title"><span class="highlight">C. Lokasi Tujuan / Asal Barang*</span></div>
        <div style="border-bottom: 1px dotted #000; padding-bottom: 2px; margin-bottom: 15px;">
            {{ $lokasiTujuan }}
        </div>

        <!-- D. Waktu -->
        <div class="section-title">D. Waktu Keluar / Masuk <span class="highlight">Barang</span></div>
        <table class="identity-table" style="margin-bottom: 10px;">
            <tr>
                <td class="bullet">•</td>
                <td class="label">Tanggal</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $tgl }}</td>
            </tr>
            <tr>
                <td class="bullet">•</td>
                <td class="label">Jam Keluar/Masuk Barang</td>
                <td class="colon">:</td>
                <td class="highlight">{{ $jam }}</td>
            </tr>
        </table>

        <!-- Catatan -->
        <div class="catatan-petugas">
            <div style="font-weight: bold; margin-bottom: 5px;">Catatan Petugas :</div>
            <div style="font-weight: bold; margin-bottom: 10px;">Apakah barang sesuai dengan peruntukkan Project awal? Iya / Tidak*</div>
            <div style="margin-bottom: 5px;"><span class="highlight">Alasan:</span></div>
            <div style="border-bottom: 1px dotted #000; height: 15px;">{{ $alasan }}</div>
        </div>

        <!-- Signatures -->
        @php
            $approvals = $document->approvals()->with('approver')->orderBy('approval_order')->get();
            $petugas = $approvals->filter(fn($a) => in_array($a->approver_role, ['role:ga', 'role:admin_operasional']))->last();
            $disetujui = $approvals->filter(fn($a) => in_array($a->approver_role, ['role:direktur_operasional', 'role:manager']))->last();
        @endphp
        <div class="signature-section">
            <div class="sig-col">
                <div class="sig-title">Pemohon</div>
                <div class="sig-name">( {{ $document->user->name }} )</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Petugas</div>
                <div class="sig-name">( {{ $petugas ? $petugas->approver->name : '................................' }} )</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Setuju/Tidak*<br>Disetujui</div>
                <div class="sig-name">( {{ $disetujui ? $disetujui->approver->name : '................................' }} )</div>
            </div>
        </div>

        <!-- Lampiran -->
        <div class="lampiran">
            <div>Lampiran :</div>
            <ol style="padding-left: 20px;">
                <li>PO (Purchase Order)</li>
                <li>Delivery Order atau Surat Jalan (DO)</li>
                <li>Surat Tugas</li>
            </ol>
        </div>

        <!-- Footer -->
        <table class="footer-table">
            <tr>
                <td colspan="3" style="border-bottom: none;">Surat Ijin Keluar / Masuk Barang Gudang</td>
                <td style="text-align: right; border-bottom: none;">IV-D-35</td>
            </tr>
            <tr>
                <td style="width: 20%;">Edisi ke:</td>
                <td style="width: 35%;">Berlaku mulai tanggal: 01 Januari 2025</td>
                <td style="width: 45%;" colspan="2">Diverifikasi oleh :</td>
            </tr>
            <tr>
                <td>Revisi ke:</td>
                <td>Tanggal yang digantikan : -</td>
                <td colspan="2"></td>
            </tr>
        </table>
    </div>
</body>
</html>
