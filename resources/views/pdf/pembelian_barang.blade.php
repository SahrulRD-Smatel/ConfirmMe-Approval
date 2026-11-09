<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $document->document_number }} - Permintaan Pembelian Barang</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 10mm 12mm 10mm 12mm;
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

        table {
            border-collapse: collapse;
        }

        .w-full {
            width: 100%;
        }

        /* Top Header Layout */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .doc-title-main {
            font-size: 13px;
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .doc-title-sub {
            font-size: 11px;
            font-weight: bold;
            text-align: center;
            font-style: italic;
        }

        /* Meta Information Boxes */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
        }

        .meta-table td {
            padding: 1.5px 0;
            vertical-align: middle;
        }

        .meta-label {
            width: 95px;
            color: #000;
        }

        .meta-colon {
            width: 10px;
            text-align: center;
        }

        .meta-value-line {
            border-bottom: 1px solid #777;
            padding-bottom: 1px;
            font-weight: 500;
        }

        /* Main Data Table */
        .data-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 6px;
            font-size: 8.5px;
        }

        .data-table th {
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            background-color: #fff;
            vertical-align: middle;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            vertical-align: middle;
        }

        .category-row td {
            background-color: #F7CE3E !important;
            font-weight: bold;
            padding: 3px 6px;
            border: 1px solid #000;
        }

        .total-row td {
            font-weight: bold;
        }

        .bg-yellow {
            background-color: #F7CE3E !important;
        }

        /* Signatures Block */
        .sig-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .sig-box-cell {
            width: 25%;
            padding: 0 4px;
            vertical-align: top;
        }

        .sig-card {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
        }

        .sig-card-header {
            border-bottom: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 9.5px;
            padding: 3px;
            background-color: #fff;
            height: 18px;
        }

        .sig-card-body {
            text-align: center;
            vertical-align: middle;
            height: 52px;
            padding: 2px;
            position: relative;
        }

        .sig-card-footer {
            border-top: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            padding: 3px 2px;
            background-color: #fff;
            height: 20px;
        }

        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>
