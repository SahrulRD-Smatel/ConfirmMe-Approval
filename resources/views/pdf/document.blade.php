<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }} - {{ $document->title }}</title>
    <style>
        @php
            $isLandscape = ($orientation ?? 'portrait') === 'landscape';
        @endphp

        @if($isLandscape)
            {{-- LANDSCAPE STYLES (DAFTAR PENGADAAN / PEMBELIAN BARANG) --}}
            @page {
                size: A4 landscape;
                margin: 20mm 22mm 18mm 22mm;
            }

            html, body {
                margin: 0;
                padding: 0;
                font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
                font-size: 8.5px;
                color: #000;
                line-height: 1.25;
                background: #fff;
            }

            table { border-collapse: collapse; }
            .w-full { width: 100%; }

            .header-table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
            .header-table td { vertical-align: top; padding: 0; }

            .doc-title-main { font-size: 13px; font-weight: bold; text-align: center; letter-spacing: 0.5px; margin-bottom: 2px; text-transform: uppercase; }
            .doc-title-sub { font-size: 11px; font-weight: bold; text-align: center; font-style: italic; }

            .meta-table { width: 100%; border-collapse: collapse; font-size: 8.5px; }
            .meta-table td { padding: 1.5px 0; vertical-align: middle; }
            .meta-label { width: 95px; color: #000; }
            .meta-colon { width: 10px; text-align: center; }
            .meta-value-line { border-bottom: 1px solid #777; padding-bottom: 1px; font-weight: 500; }

            .data-table { width: 100%; border: 1px solid #000; border-collapse: collapse; margin-top: 4px; font-size: 8.5px; }
            .data-table thead { display: table-header-group; }
            .data-table tr { page-break-inside: avoid; page-break-after: auto; }
            .data-table th { border: 1px solid #000; padding: 4px 3px; text-align: center; font-weight: bold; background-color: #fff; vertical-align: middle; }
            .data-table td { border: 1px solid #000; padding: 3px 4px; vertical-align: middle; }

            .category-row td { background-color: #F7CE3E !important; font-weight: bold; padding: 3px 6px; border: 1px solid #000; }
            .total-row td { font-weight: bold; }
            .bg-yellow { background-color: #F7CE3E !important; }

            .sig-container { width: 100%; border-collapse: collapse; margin-top: 12px; page-break-inside: avoid; }
            .sig-box-cell { width: 25%; padding: 0 4px; vertical-align: top; }
            .sig-card { width: 100%; border: 1px solid #000; border-collapse: collapse; }
            .sig-card-header { border-bottom: 1px solid #000; text-align: center; font-weight: bold; font-size: 9.5px; padding: 3px; background-color: #fff; height: 18px; }
            .sig-card-body { text-align: center; vertical-align: middle; height: 50px; padding: 2px; position: relative; }
            .sig-card-footer { border-top: 1px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 3px 2px; background-color: #fff; height: 20px; }

            .text-center { text-align: center; }
            .text-right { text-align: right; }
            .text-left { text-align: left; }

            .verification-footer { margin-top: 12px; padding: 6px 10px; background-color: #f9fafb; border: 1px solid #e5e7eb; font-size: 8px; color: #555; page-break-inside: avoid; }
            .verification-footer table { width: 100%; border-collapse: collapse; }
            .verification-footer td { vertical-align: middle; padding: 0; }
        @else
            {{-- PORTRAIT STYLES (STANDAR RESMI FORMULIR KERJA - GAMBAR 1) --}}
            @page {
                margin: 140px 45px 55px 45px !important;
            }

            header {
                position: fixed;
                top: -115px;
                left: 0;
                right: 0;
                height: 105px;
            }
            
            header .company-header {
                margin-bottom: 0 !important;
            }

            html, body, div, p, span, table, th, td, h1, h2, h3, h4, h5, h6 {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'DejaVu Sans', sans-serif;
                font-size: 11px;
                line-height: 1.5;
                color: #333;
                background: #fff;
            }

            .container {
                width: 100%;
            }

            /* Sub-Header Banner (Category matching Image 1) */
            .sub-header-banner {
                background-color: #1e3a5f;
                color: white;
                text-align: center;
                padding: 6px 15px;
                margin-bottom: 8px;
                font-size: 11.5px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .doc-title-banner {
                text-align: center;
                padding: 4px 15px;
                margin-bottom: 14px;
                font-size: 13px;
                font-weight: bold;
                text-transform: uppercase;
                letter-spacing: 0.5px;
                color: #1a1a1a;
            }

            /* Info Table (Dynamic Form Fields matching Image 1) */
            .info-table {
                width: 100%;
                margin-bottom: 12px;
                border-collapse: collapse;
            }

            .info-table th {
                text-align: left;
                width: 150px;
                padding: 2.5px 0;
                color: #333;
                font-weight: normal;
                font-size: 11px;
                vertical-align: top;
                border: none;
            }

            .info-table td {
                padding: 2.5px 0;
                font-size: 11px;
                color: #111;
                vertical-align: top;
                border: none;
            }

            /* Data Table (Dynamic Table Fields e.g. Data Penumpang) */
            .data-table-container {
                margin-top: 10px;
                margin-bottom: 14px;
                page-break-inside: auto;
            }

            .data-table {
                width: 100%;
                margin-top: 6px;
                margin-bottom: 10px;
                border-collapse: collapse;
                table-layout: auto;
                page-break-inside: auto;
            }

            .data-table thead {
                display: table-header-group;
            }

            .data-table tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            .data-table th {
                text-align: left;
                background-color: #f3f4f6;
                color: #1f2937;
                font-weight: bold;
                text-transform: uppercase;
                font-size: 9px;
                letter-spacing: 0.5px;
                padding: 6px 8px;
                border: 1px solid #d1d5db;
            }

            .data-table td {
                padding: 5px 8px;
                border: 1px solid #e5e7eb;
                font-size: 10px;
                color: #222;
            }

            .data-table tr:nth-child(even) td {
                background-color: #f9fafb;
            }

            .section-title {
                font-size: 11px;
                font-weight: bold;
                color: #7F1D1D;
                margin-top: 10px;
                margin-bottom: 6px;
                padding-bottom: 3px;
                border-bottom: 1.5px solid #7F1D1D;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            .attachment-box {
                margin-top: 8px;
                margin-bottom: 10px;
                page-break-inside: avoid;
            }

            .attachment-img {
                max-width: 220px;
                max-height: 140px;
                border: 1px solid #d1d5db;
                border-radius: 4px;
                display: block;
                margin-top: 4px;
            }

            .signature-section {
                margin-top: 20px;
                page-break-inside: avoid;
            }

            .signature-table {
                width: 100%;
                border-collapse: collapse;
            }

            .signature-table td {
                text-align: center;
                vertical-align: top;
                padding: 4px 6px;
            }

            .sig-role-label {
                font-size: 10px;
                font-weight: bold;
                color: #333;
                margin-bottom: 6px;
            }

            .sig-qr-box {
                width: 60px;
                height: 60px;
                margin: 4px auto;
                display: block;
            }

            .sig-qr-box img {
                width: 60px;
                height: 60px;
            }

            .sig-pending-box {
                width: 60px;
                height: 60px;
                margin: 4px auto;
                border: 1px dashed #ccc;
                display: table;
            }

            .sig-pending-box span {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
                font-size: 8px;
                color: #aaa;
            }

            .sig-name {
                font-size: 10px;
                font-weight: bold;
                color: #1a1a1a;
                display: block;
                margin-bottom: 2px;
            }

            .sig-nip {
                font-size: 8px;
                color: #555;
            }

            .sig-date {
                font-size: 8px;
                color: #6b7280;
                margin-top: 2px;
            }

            .verification-footer {
                margin-top: 18px;
                padding: 8px 10px;
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                font-size: 8px;
                color: #555;
                page-break-inside: avoid;
            }

            .verification-footer table {
                width: 100%;
                border-collapse: collapse;
            }

            .verification-footer td {
                vertical-align: middle;
                padding: 0;
            }
        @endif
    </style>
</head>

<body>
    @php
        // 1. Resolve Document Template
        $template = null;
        if ($document->relationLoaded('documentTemplate')) {
            $template = $document->documentTemplate;
        } elseif (is_object($document->template) && $document->template instanceof \App\Models\DocumentTemplate) {
            $template = $document->template;
        } else {
            try {
                $template = \App\Models\DocumentTemplate::where('code', (string)$document->template)->first();
            } catch (\Throwable $e) {
                $template = null;
            }
        }

        $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
        $metadata = $document->metadata ?? [];
        $approvals = $document->approvals ?? collect();
        $pemohon = $document->user;
        $docDate = $document->submitted_at ?? ($document->created_at ?? now());
        $verificationUrl = $document->approval_hash ? route('verify.show', $document->approval_hash) : null;
    @endphp

    @if($isLandscape)
        {{-- ========================================================================= --}}
        {{-- PRESET B: DAFTAR PENGADAAN / PEMBELIAN BARANG (LANDSCAPE)                 --}}
        {{-- ========================================================================= --}}
        @php
            $kategori = $metadata['kategori_pembelian'] ?? null;
            if (!$kategori) {
                if (stripos($document->title, 'P3K') !== false) {
                    $kategori = 'Kebutuhan P3K';
                } elseif (stripos($document->title, 'Pantry') !== false) {
                    $kategori = 'Kebutuhan Pantry';
                } elseif (stripos($document->title, 'ATK') !== false || stripos($document->title, 'Alat Tulis') !== false) {
                    $kategori = 'Alat Tulis Kantor';
                } else {
                    $kategori = $document->title;
                }
            }
            $subTitle = ucwords(strtolower(trim($kategori)));

            $logoPath = public_path('images/logo-infodata.jpg');
            $logoDataUri = file_exists($logoPath)
                ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
                : null;

            $rawItems = $metadata['items'] ?? [];
            if (empty($rawItems) && $document->purchaseOrder && $document->purchaseOrder->items) {
                $rawItems = $document->purchaseOrder->items->map(function($poItem) {
                    return [
                        'Nama Barang' => $poItem->item_name,
                        'Keterangan' => $poItem->description,
                        'Max Perbulan' => '-',
                        'Saat ini' => '0',
                        'Permintaan' => $poItem->quantity,
                        'Harga Perk./Item' => $poItem->unit_price,
                        'Jml harga perkiraan' => $poItem->total_price,
                    ];
                })->toArray();
            }

            $hasExpDate = false;
            foreach ($rawItems as $it) {
                if (!empty($it['Exp Date']) || !empty($it['exp_date']) || !empty($it['Kadaluarsa'])) {
                    $hasExpDate = true;
                    break;
                }
            }

            // 4 Approver boxes
            $disetujuiApproval = $approvals->first(function($app) {
                $r = strtolower($app->approver_role ?? '');
                return !str_contains($r, 'finance') && !str_contains($r, 'keuangan') && !str_contains($r, 'ga');
            }) ?? $approvals->first();

            $notifyUsers = collect();
            try { $notifyUsers = $document->getNotifyUsers(); } catch (\Throwable $e) {}
            $mengetahuiUser = $notifyUsers->first();
            if (!$mengetahuiUser && $approvals->count() >= 2) {
                $mengetahuiApproval = $approvals->first(function($app) {
                    return str_contains(strtolower($app->approver_role ?? ''), 'ga') || str_contains(strtolower($app->approver_role ?? ''), 'hrga');
                });
                $mengetahuiUser = $mengetahuiApproval?->approver;
            }

            $financeApproval = $approvals->first(function($app) {
                $r = strtolower($app->approver_role ?? '');
                return str_contains($r, 'finance') || str_contains($r, 'keuangan');
            });
            $financeUser = $financeApproval?->approver;
        @endphp

        {{-- Kop Pembelian --}}
        <table class="header-table">
            <tr>
                <td style="width: 37%;">
                    @if($logoDataUri)
                        <img src="{{ $logoDataUri }}" style="height: 48px; width: auto; margin-bottom: 5px; display: block;" />
                    @endif
                    <table class="meta-table">
                        <tr>
                            <td class="meta-label">No Permintaan</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ $document->document_number }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Tanggal</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ $document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Dept/Divisi</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ $document->department ?? ($document->user->department ?? 'GA/HRGA') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Keterangan/Projek</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ $metadata['keterangan'] ?? ($document->description ?? $document->title) }}</td>
                        </tr>
                    </table>
                </td>

                <td style="width: 26%; text-align: center; padding-top: 10px;">
                    <div class="doc-title-main">PERMINTAAN PEMBELIAN BARANG</div>
                    <div class="doc-title-sub">"{{ $subTitle }}"</div>
                </td>

                <td style="width: 37%; padding-left: 20px;">
                    <table class="meta-table" style="margin-top: 25px;">
                        <tr>
                            <td class="meta-label" style="width: 140px;">Lokasi Permintaan</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ $metadata['lokasi_permintaan'] ?? 'GMH' }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Perkiraan Tgl Penerimaan</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">
                                @php $tglTerima = $metadata['perkiraan_tgl_penerimaan'] ?? null; @endphp
                                {{ $tglTerima ? (is_string($tglTerima) && strtotime($tglTerima) ? \Carbon\Carbon::parse($tglTerima)->format('d F Y') : $tglTerima) : ($document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y')) }}
                            </td>
                        </tr>
                        <tr>
                            <td class="meta-label" style="vertical-align: top;">Jenis Pembelian</td>
                            <td class="meta-colon" style="vertical-align: top;">:</td>
                            <td style="vertical-align: top; padding: 0;">
                                <table style="width: 100%; border-collapse: collapse;">
                                    <tr>
                                        <td style="padding: 1px 0; width: 85px;">Investasi</td>
                                        <td style="padding: 1px 0; width: 8px;">:</td>
                                        <td style="padding: 1px 0;">Non Proyek/Proyek *)</td>
                                    </tr>
                                    <tr>
                                        <td style="padding: 1px 0;">Non Investasi</td>
                                        <td style="padding: 1px 0;">:</td>
                                        <td style="padding: 1px 0;">Non Proyek/Proyek *)</td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        {{-- Data Table Belanja --}}
        <table class="data-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: {{ $hasExpDate ? '20%' : '26%' }};">Nama Barang</th>
                    <th rowspan="2" style="width: {{ $hasExpDate ? '17%' : '21%' }};">Keterangan</th>
                    @if($hasExpDate)
                        <th rowspan="2" style="width: 9%;">Exp Date</th>
                    @endif
                    <th colspan="3" style="width: 25%;">STOCK</th>
                    <th rowspan="2" style="width: 14%;">Harga Perk./Item</th>
                    <th rowspan="2" style="width: 14%;">Jml harga perkiraan</th>
                </tr>
                <tr>
                    <th style="width: 9%;">Max Perbulan</th>
                    <th style="width: 8%;">Saat ini</th>
                    <th style="width: 8%;">Permintaan</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $grandTotal = 0;
                    $currentCat = null;
                @endphp
                @foreach($rawItems as $item)
                    @php
                        $nama = $item['Nama Barang'] ?? ($item['nama_barang'] ?? ($item['item_name'] ?? '-'));
                        $ket = $item['Keterangan'] ?? ($item['keterangan'] ?? ($item['description'] ?? '-'));
                        $exp = $item['Exp Date'] ?? ($item['exp_date'] ?? ($item['Kadaluarsa'] ?? '-'));
                        $max = $item['Max Perbulan'] ?? ($item['max_perbulan'] ?? '-');
                        $saatIni = $item['Saat ini'] ?? ($item['saat_ini'] ?? ($item['Stock Saat Ini'] ?? '0'));
                        $permintaan = $item['Permintaan'] ?? ($item['permintaan'] ?? ($item['quantity'] ?? '0'));
                        
                        $hargaRaw = $item['Harga Perk./Item'] ?? ($item['harga'] ?? ($item['unit_price'] ?? 0));
                        $harga = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$hargaRaw));
                        
                        $totalRaw = $item['Jml harga perkiraan'] ?? ($item['total'] ?? ($item['total_price'] ?? ($harga * (float)$permintaan)));
                        $total = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$totalRaw));
                        $grandTotal += $total;

                        $namaLower = strtolower(trim($nama));
                        if ($currentCat === null) {
                            if (str_contains($namaLower, 'tolak angin') || str_contains($namaLower, 'sangobion') || str_contains($namaLower, 'obat')) {
                                $currentCat = 'Obat-obatan';
                                echo '<tr class="category-row"><td colspan="' . ($hasExpDate ? 8 : 7) . '">' . $currentCat . '</td></tr>';
                            } elseif (str_contains($namaLower, 'double tape') || str_contains($namaLower, 'lakban')) {
                                $currentCat = 'Lemari ATK No.1';
                                echo '<tr class="category-row"><td colspan="' . ($hasExpDate ? 8 : 7) . '">' . $currentCat . '</td></tr>';
                            }
                        } elseif ($currentCat === 'Lemari ATK No.1' && (str_contains($namaLower, 'paper bag finnace') || str_contains($namaLower, 'kertas a4'))) {
                            $currentCat = 'Lemari ATK No.2';
                            echo '<tr class="category-row"><td colspan="' . ($hasExpDate ? 8 : 7) . '">' . $currentCat . '</td></tr>';
                        }
                    @endphp
                    <tr>
                        <td class="text-left">{{ $nama }}</td>
                        <td class="text-left">{{ $ket }}</td>
                        @if($hasExpDate)
                            <td class="text-center">{{ $exp }}</td>
                        @endif
                        <td class="text-center">{{ $max }}</td>
                        <td class="text-center">{{ $saatIni }}</td>
                        <td class="text-center">{{ $permintaan }}</td>
                        <td class="text-right">
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr>
                                    <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                    <td style="border: none; padding: 0; text-align: right;">{{ number_format($harga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td class="text-right">
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr>
                                    <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                    <td style="border: none; padding: 0; text-align: right;">{{ number_format($total, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="{{ $hasExpDate ? 6 : 5 }}" style="border: none;"></td>
                    <td class="bg-yellow text-center" style="font-weight: bold; border: 1px solid #000;">Rp</td>
                    <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                        {{ number_format($grandTotal, 0, ',', '.') }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- 4 Signature Boxes --}}
        <table class="sig-container">
            <tr>
                <td class="sig-box-cell">
                    <table class="sig-card">
                        <tr><td class="sig-card-header">Pemohon</td></tr>
                        <tr>
                            <td class="sig-card-body">
                                @if($verificationUrl)
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" />
                                @endif
                            </td>
                        </tr>
                        <tr><td class="sig-card-footer">{{ $pemohon->name ?? '-' }}</td></tr>
                    </table>
                </td>
                <td class="sig-box-cell">
                    <table class="sig-card">
                        <tr><td class="sig-card-header">Disetujui</td></tr>
                        <tr>
                            <td class="sig-card-body">
                                @if($disetujuiApproval && $disetujuiApproval->status === 'approved' && $verificationUrl)
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" />
                                @else
                                    <span style="color: #aaa; font-size: 8px;">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td class="sig-card-footer">{{ $disetujuiApproval?->approver?->name ?? '-' }}</td></tr>
                    </table>
                </td>
                <td class="sig-box-cell">
                    <table class="sig-card">
                        <tr><td class="sig-card-header">Mengetahui</td></tr>
                        <tr>
                            <td class="sig-card-body">
                                @if($mengetahuiUser && $document->status === 'completed' && $verificationUrl)
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" />
                                @endif
                            </td>
                        </tr>
                        <tr><td class="sig-card-footer">{{ $mengetahuiUser->name ?? '-' }}</td></tr>
                    </table>
                </td>
                <td class="sig-box-cell">
                    <table class="sig-card">
                        <tr><td class="sig-card-header">Bagian Keuangan</td></tr>
                        <tr>
                            <td class="sig-card-body">
                                @if($financeApproval && $financeApproval->status === 'approved' && $verificationUrl)
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" />
                                @else
                                    <span style="color: #aaa; font-size: 8px;">Pending</span>
                                @endif
                            </td>
                        </tr>
                        <tr><td class="sig-card-footer">{{ $financeUser->name ?? '-' }}</td></tr>
                    </table>
                </td>
            </tr>
        </table>

        <div class="verification-footer">
            <strong>Catatan Verifikasi:</strong> Dokumen ini diterbitkan secara elektronik oleh ConfirmMe - PT Info Solusindo Data Utama. Scan QR Code untuk memverifikasi keabsahan dokumen. Hash: {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...
        </div>

    @else
        {{-- ========================================================================= --}}
        {{-- PRESET A: FORMULIR KERJA RESMI PERUSAHAAN (PORTRAIT - GAMBAR 1)           --}}
        {{-- ========================================================================= --}}
        @php
            $categoryName = $template ? ($template->fields['document_category'] ?? $template->name) : 'FORMULIR DOKUMEN';
            if ($categoryName === 'peminjaman_aset' || str_contains(strtolower($document->title), 'mobil') || str_contains(strtolower($document->title), 'kendaraan')) {
                $categoryName = 'PENGGUNAAN KENDARAAN OPERASIONAL';
            }
            $categoryName = strtoupper(str_replace('_', ' ', $categoryName));
        @endphp

        <!-- Fixed Header Box (Formulir Kerja - Repeated on every page) -->
        <header>
            @include('pdf.partials.company-header', [
                'accentColor' => '#7F1D1D',
                'docNumber' => $document->document_number,
                'docDate' => $document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y'),
            ])
        </header>

        <div class="container">
            <!-- Sub-Header Banner (Category matching Image 1) -->
            <div class="sub-header-banner">{{ $categoryName }}</div>

            <!-- Document Title -->
            <div class="doc-title-banner">{{ $document->title }}</div>

            <!-- Intro Paragraph (Description) -->
            @if($document->description)
                <div style="margin-bottom: 12px; font-size: 11px; line-height: 1.6; text-align: justify; color: #222;">
                    {!! nl2br(e($document->description)) !!}
                </div>
            @endif

            {{-- Dynamic Fields: Key-Value Form Fields (Matching Image 1) --}}
            @if(count($dynamicFields) > 0 && !empty($metadata))
                @php
                    $tableFields = [];
                    $skipNext = false;
                    $nonTableFields = [];

                    foreach ($dynamicFields as $f) {
                        if (($f['type'] ?? '') === 'table') {
                            $tableFields[] = $f;
                        } else {
                            $nonTableFields[] = $f;
                        }
                    }
                @endphp

                @if(count($nonTableFields) > 0)
                    <table class="info-table">
                        @for($i = 0; $i < count($nonTableFields); $i++)
                            @php
                                if ($skipNext) {
                                    $skipNext = false;
                                    continue;
                                }

                                $field = $nonTableFields[$i];
                                $fName = $field['name'];
                                $fVal = $metadata[$fName] ?? null;
                                $fType = $field['type'] ?? 'text';
                                $fLabel = $field['label'];

                                // Intelligent pairing: Date + Time
                                $nextField = $nonTableFields[$i + 1] ?? null;
                                $pairedTime = null;
                                $pairedLabel = '';
                                if ($fType === 'date' && $nextField && ($nextField['type'] ?? '') === 'time') {
                                    $nextVal = $metadata[$nextField['name']] ?? null;
                                    if ($nextVal) {
                                        $pairedTime = $nextVal;
                                        $pairedLabel = $nextField['label'];
                                        $skipNext = true;
                                    }
                                }
                            @endphp

                            <tr style="page-break-inside: avoid;">
                                <th>{{ $fLabel }}</th>
                                <td>
                                    @if($fType === 'textarea')
                                        : {!! nl2br(e($fVal ?: '-')) !!}
                                    @elseif($fType === 'file' && $fVal)
                                        @php
                                            $ext = strtolower(pathinfo($fVal, PATHINFO_EXTENSION));
                                            $relPath = ltrim(str_replace('public/', '', $fVal), '/');
                                            $imgPath = storage_path('app/public/' . $relPath);
                                            if (!file_exists($imgPath)) {
                                                $imgPath = public_path('storage/' . $relPath);
                                            }
                                            $imgDataUri = null;
                                            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && file_exists($imgPath)) {
                                                $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                                                $imgDataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($imgPath));
                                            }
                                        @endphp
                                        @if($imgDataUri)
                                            <div class="attachment-box">
                                                <img src="{{ $imgDataUri }}" class="attachment-img" alt="{{ $fLabel }}" />
                                            </div>
                                        @else
                                            : <em>{{ basename($fVal) }}</em>
                                        @endif
                                    @else
                                        @php
                                            $displayVal = $fVal ?: '-';
                                            if ($fVal && $fType === 'date' && strtotime($fVal)) {
                                                $displayVal = \Carbon\Carbon::parse($fVal)->format('d/m/Y');
                                            } elseif ($fVal && $fType === 'number' && is_numeric($fVal)) {
                                                $displayVal = number_format((float)$fVal, 0, ',', '.');
                                            }
                                        @endphp

                                        : {{ $displayVal }}

                                        @if($pairedTime)
                                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                            <strong>{{ $pairedLabel }}:</strong> {{ $pairedTime }} WIB
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @endfor
                    </table>
                @endif

                {{-- Dynamic Tables (e.g. Data Penumpang dalam Mobil) --}}
                @foreach($tableFields as $tField)
                    @php
                        $tName = $tField['name'];
                        $tData = $metadata[$tName] ?? [];
                        $tCols = !empty($tField['columns']) 
                            ? array_filter(array_map('trim', explode(',', $tField['columns'])))
                            : (!empty($tData[0]) ? array_keys($tData[0]) : ['Item']);
                    @endphp

                    @if(is_array($tData) && count($tData) > 0)
                        <div class="data-table-container">
                            <div class="section-title">{{ strtoupper($tField['label']) }}</div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th style="width: 35px; text-align: center;">No</th>
                                        @foreach($tCols as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $rowIdx = 1; @endphp
                                    @foreach($tData as $row)
                                        <tr>
                                            <td style="text-align: center; width: 35px; color: #6b7280;">{{ $rowIdx++ }}</td>
                                            @foreach($tCols as $col)
                                                @php
                                                    $cellVal = '-';
                                                    foreach ($row as $k => $v) {
                                                        if (strtolower(trim((string)$k)) === strtolower(trim((string)$col))) {
                                                            $cellVal = $v;
                                                            break;
                                                        }
                                                    }
                                                    if ($cellVal === '-' && isset($row[$col])) {
                                                        $cellVal = $row[$col];
                                                    }
                                                @endphp
                                                <td>{{ $cellVal }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            @endif

            {{-- Terms & Conditions / Disclaimer (Matching Image 1) --}}
            @php
                $terms = $template ? ($template->fields['terms_and_conditions'] ?? null) : null;
            @endphp
            @if($terms)
                <div style="margin-top: 15px; margin-bottom: 15px; font-size: 10.5px; color: #333; line-height: 1.6; text-align: justify; page-break-inside: avoid;">
                    {!! nl2br(e($terms)) !!}
                </div>
            @endif

            {{-- Signature Block (Matching Image 1 + Digital QR Signatures) --}}
            <div class="signature-section">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
                    <tr>
                        <td style="text-align: right; font-size: 11px; padding-bottom: 4px;">
                            Jakarta, {{ $docDate->format('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-size: 11px; padding-bottom: 10px;">
                            Disetujui,
                        </td>
                    </tr>
                </table>

                <table class="signature-table">
                    <tr>
                        <td style="width: {{ round(100 / (1 + max(count($approvals), 1)), 2) }}%;">
                            <div class="sig-role-label">{{ __('Pemohon') }}</div>
                        </td>
                        @foreach($approvals as $approval)
                            <td style="width: {{ round(100 / (1 + max(count($approvals), 1)), 2) }}%;">
                                <div class="sig-role-label">{{ $approval->approver?->position ?? ($approval->role_label ?? 'Approver') }}</div>
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td>
                            @if($verificationUrl)
                                <div class="sig-qr-box">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                                </div>
                                <div style="font-size: 7.5px; color: #059669; font-weight: bold; margin-top: 1px;">Signed</div>
                            @else
                                <div class="sig-pending-box"><span>-</span></div>
                            @endif
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                @if($approval->status === 'approved' && $verificationUrl)
                                    <div class="sig-qr-box">
                                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                                    </div>
                                    <div style="font-size: 7.5px; color: #059669; font-weight: bold; margin-top: 1px;">Signed</div>
                                @elseif($approval->status === 'approved')
                                    <div style="font-size: 8.5px; font-weight: bold; color: #059669; height: 60px; line-height: 60px;">APPROVED</div>
                                @else
                                    <div class="sig-pending-box"><span>Pending</span></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td>
                            <div class="sig-name">( {{ $pemohon->name ?? 'Pemohon' }} )</div>
                            @if($pemohon && $pemohon->employee_id)
                                <div class="sig-nip">NIP. {{ $pemohon->employee_id }}</div>
                            @endif
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                @if($approval->approver)
                                    <div class="sig-name">( {{ $approval->approver->name }} )</div>
                                    @if($approval->approver->employee_id)
                                        <div class="sig-nip">NIP. {{ $approval->approver->employee_id }}</div>
                                    @endif
                                @else
                                    <div class="sig-name">( - )</div>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    <tr>
                        <td>
                            <span class="sig-date">
                                {{ $document->submitted_at ? $document->submitted_at->format('d/m/Y H:i') . ' WIB' : ($document->created_at ? $document->created_at->format('d/m/Y H:i') . ' WIB' : '') }}
                            </span>
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                <span class="sig-date">
                                    {{ $approval->action_at ? $approval->action_at->format('d/m/Y H:i') . ' WIB' : '-' }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>

            <!-- Verification Footer -->
            <div class="verification-footer">
                <table>
                    <tr>
                        <td style="width: 78%; padding-right: 15px;">
                            <strong>{{ __("Catatan Verifikasi") }}:</strong><br>
                            {{ __("Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe - PT Info Solusindo Data Utama.") }}
                            {{ __("Scan QR Code di atas untuk memverifikasi keaslian dokumen.") }}<br>
                            <strong>ID Dokumen:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...
                        </td>
                        <td style="width: 22%; text-align: center;">
                            @if(isset($qrCode))
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 50px; height: 50px;">
                                <div style="font-size: 7px; color: #888; margin-top: 2px;">{{ __("Scan untuk verifikasi") }}</div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
</body>

</html>
