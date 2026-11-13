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
                margin: 8mm 12mm 8mm 12mm !important;
            }

            body {
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

            .sig-container { width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid; }
            .sig-box-cell { width: 25%; padding: 0 5px; vertical-align: top; }
            .sig-card { width: 100%; border: 1px solid #000; border-collapse: collapse; }
            .sig-card-header { border-bottom: 1px solid #000; text-align: center; font-weight: bold; font-size: 9px; padding: 4px 2px; background-color: #fff; height: 16px; }
            .sig-card-body { text-align: center; vertical-align: middle; height: 74px; min-height: 74px; padding: 3px 2px; background-color: #fff; }
            .sig-card-footer { border-top: 1px solid #000; text-align: center; font-weight: bold; font-size: 8.5px; padding: 4px 2px; background-color: #fff; height: 18px; }

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
                border-collapse: collapse;
                margin-left: auto;
                margin-right: auto;
            }

            .signature-table td {
                text-align: center;
                vertical-align: top;
                padding: 3px 6px;
            }

            .sig-role-label {
                font-size: 10px;
                font-weight: bold;
                color: #1f2937;
                margin-bottom: 4px;
                min-height: 14px;
                line-height: 1.25;
            }

            .sig-qr-box {
                width: 78px;
                height: 78px;
                margin: 2px auto 1px auto;
                display: block;
            }

            .sig-qr-box img {
                width: 78px;
                height: 78px;
                display: block;
            }

            .sig-status-signed {
                font-size: 8px;
                color: #059669;
                font-weight: bold;
                margin-top: 2px;
                letter-spacing: 0.3px;
            }

            .sig-status-approved {
                font-size: 9px;
                font-weight: bold;
                color: #059669;
                height: 78px;
                line-height: 78px;
            }

            .sig-pending-box {
                width: 78px;
                height: 78px;
                margin: 2px auto;
                border: 1px dashed #cbd5e1;
                background-color: #f8fafc;
                display: table;
            }

            .sig-pending-box span {
                display: table-cell;
                vertical-align: middle;
                text-align: center;
                font-size: 8px;
                color: #94a3b8;
            }

            .sig-name {
                font-size: 9.5px;
                font-weight: bold;
                color: #111827;
                display: block;
                margin-top: 4px;
                margin-bottom: 1px;
                line-height: 1.25;
            }

            .sig-nip {
                font-size: 8px;
                color: #4b5563;
                margin-top: 1px;
            }

            .sig-date {
                font-size: 8px;
                color: #64748b;
                display: block;
                margin-top: 2px;
            }

            .verification-footer {
                margin-top: 16px;
                padding: 8px 12px;
                background-color: #f8fafc;
                border: 1px solid #cbd5e1;
                font-size: 8.5px;
                color: #475569;
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
        // 1. Resolve Document Template safely
        $template = $document->documentTemplate ?? null;
        if (!$template && is_object($document->template) && $document->template instanceof \App\Models\DocumentTemplate) {
            $template = $document->template;
        }
        if (!$template && !empty($document->template)) {
            try {
                $template = \App\Models\DocumentTemplate::where('code', (string)$document->template)->first();
            } catch (\Throwable $e) {}
        }

        $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
        $metadata = $document->metadata ?? [];

        // Universal Fallback: If template dynamic_fields is empty but metadata exists, derive fields automatically
        if (empty($dynamicFields) && !empty($metadata)) {
            foreach ($metadata as $mKey => $mVal) {
                if (is_array($mVal) && !empty($mVal) && isset($mVal[0]) && is_array($mVal[0])) {
                    $dynamicFields[] = [
                        'name' => $mKey,
                        'label' => ucwords(str_replace('_', ' ', $mKey)),
                        'type' => 'table',
                        'columns' => implode(', ', array_keys($mVal[0])),
                    ];
                } elseif (!is_array($mVal)) {
                    $dynamicFields[] = [
                        'name' => $mKey,
                        'label' => ucwords(str_replace('_', ' ', $mKey)),
                        'type' => 'text',
                    ];
                }
            }
        }

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
            $kategori = $metadata['kategori_pembelian'] ?? ($metadata['kategori'] ?? null);
            $mainTitle = strtoupper($template->name ?? $document->title);
            $subTitle = $kategori ? format_acronyms(ucwords(strtolower(trim($kategori)))) : null;

            $logoPath = public_path('images/logo-infodata.jpg');
            $logoDataUri = file_exists($logoPath)
                ? 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath))
                : null;

            $parseMoney = function($raw): float {
                if (is_int($raw) || is_float($raw)) return (float)$raw;
                if (empty($raw)) return 0.0;
                
                $clean = trim((string)$raw);
                $clean = preg_replace('/^(?:rp\.?|idr)\s*/i', '', $clean);
                $clean = preg_replace('/[^\d.,]/', '', $clean);
                $clean = trim($clean, "., ");
                if ($clean === '') return 0.0;
                
                if (str_contains($clean, '.') && str_contains($clean, ',')) {
                    $lastDot = strrpos($clean, '.');
                    $lastComma = strrpos($clean, ',');
                    if ($lastComma > $lastDot) {
                        $clean = str_replace('.', '', $clean);
                        $clean = str_replace(',', '.', $clean);
                    } else {
                        $clean = str_replace(',', '', $clean);
                    }
                } elseif (str_contains($clean, '.')) {
                    $parts = explode('.', $clean);
                    $isThousand = true;
                    for ($i = 1; $i < count($parts); $i++) {
                        if (strlen($parts[$i]) !== 3) {
                            $isThousand = false;
                            break;
                        }
                    }
                    if ($isThousand && count($parts) > 1) {
                        $clean = str_replace('.', '', $clean);
                    }
                } elseif (str_contains($clean, ',')) {
                    $parts = explode(',', $clean);
                    $isThousand = true;
                    for ($i = 1; $i < count($parts); $i++) {
                        if (strlen($parts[$i]) !== 3) {
                            $isThousand = false;
                            break;
                        }
                    }
                    if ($isThousand && count($parts) > 1) {
                        $clean = str_replace(',', '', $clean);
                    } else {
                        $clean = str_replace(',', '.', $clean);
                    }
                }
                
                return (float)$clean;
            };

            $parseQty = function($raw): float {
                if (is_int($raw) || is_float($raw)) return (float)$raw;
                if (empty($raw)) return 0.0;
                
                $clean = trim((string)$raw);
                if (preg_match('/(\d+(?:[.,]\d+)?)/', $clean, $matches)) {
                    return (float) str_replace(',', '.', $matches[1]);
                }
                return 0.0;
            };

            // Resolve dynamic table fields
            $tableFields = [];
            foreach ($dynamicFields as $f) {
                if (($f['type'] ?? '') === 'table') {
                    $tableFields[] = $f;
                }
            }

            if (empty($tableFields)) {
                $fallbackItems = $metadata['items'] ?? ($metadata['daftar_barang'] ?? ($metadata['data_barang'] ?? ($metadata['pembelian_barang'] ?? [])));
                if (empty($fallbackItems)) {
                    foreach ($metadata as $metaKey => $metaVal) {
                        if (is_array($metaVal) && !empty($metaVal) && isset($metaVal[0]) && is_array($metaVal[0])) {
                            $fallbackItems = $metaVal;
                            $tableFields[] = [
                                'name' => $metaKey,
                                'label' => ucwords(str_replace('_', ' ', $metaKey)),
                                'columns' => implode(', ', array_keys($metaVal[0])),
                                '_data' => $fallbackItems,
                            ];
                            break;
                        }
                    }
                } elseif (!empty($fallbackItems)) {
                    $tableFields[] = [
                        'name' => 'items',
                        'label' => 'Daftar Barang',
                        'columns' => !empty($fallbackItems[0]) && is_array($fallbackItems[0]) ? implode(', ', array_keys($fallbackItems[0])) : 'Item',
                        '_data' => $fallbackItems,
                    ];
                }
            }

            if (empty($tableFields) && $document->purchaseOrder && $document->purchaseOrder->items) {
                $poRows = $document->purchaseOrder->items->map(function($poItem) {
                    return [
                        'Nama Barang' => $poItem->item_name,
                        'Keterangan' => $poItem->description,
                        'Permintaan' => $poItem->quantity,
                        'Harga Perk./Item' => $poItem->unit_price,
                        'Jml Harga Perkiraan' => $poItem->total_price,
                    ];
                })->toArray();
                $tableFields[] = [
                    'name' => 'po_items',
                    'label' => 'Daftar Barang PO',
                    'columns' => 'Nama Barang, Keterangan, Permintaan, Harga Perk./Item, Jml Harga Perkiraan',
                    '_data' => $poRows,
                ];
            }
        @endphp

        {{-- Kop Landscape Dinamis --}}
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
                            <td class="meta-value-line">{{ $document->created_at ? $document->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Dept/Divisi</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ format_acronyms($document->department ?? ($document->user->department ?? 'GA/HRGA')) }}</td>
                        </tr>
                        <tr>
                            <td class="meta-label">Keterangan/Projek</td>
                            <td class="meta-colon">:</td>
                            <td class="meta-value-line">{{ format_acronyms($metadata['keterangan'] ?? ($document->description ?? $document->title)) }}</td>
                        </tr>
                    </table>
                </td>

                <td style="width: 26%; text-align: center; padding-top: 10px;">
                    <div class="doc-title-main">{{ $mainTitle }}</div>
                    @if($subTitle && strtolower($subTitle) !== strtolower($mainTitle))
                        <div class="doc-title-sub">"{{ $subTitle }}"</div>
                    @endif
                </td>

                <td style="width: 37%; padding-left: 20px;">
                    <table class="meta-table" style="margin-top: 20px;">
                        @if(!empty($metadata['lokasi_permintaan']) || !empty($metadata['lokasi']))
                            <tr>
                                <td class="meta-label" style="width: 140px;">Lokasi Permintaan</td>
                                <td class="meta-colon">:</td>
                                <td class="meta-value-line">{{ format_acronyms($metadata['lokasi_permintaan'] ?? $metadata['lokasi']) }}</td>
                            </tr>
                        @endif

                        @php
                            $tglTerima = $metadata['perkiraan_tgl_penerimaan'] ?? ($metadata['expected_delivery'] ?? ($metadata['delivery_date'] ?? null));
                        @endphp
                        @if(!empty($tglTerima))
                            <tr>
                                <td class="meta-label">Perkiraan Tgl Penerimaan</td>
                                <td class="meta-colon">:</td>
                                <td class="meta-value-line">
                                    {{ (is_string($tglTerima) && strtotime($tglTerima)) ? \Carbon\Carbon::parse($tglTerima)->translatedFormat('d F Y') : $tglTerima }}
                                </td>
                            </tr>
                        @endif

                        @if(isset($metadata['jenis_pembelian']) || isset($metadata['kategori_pembelian']))
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
                        @endif

                        @php
                            $renderedKeys = ['kategori_pembelian', 'kategori', 'keterangan', 'lokasi_permintaan', 'lokasi', 'perkiraan_tgl_penerimaan', 'expected_delivery', 'delivery_date', 'jenis_pembelian'];
                            $extraFields = array_filter($dynamicFields, function($f) use ($renderedKeys) {
                                return ($f['type'] ?? '') !== 'table' && !in_array($f['name'] ?? '', $renderedKeys);
                            });
                        @endphp
                        @foreach($extraFields as $ef)
                            @php
                                $efVal = $metadata[$ef['name']] ?? null;
                                if ($efVal === null || $efVal === '') continue;
                                if (is_array($efVal)) $efVal = implode(', ', array_filter($efVal, 'is_scalar'));
                            @endphp
                            <tr>
                                <td class="meta-label">{{ $ef['label'] }}</td>
                                <td class="meta-colon">:</td>
                                <td class="meta-value-line">{{ $efVal }}</td>
                            </tr>
                        @endforeach
                    </table>
                </td>
            </tr>
        </table>

        {{-- Render Tabel Dinamis (100% Dynamic Universal Table) --}}
        @foreach($tableFields as $tField)
            @php
                $tName = $tField['name'];
                $rows = $tField['_data'] ?? ($metadata[$tName] ?? ($metadata['items'] ?? []));
                if (!is_array($rows) || empty($rows)) continue;

                $rawCols = !empty($tField['columns']) 
                    ? array_map('trim', explode(',', $tField['columns']))
                    : (!empty($rows[0]) && is_array($rows[0]) ? array_keys($rows[0]) : ['Item']);
                $columns = array_values(array_filter($rawCols));

                $priceCol = null;
                $qtyCol = null;
                $totalCol = null;
                $categoryCol = null;

                foreach ($columns as $c) {
                    $cClean = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $c));
                    if ($totalCol === null && in_array($cClean, ['total', 'jmlhargaperkiraan', 'jumlahharga', 'totalharga', 'subtotal', 'totalbiaya'])) {
                        $totalCol = $c;
                    }
                    if ($categoryCol === null && in_array($cClean, ['kategori', 'kategoribarang', 'category', 'kelompok', 'group'])) {
                        $categoryCol = $c;
                    }
                    if ($priceCol === null && (str_contains($cClean, 'harga') || str_contains($cClean, 'price') || str_contains($cClean, 'biaya') || str_contains($cClean, 'tarif'))) {
                        if (!str_contains($cClean, 'total') && !str_contains($cClean, 'jml')) {
                            $priceCol = $c;
                        }
                    }
                    if ($qtyCol === null && (str_contains($cClean, 'qty') || str_contains($cClean, 'jumlah') || str_contains($cClean, 'kuantitas') || str_contains($cClean, 'permintaan') || str_contains($cClean, 'vol') || str_contains($cClean, 'volume'))) {
                        if (!str_contains($cClean, 'harga') && !str_contains($cClean, 'biaya') && !str_contains($cClean, 'total')) {
                            $qtyCol = $c;
                        }
                    }
                }

                $hasPriceCalculation = ($priceCol !== null || $totalCol !== null);
                $showCalculatedTotalCol = ($priceCol !== null && $totalCol === null);
                $totalColCount = 1 + count($columns) + ($showCalculatedTotalCol ? 1 : 0);
            @endphp

            @if(count($tableFields) > 1)
                <div style="font-size: 10px; font-weight: bold; margin-top: 10px; margin-bottom: 4px; text-transform: uppercase;">
                    {{ $tField['label'] }}
                </div>
            @endif

            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 30px; text-align: center;">No</th>
                        @foreach($columns as $col)
                            <th style="text-align: {{ ($col === $priceCol || $col === $totalCol) ? 'right' : 'center' }};">
                                {{ $col }}
                            </th>
                        @endforeach
                        @if($showCalculatedTotalCol)
                            <th style="width: 14%; text-align: right;">Jml Harga Perkiraan</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @php
                        $grandTotal = 0;
                        $lastCategory = null;
                    @endphp
                    @foreach($rows as $rIdx => $row)
                        @php
                            $row = (array)$row;

                            $currentCat = $categoryCol ? ($row[$categoryCol] ?? null) : null;
                            if ($currentCat && $currentCat !== $lastCategory) {
                                $lastCategory = $currentCat;
                                echo '<tr class="category-row"><td colspan="' . $totalColCount . '">' . e($currentCat) . '</td></tr>';
                            }

                            $rowPrice = 0;
                            $rowQty = 1;
                            $rowTotal = 0;

                            if ($priceCol && isset($row[$priceCol])) {
                                $rowPrice = $parseMoney($row[$priceCol]);
                            }
                            if ($qtyCol && isset($row[$qtyCol])) {
                                $rawQ = trim((string)$row[$qtyCol]);
                                if ($rawQ === '0' || $rawQ === 0) {
                                    $rowQty = 0;
                                } elseif ($rawQ === '' || $rawQ === '-') {
                                    $rowQty = 1;
                                } else {
                                    $rowQty = $parseQty($rawQ);
                                }
                            }
                            if ($totalCol && isset($row[$totalCol])) {
                                $rowTotal = $parseMoney($row[$totalCol]);
                            } elseif ($priceCol) {
                                $rowTotal = $rowPrice * $rowQty;
                            }
                            $grandTotal += $rowTotal;
                        @endphp
                        <tr>
                            <td style="text-align: center; width: 30px; color: #555;">{{ $rIdx + 1 }}</td>
                            @foreach($columns as $col)
                                @php
                                    $val = $row[$col] ?? '-';
                                    if (is_array($val)) {
                                        $val = implode(', ', array_filter($val, 'is_scalar'));
                                    }
                                    $isPrice = ($col === $priceCol);
                                    $isTotal = ($col === $totalCol);
                                    $isQty = ($col === $qtyCol);
                                @endphp

                                @if($isPrice)
                                    <td class="text-right">
                                        <table style="width: 100%; border-collapse: collapse; border: none;">
                                            <tr>
                                                <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                                <td style="border: none; padding: 0; text-align: right;">{{ number_format($rowPrice, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                @elseif($isTotal)
                                    <td class="text-right">
                                        <table style="width: 100%; border-collapse: collapse; border: none;">
                                            <tr>
                                                <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                                <td style="border: none; padding: 0; text-align: right;">{{ number_format($rowTotal, 0, ',', '.') }}</td>
                                            </tr>
                                        </table>
                                    </td>
                                @elseif($isQty || is_numeric($val) || (is_string($val) && strlen($val) <= 15 && preg_match('/^\d+[\s\w]*$/', trim($val))))
                                    <td class="text-center">{{ $val }}</td>
                                @else
                                    <td class="text-left">{{ $val }}</td>
                                @endif
                            @endforeach

                            @if($showCalculatedTotalCol)
                                <td class="text-right">
                                    <table style="width: 100%; border-collapse: collapse; border: none;">
                                        <tr>
                                            <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                            <td style="border: none; padding: 0; text-align: right;">{{ number_format($rowTotal, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    @if($hasPriceCalculation)
                        @php
                            $hasSeparatePriceAndTotal = ($priceCol !== null && ($showCalculatedTotalCol || $totalCol !== null));
                        @endphp
                        @if($hasSeparatePriceAndTotal)
                            <tr class="total-row">
                                <td colspan="{{ max(1, $totalColCount - 2) }}" style="border: none;"></td>
                                <td class="bg-yellow text-center" style="font-weight: bold; border: 1px solid #000; width: 35px;">Rp</td>
                                <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                    {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            </tr>
                        @else
                            <tr class="total-row">
                                <td colspan="{{ max(1, $totalColCount - 1) }}" style="border: none;"></td>
                                <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                    <table style="width: 100%; border-collapse: collapse; border: none;">
                                        <tr>
                                            <td style="border: none; padding: 0; text-align: left; width: 18px; font-weight: bold; background: transparent;">Rp</td>
                                            <td style="border: none; padding: 0; text-align: right; font-weight: bold; background: transparent;">{{ number_format($grandTotal, 0, ',', '.') }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        @endif
                    @endif
                </tbody>
            </table>
        @endforeach

        {{-- Adaptive Landscape Signatures (Pemohon & Approvers Only - Mengetahui has no signature box) --}}
        @php
            $genSignerQr = function(string $url) {
                return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(250)->margin(0)->generate($url));
            };

            $allSigners = collect();

            // 1. Pemohon (Always first in document workflow)
            $allSigners->push([
                'role' => 'Pemohon',
                'name' => format_acronyms($pemohon->name ?? '-'),
                'is_signed' => (bool)$verificationUrl,
                'status' => 'approved',
                'qr_url' => $verificationUrl ? route('verify.show', ['hash' => $document->approval_hash, 'signer' => 'requester']) : null,
            ]);

            // 2. Approvers in sequential workflow order
            foreach ($approvals as $approval) {
                $allSigners->push([
                    'role' => format_acronyms($approval->approver?->position ?? ($approval->role_label ?? 'Approver')),
                    'name' => format_acronyms($approval->approver?->name ?? '-'),
                    'is_signed' => $approval->status === 'approved' && $verificationUrl,
                    'status' => $approval->status,
                    'qr_url' => ($approval->status === 'approved' && $verificationUrl)
                        ? route('verify.show', ['hash' => $document->approval_hash, 'step' => $approval->id])
                        : null,
                ]);
            }

            // In landscape, up to 6 signers fit cleanly in 1 row. If > 6, chunk by 4.
            $chunkSize = $allSigners->count() <= 6 ? $allSigners->count() : 4;
            $signerRows = $allSigners->chunk($chunkSize);
        @endphp

        @foreach($signerRows as $row)
            @php
                $colWidth = round(100 / $row->count(), 2);
            @endphp
            <table class="sig-container" style="margin-top: 10px;">
                <tr>
                    @foreach($row as $signer)
                        <td class="sig-box-cell" style="width: {{ $colWidth }}%;">
                            <table class="sig-card">
                                <tr>
                                    <td class="sig-card-header">{{ $signer['role'] }}</td>
                                </tr>
                                <tr>
                                    <td class="sig-card-body">
                                        @if($signer['is_signed'] && $signer['qr_url'])
                                            <img src="data:image/svg+xml;base64,{{ $genSignerQr($signer['qr_url']) }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                        @else
                                            <div style="height: 70px; line-height: 70px; color: #aaa; font-size: 8.5px; text-align: center;">{{ ucfirst($signer['status'] ?? 'pending') }}</div>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td class="sig-card-footer">{{ $signer['name'] }}</td>
                                </tr>
                            </table>
                        </td>
                    @endforeach
                </tr>
            </table>
        @endforeach

        <div class="verification-footer">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="width: 80%; padding-right: 15px; vertical-align: middle; line-height: 1.4;">
                        <strong style="color: #0f172a; font-size: 9px;">Catatan Verifikasi:</strong><br>
                        Dokumen ini diterbitkan secara elektronik oleh ConfirmMe - PT Info Solusindo Data Utama. Scan QR Code di samping untuk memverifikasi keabsahan dokumen.<br>
                        <span style="color: #334155; font-size: 8px;"><strong>ID Dokumen:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...</span>
                        @php
                            $tembusanUsers = collect();
                            try { $tembusanUsers = $document->getNotifyUsers(); } catch (\Throwable $e) {}
                        @endphp
                        @if($tembusanUsers->isNotEmpty())
                            <div style="margin-top: 3px; font-size: 7.5px; color: #475569;">
                                <strong>Tembusan (Mengetahui):</strong> {{ $tembusanUsers->map(fn($u) => format_acronyms($u->name) . ' (' . format_acronyms($u->position ?? $u->department ?? 'Staff') . ')')->implode(', ') }}
                            </div>
                        @endif
                    </td>
                    <td style="width: 20%; text-align: center; vertical-align: middle;">
                        @if(isset($qrCode))
                            <div style="display: inline-block; text-align: center;">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                                <div style="font-size: 7.5px; font-weight: bold; color: #475569; margin-top: 2px;">Scan verifikasi</div>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
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
                'docDate' => $document->created_at ? $document->created_at->translatedFormat('d F Y') : now()->translatedFormat('d F Y'),
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
                                            if (!file_exists($imgPath)) {
                                                $imgPath = storage_path('app/' . $relPath);
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
                                            if (is_array($displayVal)) {
                                                $displayVal = implode(', ', array_filter($displayVal, 'is_scalar'));
                                            }
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
                                                    if (is_array($cellVal)) {
                                                        $cellVal = implode(', ', array_filter($cellVal, 'is_scalar'));
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
                            Jakarta, {{ $docDate->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-size: 11px; padding-bottom: 10px;">
                            Disetujui,
                        </td>
                    </tr>
                </table>

                @php
                    $allSigners = collect();

                    $genSignerQr = function(string $url) {
                        return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(150)->margin(1)->generate($url));
                    };

                    // 1. Pemohon (Always first in document workflow)
                    $allSigners->push([
                        'role' => __('Pemohon'),
                        'name' => format_acronyms($pemohon->name ?? 'Pemohon'),
                        'nip' => $pemohon->employee_id ?? null,
                        'is_signed' => (bool)$verificationUrl,
                        'signed_label' => 'Signed',
                        'status' => 'approved',
                        'qr_code' => $verificationUrl ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'signer' => 'requester'])) : null,
                    ]);

                    // 2. Approvers in sequential workflow order
                    foreach ($approvals as $approval) {
                        $allSigners->push([
                            'role' => format_acronyms($approval->approver?->position ?? ($approval->role_label ?? 'Approver')),
                            'name' => $approval->approver ? format_acronyms($approval->approver->name) : '-',
                            'nip' => $approval->approver?->employee_id ?? null,
                            'is_signed' => $approval->status === 'approved' && $verificationUrl,
                            'signed_label' => $approval->status === 'approved' ? 'Signed' : ucfirst($approval->status ?? 'pending'),
                            'status' => $approval->status,
                            'qr_code' => ($approval->status === 'approved' && $verificationUrl)
                                ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $approval->id]))
                                : null,
                        ]);
                    }

                    // Chunk into maximum 3 barcodes per row for large scannable display
                    $signerChunks = $allSigners->chunk(3);
                @endphp

                @foreach($signerChunks as $chunkIndex => $chunk)
                    @php
                        $countInChunk = $chunk->count();
                        if ($signerChunks->count() === 1) {
                            $tableWidth = $countInChunk === 1 ? '38%' : ($countInChunk === 2 ? '70%' : '100%');
                        } else {
                            $tableWidth = $countInChunk === 3 ? '100%' : ($countInChunk === 2 ? '66.66%' : '33.33%');
                        }
                        $cellWidth = round(100 / $countInChunk, 2) . '%';
                    @endphp

                    <table class="signature-table" align="center" style="width: {{ $tableWidth }}; margin: {{ $chunkIndex > 0 ? '14px' : '0' }} auto 0 auto;">
                        {{-- Row 1: Role Label --}}
                        <tr>
                            @foreach($chunk as $signer)
                                <td style="width: {{ $cellWidth }};">
                                    <div class="sig-role-label">{{ $signer['role'] }}</div>
                                </td>
                            @endforeach
                        </tr>

                        {{-- Row 2: QR Code / Signature Status --}}
                        <tr>
                            @foreach($chunk as $signer)
                                <td>
                                    @if($signer['is_signed'])
                                        <div class="sig-qr-box">
                                            <img src="data:image/svg+xml;base64,{{ $signer['qr_code'] ?? $qrCode }}" alt="QR Code">
                                        </div>
                                        <div class="sig-status-signed">{{ $signer['signed_label'] }}</div>
                                    @elseif($signer['status'] === 'approved')
                                        <div class="sig-status-approved">APPROVED</div>
                                    @else
                                        <div class="sig-pending-box">
                                            <span>{{ $signer['signed_label'] }}</span>
                                        </div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>

                        {{-- Row 3: Name & NIP --}}
                        <tr>
                            @foreach($chunk as $signer)
                                <td>
                                    <div class="sig-name">( {{ $signer['name'] }} )</div>
                                    @if(!empty($signer['nip']))
                                        <div class="sig-nip">NIP. {{ $signer['nip'] }}</div>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    </table>
                @endforeach
            </div>

            <!-- Verification Footer -->
            <div class="verification-footer">
                <table>
                    <tr>
                        <td style="width: 74%; padding-right: 15px; vertical-align: middle; line-height: 1.45;">
                            <strong style="color: #0f172a; font-size: 9px;">{{ __("Catatan Verifikasi") }}:</strong><br>
                            {{ __("Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe - PT Info Solusindo Data Utama.") }}
                            {{ __("Scan QR Code di samping untuk memverifikasi keaslian dokumen.") }}<br>
                            <span style="color: #1e293b; font-size: 8px;"><strong>ID Dokumen:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...</span>
                        </td>
                        <td style="width: 26%; text-align: center; vertical-align: middle;">
                            @if(isset($qrCode))
                                <div style="display: inline-block; text-align: center;">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 78px; height: 78px; display: block; margin: 0 auto;">
                                    <div style="font-size: 7.5px; font-weight: bold; color: #475569; margin-top: 3px;">{{ __("Scan untuk verifikasi") }}</div>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif
</body>

</html>