<body>
    @php
        $metadata = $document->metadata ?? [];

        // 1. Kategori / Sub-judul
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

        // 2. Logo Base64
        $logoPath = public_path('images/logo-infodata.jpg');
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        // 3. Raw Items
        $rawItems = $metadata['items'] ?? [];
        if (empty($rawItems) && $document->purchaseOrder && $document->purchaseOrder->items) {
            $rawItems = $document->purchaseOrder->items->map(function($poItem) {
                return [
                    'Nama Barang' => $poItem->item_name,
                    'Keterangan' => $poItem->description,
                    'Max Perbulan' => '-',
                    'Stock Saat Ini' => '0',
                    'Permintaan' => $poItem->quantity,
                    'Harga Perk./Item' => $poItem->unit_price,
                    'Jml harga perkiraan' => $poItem->total_price,
                ];
            })->toArray();
        }

        // Cek apakah ada kolom Exp Date
        $hasExpDate = false;
        foreach ($rawItems as $it) {
            if (!empty($it['Exp Date']) || !empty($it['exp_date']) || !empty($it['Kadaluarsa'])) {
                $hasExpDate = true;
                break;
            }
        }

        // 4. Approver & Signatures resolution
        $approvals = $document->approvals ?? collect();
        $pemohon = $document->user;

        // Disetujui (Approver Manajerial / Atasan)
        $disetujuiApproval = $approvals->first(function($app) {
            $r = strtolower($app->approver_role ?? '');
            return !str_contains($r, 'finance') && !str_contains($r, 'keuangan') && !str_contains($r, 'ga');
        }) ?? $approvals->first();

        // Mengetahui (Pihak Mengetahui / GA / HR)
        $notifyUsers = collect();
        try {
            $notifyUsers = $document->getNotifyUsers();
        } catch (\Throwable $e) {
            $notifyUsers = collect();
        }
        $mengetahuiUser = $notifyUsers->first();
        if (!$mengetahuiUser && $approvals->count() >= 2) {
            $mengetahuiApproval = $approvals->first(function($app) {
                return str_contains(strtolower($app->approver_role ?? ''), 'ga') || str_contains(strtolower($app->approver_role ?? ''), 'hrga');
            });
            $mengetahuiUser = $mengetahuiApproval?->approver;
        }

        // Bagian Keuangan (Finance)
        $financeApproval = $approvals->first(function($app) {
            $r = strtolower($app->approver_role ?? '');
            return str_contains($r, 'finance') || str_contains($r, 'keuangan');
        });
        $financeUser = $financeApproval?->approver;
    @endphp

    {{-- KOP HEADER --}}
    <table class="header-table">
        <tr>
            {{-- Kiri: Logo & Metadata Dokumen --}}
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

            {{-- Tengah: Judul Dokumen --}}
            <td style="width: 26%; text-align: center; padding-top: 10px;">
                <div class="doc-title-main">PERMINTAAN PEMBELIAN BARANG</div>
                <div class="doc-title-sub">"{{ $subTitle }}"</div>
            </td>

            {{-- Kanan: Lokasi & Jenis Pembelian --}}
            <td style="width: 37%; padding-left: 20px;">
                <table class="meta-table" style="margin-top: 25px;">
                    <tr>
                        <td style="width: 125px;">Lokasi Permintaan</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $metadata['lokasi_permintaan'] ?? 'GMH' }}</td>
                    </tr>
                    <tr>
                        <td>Perkiraan Tgl Penerimaan</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">
                            {{ !empty($metadata['expected_delivery']) ? \Carbon\Carbon::parse($metadata['expected_delivery'])->format('d F Y') : ($metadata['perkiraan_tgl_penerimaan'] ?? ($document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y'))) }}
                        </td>
                    </tr>
                    <tr>
                        <td>Jenis Pembelian</td>
                        <td class="meta-colon">:</td>
                        <td>
                            <table style="width: 100%; border-collapse: collapse;">
                                <tr>
                                    <td style="width: 75px; padding: 0;">Investasi</td>
                                    <td style="width: 10px; padding: 0;">:</td>
                                    <td style="padding: 0;">{{ $metadata['jenis_investasi'] ?? 'Non Proyek/Proyek *)' }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 0;">Non Investasi</td>
                                    <td style="padding: 0;">:</td>
                                    <td style="padding: 0;">{{ $metadata['jenis_non_investasi'] ?? 'Non Proyek/Proyek *)' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- TABEL RINCIAN BARANG --}}
    <table class="data-table">
        <thead>
            <tr>
                <th rowspan="2" style="width: {{ $hasExpDate ? '20%' : '22%' }};">Nama Barang</th>
                <th rowspan="2" style="width: {{ $hasExpDate ? '16%' : '19%' }};">Keterangan</th>
                @if($hasExpDate)
                    <th rowspan="2" style="width: 8%;">Exp Date</th>
                @endif
                <th colspan="3" style="width: 25%;">STOCK</th>
                <th rowspan="2" style="width: 13%;">Harga Perk./Item</th>
                <th rowspan="2" style="width: 15%;">Jml harga perkiraan</th>
            </tr>
            <tr>
                <th style="width: 8.5%;">Max Perbulan</th>
                <th style="width: 8%;">Saat Ini</th>
                <th style="width: 8.5%;">Permintaan</th>
            </tr>
        </thead>
        <tbody>
            @php
                $grandTotal = 0;
                $colSpanTotal = $hasExpDate ? 8 : 7;
            @endphp

            @forelse($rawItems as $item)
                @php
                    $namaBarang = $item['Nama Barang'] ?? $item['nama_barang'] ?? $item['item_name'] ?? '';
                    $keterangan = $item['Keterangan'] ?? $item['keterangan'] ?? $item['description'] ?? '';
                    $expDate = $item['Exp Date'] ?? $item['exp_date'] ?? $item['Kadaluarsa'] ?? '-';
                    $maxPerbulan = $item['Max Perbulan'] ?? $item['max_perbulan'] ?? '-';
                    $stockSaatIni = $item['Stock Saat Ini'] ?? $item['Saat Ini'] ?? $item['stock_saat_ini'] ?? $item['current_stock'] ?? '0';
                    $permintaan = $item['Permintaan (Qty)'] ?? $item['Permintaan'] ?? $item['qty'] ?? $item['quantity'] ?? '0';
                    $hargaItem = $item['Harga Perk./Item'] ?? $item['Harga'] ?? $item['harga'] ?? $item['unit_price'] ?? 0;

                    $cleanHarga = (float)preg_replace('/[^0-9.]/', '', (string)$hargaItem);
                    $cleanPermintaan = (float)preg_replace('/[^0-9.]/', '', (string)$permintaan);

                    $rawSubtotal = $item['Jml harga perkiraan'] ?? $item['subtotal'] ?? null;
                    if ($rawSubtotal === null || $rawSubtotal === '') {
                        $cleanSubtotal = $cleanHarga * ($cleanPermintaan > 0 ? $cleanPermintaan : 1);
                    } else {
                        $cleanSubtotal = (float)preg_replace('/[^0-9.]/', '', (string)$rawSubtotal);
                    }

                    // Cek baris kategori (misal: "Lemari ATK No.1", "Obat-obatan", "Pantry")
                    $isCategory = !empty($item['is_category']) || (!empty($namaBarang) && empty($item['Permintaan']) && empty($item['Permintaan (Qty)']) && empty($item['Harga Perk./Item']) && empty($item['Harga']));
                @endphp

                @if($isCategory)
                    <tr class="category-row">
                        <td colspan="{{ $colSpanTotal }}">{{ $namaBarang }}</td>
                    </tr>
                @else
                    @php $grandTotal += $cleanSubtotal; @endphp
                    <tr>
                        <td class="text-left">{{ $namaBarang }}</td>
                        <td class="text-left">{{ $keterangan }}</td>
                        @if($hasExpDate)
                            <td class="text-center">{{ $expDate }}</td>
                        @endif
                        <td class="text-center">{{ $maxPerbulan }}</td>
                        <td class="text-center">{{ $stockSaatIni }}</td>
                        <td class="text-center">{{ $permintaan }}</td>
                        <td style="padding: 2px 4px;">
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr style="border: none;">
                                    <td style="border: none; text-align: left; padding: 0; width: 18px;">Rp</td>
                                    <td style="border: none; text-align: right; padding: 0;">{{ number_format($cleanHarga, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                        <td style="padding: 2px 4px;">
                            <table style="width: 100%; border-collapse: collapse; border: none;">
                                <tr style="border: none;">
                                    <td style="border: none; text-align: left; padding: 0; width: 18px;">Rp</td>
                                    <td style="border: none; text-align: right; padding: 0;">{{ number_format($cleanSubtotal, 0, ',', '.') }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                @endif
            @empty
                <tr>
                    <td colspan="{{ $colSpanTotal }}" class="text-center" style="padding: 15px; color: #777;">
                        Belum ada daftar barang yang dimasukkan.
                    </td>
                </tr>
            @endforelse

            {{-- Fallback jika grand total 0 tapi dokumen punya budget --}}
            @php
                if ($grandTotal == 0) {
                    $grandTotal = (float)($document->approved_budget ?: $document->estimated_budget);
                }
            @endphp

            {{-- BARIS TOTAL --}}
            <tr class="total-row">
                <td colspan="{{ $hasExpDate ? 6 : 5 }}" style="border-top: 1px solid #000; border-bottom: 1px solid #000; border-left: 1px solid #000; border-right: none; text-align: right; padding-right: 15px;">
                    @if(stripos($subTitle, 'pantry') !== false)
                        Total Estimasi Belanja Pantry
                    @elseif(stripos($subTitle, 'p3k') !== false)
                        Total Estimasi Belanja P3K
                    @endif
                </td>
                <td class="bg-yellow" style="border: 1px solid #000; padding: 3px 4px; text-align: left; width: 22px;">
                    Rp
                </td>
                <td class="bg-yellow" style="border: 1px solid #000; padding: 3px 6px; text-align: right;">
                    {{ number_format($grandTotal, 0, ',', '.') }}
                </td>
            </tr>
        </tbody>
    </table>

    {{-- 4 KOTAK TANDA TANGAN (HORIZONTAL) --}}
    <table class="sig-container">
        <tr>
            {{-- Kotak 1: Pemohon --}}
            <td class="sig-box-cell">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">Pemohon</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            <div style="font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px;">
                                ✓ Disubmit Digital
                            </div>
                            <div style="font-size: 7.5px; color: #666;">
                                {{ $document->created_at ? $document->created_at->format('d/m/Y') : now()->format('d/m/Y') }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            {{ $pemohon?->name ?? 'Pemohon' }}
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 2: Disetujui --}}
            <td class="sig-box-cell">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">Disetujui</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($disetujuiApproval && $disetujuiApproval->status === 'approved')
                                <div style="font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px;">
                                    ✓ Approved
                                </div>
                                <div style="font-size: 7.5px; color: #666;">
                                    {{ $disetujuiApproval->action_at?->format('d/m/Y') }}
                                </div>
                            @else
                                <div style="font-size: 8px; color: #999; font-style: italic;">
                                    Menunggu Approval
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            {{ $disetujuiApproval?->approver?->name ?? 'Mohamad Iqbal Nizamy' }}
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 3: Mengetahui --}}
            <td class="sig-box-cell">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">Mengetahui</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($document->status === 'approved')
                                <div style="font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px;">
                                    ✓ Mengetahui
                                </div>
                                <div style="font-size: 7.5px; color: #666;">
                                    {{ $document->completed_at ? $document->completed_at->format('d/m/Y') : ($document->updated_at ? $document->updated_at->format('d/m/Y') : now()->format('d/m/Y')) }}
                                </div>
                            @else
                                <div style="font-size: 8px; color: #999; font-style: italic;">
                                    Tembusan / CC
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            {{ $mengetahuiUser?->name ?? 'Fathia Sena' }}
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 4: Bagian Keuangan --}}
            <td class="sig-box-cell">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">Bagian Keuangan</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($financeApproval && $financeApproval->status === 'approved')
                                <div style="font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px;">
                                    ✓ Disetujui Keuangan
                                </div>
                                <div style="font-size: 7.5px; color: #666;">
                                    {{ $financeApproval->action_at?->format('d/m/Y') }}
                                </div>
                            @elseif($document->status === 'approved')
                                <div style="font-size: 8px; color: #059669; font-weight: bold; margin-bottom: 2px;">
                                    ✓ Validasi Keuangan
                                </div>
                            @else
                                <div style="font-size: 8px; color: #999; font-style: italic;">
                                    Review Keuangan
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            {{ $financeUser?->name ?? 'Fajar Trimuri Jantono' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- CATATAN & QR VERIFIKASI RESMI --}}
    <table style="width: 100%; margin-top: 6px; border-collapse: collapse;">
        <tr>
            <td style="font-size: 8px; color: #444; vertical-align: middle;">
                @if(!empty($metadata['catatan']))
                    <strong>Note:</strong> {{ $metadata['catatan'] }}
                @endif
            </td>
            <td style="text-align: right; vertical-align: middle; width: 180px;">
                <div style="display: inline-block; vertical-align: middle; text-align: right; margin-right: 5px;">
                    <div style="font-size: 7px; font-weight: bold; color: #333;">Dokumen Resmi ConfirmMe</div>
                    <div style="font-size: 6px; color: #777;">Scan QR untuk verifikasi publik</div>
                </div>
                @if(!empty($qrCode))
                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 28px; height: 28px; vertical-align: middle;" />
                @endif
            </td>
        </tr>
    </table>
</body>
</html>

