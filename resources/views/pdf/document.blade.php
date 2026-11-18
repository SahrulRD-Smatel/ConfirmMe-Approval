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
                width: 175px;
                padding: 3.5px 0;
                color: #333;
                font-weight: normal;
                font-size: 11px;
                vertical-align: top;
                border: none;
            }

            .info-table td {
                padding: 3.5px 0;
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
                width: 70px;
                height: 70px;
                margin: 2px auto 1px auto;
                display: block;
            }

            .sig-qr-box img {
                width: 70px;
                height: 70px;
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
                height: 70px;
                line-height: 70px;
            }

            .sig-pending-box {
                width: 70px;
                height: 70px;
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
        $template = $template ?? ($document->documentTemplate ?? null);
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

            $logoPath = file_exists(public_path('images/logo.png'))
                ? public_path('images/logo.png')
                : public_path('images/logo-infodata.jpg');
            $logoMime = str_ends_with($logoPath, '.png') ? 'image/png' : 'image/jpeg';
            $logoDataUri = file_exists($logoPath)
                ? 'data:' . $logoMime . ';base64,' . base64_encode(file_get_contents($logoPath))
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
                        <img src="{{ $logoDataUri }}" style="height: 64px; width: auto; margin-bottom: 5px; display: block;" />
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
                return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->margin(1)->generate($url));
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
            $templateSigners = $template->fields['workflow_signers'] ?? ($document->template->fields['workflow_signers'] ?? []);
            $templateWorkflow = $template ? ($template->workflow ?? []) : [];
            foreach ($approvals as $approval) {
                $orderIndex = $approval->approval_order - 1;

                // Cari konfigurasi signer: prioritas cocokkan by role di workflow template; fallback orderIndex hanya jika template tidak memiliki definisi workflow role
                $signerCfg = null;
                if (!empty($templateSigners) && !empty($templateWorkflow)) {
                    $approverRole = $approval->approver_role;
                    foreach ($templateWorkflow as $wfIndex => $wfRole) {
                        if ($wfRole === $approverRole || str_replace(['role:', 'hierarchy:'], '', (string)$wfRole) === str_replace(['role:', 'hierarchy:'], '', (string)$approverRole)) {
                            $signerCfg = $templateSigners[$wfIndex] ?? null;
                            break;
                        }
                    }
                } elseif (!empty($templateSigners) && isset($templateSigners[$orderIndex])) {
                    $signerCfg = $templateSigners[$orderIndex];
                }

                $showInPdf = true;
                if ($signerCfg !== null && isset($signerCfg['show_in_pdf'])) {
                    $showInPdf = (bool) $signerCfg['show_in_pdf'];
                } elseif ($approval->show_in_pdf !== null) {
                    $showInPdf = (bool) $approval->show_in_pdf;
                }

                if (!$showInPdf) {
                    continue;
                }

                $customLabel = null;
                if ($signerCfg !== null) {
                    // Jika konfigurasi template ditemukan: jika diisi custom pakai custom, jika dikosongkan kembali ke jabatan asli
                    $customLabel = !empty($signerCfg['pdf_label']) ? trim($signerCfg['pdf_label']) : null;
                } elseif (!empty($approval->pdf_label)) {
                    $customLabel = $approval->pdf_label;
                }

                $roleTitle = $customLabel ? format_acronyms($customLabel) : format_acronyms($approval->approver?->position ?? ($approval->role_label ?? 'Approver'));

                $allSigners->push([
                    'role' => $roleTitle,
                    'name' => format_acronyms($approval->approver?->name ?? '-'),
                    'is_signed' => $approval->status === 'approved' && $verificationUrl,
                    'status' => $approval->status,
                    'qr_url' => ($approval->status === 'approved' && $verificationUrl)
                        ? route('verify.show', ['hash' => $document->approval_hash, 'step' => $approval->id])
                        : null,
                ]);
            }

            // In landscape, if more than 4 signers, split evenly into 2 rows (e.g. 5 => 3 & 2, 6 => 3 & 3, 7 => 4 & 3, 8 => 4 & 4)
            $chunkSize = $allSigners->count() > 4 ? (int) ceil($allSigners->count() / 2) : $allSigners->count();
            $signerRows = $allSigners->chunk($chunkSize);
        @endphp

        @foreach($signerRows as $rowIndex => $row)
            @php
                $tableWidth = round(($row->count() / $chunkSize) * 100, 2) . '%';
                $colWidth = round(100 / $row->count(), 2);
            @endphp
            <table class="sig-container" align="center" style="width: {{ $tableWidth }}; margin: {{ $rowIndex > 0 ? '12px' : '8px' }} auto 0 auto;">
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
                    <td style="width: 78%; padding-right: 15px; vertical-align: middle; line-height: 1.45;">
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
                    <td style="width: 22%; text-align: center; vertical-align: middle;">
                        @if(isset($qrCode))
                            <div style="display: inline-block; text-align: center;">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                                <div style="font-size: 8px; font-weight: bold; color: #334155; margin-top: 3px;">Scan verifikasi</div>
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
            if (empty($categoryName) || $categoryName === 'peminjaman_aset') {
                $categoryName = 'PENGGUNAAN KENDARAAN OPERASIONAL';
            }
            $categoryName = strtoupper(str_replace('_', ' ', (string)$categoryName));
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
            @php
                $templateCode = strtolower(str_replace(['-', ' '], '_', (string)($template ? $template->code : ($document->template ?? ''))));
                $isStpd = $templateCode === 'stpd' || (!empty($metadata['pemberi_tugas_nama']) && !empty($metadata['nama_yang_ditugaskan']));
                $isUmbpd = $templateCode === 'hr_umbpd' || (!empty($metadata['pemesanan_tiket']) && !empty($metadata['total_estimasi_pemohon']));

                $genSignerQr = function(string $url) {
                    return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->margin(1)->generate($url));
                };

                $templateSigners = $template->fields['workflow_signers'] ?? ($document->template->fields['workflow_signers'] ?? []);
                $templateWorkflow = $template ? ($template->workflow ?? []) : [];

                $checkShowInPdf = function($approval) use ($templateSigners, $templateWorkflow) {
                    if (!$approval) return false;
                    $orderIdx = $approval->approval_order - 1;
                    $signerCfg = null;
                    if (!empty($templateSigners) && isset($templateSigners[$orderIdx])) {
                        $signerCfg = $templateSigners[$orderIdx];
                    } elseif (!empty($templateSigners) && !empty($templateWorkflow)) {
                        $approverRole = $approval->approver_role;
                        foreach ($templateWorkflow as $wfIndex => $wfRole) {
                            if ($wfRole === $approverRole || str_replace(['role:', 'hierarchy:'], '', (string)$wfRole) === str_replace(['role:', 'hierarchy:'], '', (string)$approverRole)) {
                                $signerCfg = $templateSigners[$wfIndex] ?? null;
                                break;
                            }
                        }
                    }

                    if ($signerCfg !== null && isset($signerCfg['show_in_pdf'])) {
                        return (bool) $signerCfg['show_in_pdf'];
                    }
                    return $approval->show_in_pdf !== null ? (bool)$approval->show_in_pdf : true;
                };

                $getSignerLabel = function($approval, $defaultLabel) use ($templateSigners, $templateWorkflow) {
                    if (!$approval) return $defaultLabel;
                    $orderIdx = $approval->approval_order - 1;
                    $signerCfg = null;
                    if (!empty($templateSigners) && isset($templateSigners[$orderIdx])) {
                        $signerCfg = $templateSigners[$orderIdx];
                    } elseif (!empty($templateSigners) && !empty($templateWorkflow)) {
                        $approverRole = $approval->approver_role;
                        foreach ($templateWorkflow as $wfIndex => $wfRole) {
                            if ($wfRole === $approverRole || str_replace(['role:', 'hierarchy:'], '', (string)$wfRole) === str_replace(['role:', 'hierarchy:'], '', (string)$approverRole)) {
                                $signerCfg = $templateSigners[$wfIndex] ?? null;
                                break;
                            }
                        }
                    }

                    if ($signerCfg !== null && !empty($signerCfg['pdf_label'])) {
                        return trim($signerCfg['pdf_label']);
                    }
                    if (!empty($approval->pdf_label)) {
                        return trim($approval->pdf_label);
                    }
                    return $defaultLabel;
                };
            @endphp

            @if($isStpd)
                {{-- ========================================================================= --}}
                {{-- PRESET STPD: SURAT TUGAS PERJALANAN DINAS (SESUAI EXCEL LEMBAR 1)          --}}
                {{-- ========================================================================= --}}
                @php
                    $pemberiNama = $metadata['pemberi_tugas_nama'] ?? ($metadata['nama_pemberi_tugas'] ?? '-');
                    $pemberiJabatan = $metadata['pemberi_tugas_jabatan'] ?? ($metadata['jabatan_pemberi_tugas'] ?? '-');
                    $pemberiDept = $metadata['pemberi_tugas_dept'] ?? ($metadata['dept_div_pemberi_tugas'] ?? ($metadata['dept_pemberi_tugas'] ?? '-'));

                    $penerimaNama = $metadata['nama_yang_ditugaskan'] ?? ($metadata['nama_penerima_tugas'] ?? ($metadata['nama_karyawan'] ?? '-'));
                    $penerimaJabatan = $metadata['jabatan_yang_ditugaskan'] ?? ($metadata['jabatan'] ?? '-');
                    $penerimaDept = $metadata['dept_div_yang_ditugaskan'] ?? ($metadata['dept_div'] ?? ($metadata['departemen'] ?? '-'));

                    $tugasVal = $metadata['tugas'] ?? ($metadata['tugas_keperluan'] ?? '-');
                    $kotaVal = $metadata['kota_tujuan'] ?? ($metadata['tujuan'] ?? '-');
                    $tglBerangkat = !empty($metadata['tanggal_berangkat']) ? \Carbon\Carbon::parse($metadata['tanggal_berangkat'])->translatedFormat('d/m/Y') : '-';
                    $tglKembali = !empty($metadata['tanggal_kembali']) ? \Carbon\Carbon::parse($metadata['tanggal_kembali'])->translatedFormat('d/m/Y') : '-';
                    $transportVal = $metadata['jenis_transportasi'] ?? '-';
                    $catatanVal = $metadata['catatan'] ?? ($metadata['catatan_tambahan'] ?? null);

                    $pemberiDate = $docDate->translatedFormat('d/m/Y');
                    $penerimaDate = $docDate->translatedFormat('d/m/Y');
                    $atasanDate = '............';
                    $atasanNama = '...........................';
                    $pemberiQr = null;
                    $penerimaQr = null;
                    $atasanQr = null;

                    $creatorNameNorm = strtolower(trim($pemohon->name ?? ''));
                    $pemberiNorm = strtolower(trim($pemberiNama));
                    $penerimaNorm = strtolower(trim($penerimaNama));

                    $isRequesterPemberi = ($creatorNameNorm && $pemberiNorm && str_contains($pemberiNorm, $creatorNameNorm));
                    $pemohonQrCode = $verificationUrl ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'signer' => 'requester'])) : null;

                    // Cari approver pemberi tugas & atasan langsung
                    $pemberiApproval = null;
                    if (!$isRequesterPemberi && $pemberiNorm) {
                        $pemberiApproval = $document->approvals->first(function($a) use ($pemberiNorm) {
                            if (!$a->approver) return false;
                            $name = strtolower(trim($a->approver->name));
                            return str_contains($pemberiNorm, $name) || str_contains($name, $pemberiNorm);
                        });
                    }

                    if (!$isRequesterPemberi && !$pemberiApproval && $document->approvals->count() >= 2) {
                        $pemberiApproval = $document->approvals->get(1);
                    }

                    $atasanApproval = null;
                    if ($pemberiApproval) {
                        $atasanApproval = $document->approvals->where('id', '!=', $pemberiApproval->id)->first();
                    } else {
                        $atasanApproval = $document->approvals->first();
                    }

                    // 1. Yang Menugaskan (Pemberi Tugas)
                    if ($isRequesterPemberi) {
                        $pemberiQr = $pemohonQrCode;
                        $pemberiNama = format_acronyms($pemberiNama ?: ($pemohon->name ?? '-'));
                    } elseif ($pemberiApproval) {
                        if ($pemberiApproval->approver) {
                            $pemberiNama = format_acronyms($pemberiApproval->approver->name);
                        }
                        if ($pemberiApproval->action_at) {
                            $pemberiDate = $pemberiApproval->action_at->translatedFormat('d/m/Y');
                        }
                        if ($checkShowInPdf($pemberiApproval) && $pemberiApproval->status === 'approved' && $verificationUrl) {
                            $pemberiQr = $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $pemberiApproval->id]));
                        }
                    }

                    // 2. Atasan Langsung
                    if ($atasanApproval) {
                        if ($atasanApproval->approver) {
                            $atasanNama = format_acronyms($atasanApproval->approver->name);
                        }
                        if ($atasanApproval->action_at) {
                            $atasanDate = $atasanApproval->action_at->translatedFormat('d/m/Y');
                        }
                        if ($checkShowInPdf($atasanApproval) && $atasanApproval->status === 'approved' && $verificationUrl) {
                            $atasanQr = $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $atasanApproval->id]));
                        }
                    }

                    // 3. Yang Ditugaskan (Penerima Tugas)
                    if (!$isRequesterPemberi) {
                        $penerimaQr = $pemohonQrCode;
                        $penerimaNama = format_acronyms($penerimaNama ?: ($pemohon->name ?? '-'));
                    }
                @endphp

                <!-- Document Title -->
                <div class="doc-title-banner" style="margin-top: 4px; margin-bottom: 14px;">
                    {{ $document->title }}
                </div>

                <!-- Bagian 1: Pemberi Tugas -->
                <div style="font-size: 11px; margin-bottom: 3px; color: #111;">Yang bertanda tangan di bawah ini,</div>
                <table class="info-table" style="margin-bottom: 10px;">
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Nama</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $pemberiNama }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Jabatan</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $pemberiJabatan }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Dept/Div</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $pemberiDept }}</td>
                    </tr>
                </table>

                <!-- Bagian 2: Penerima Tugas -->
                <div style="font-size: 11px; margin-bottom: 3px; color: #111;">Menugaskan,</div>
                <table class="info-table" style="margin-bottom: 10px;">
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Nama</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $penerimaNama }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Jabatan</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $penerimaJabatan }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Dept/Div</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $penerimaDept }}</td>
                    </tr>
                </table>

                <!-- Bagian 3: Pelaksanaan Dinas -->
                <div style="font-size: 11px; margin-bottom: 3px; color: #111;">Untuk melaksanakan perjalanan dinas</div>
                <table class="info-table" style="margin-bottom: 10px;">
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Tugas</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{!! nl2br(e($tugasVal)) !!}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Kota tujuan</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $kotaVal }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Tanggal Berangkat</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $tglBerangkat }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Tanggal Kembali</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $tglKembali }}</td>
                    </tr>
                    <tr>
                        <th style="width: 140px; padding: 2.5px 0;">Jenis Transportasi</th>
                        <td style="width: 12px; padding: 2.5px 0;">:</td>
                        <td style="padding: 2.5px 0;">{{ $transportVal }}</td>
                    </tr>
                </table>

                <!-- Bagian 4: Catatan Box Sesuai Excel -->
                <div style="border: 1px solid #111; padding: 5px 8px; margin-top: 6px; margin-bottom: 12px; min-height: 44px; page-break-inside: avoid;">
                    <strong style="display: block; font-size: 11px; margin-bottom: 2px;">Catatan :</strong>
                    <div style="font-size: 10px; color: #222;">
                        {!! $catatanVal ? nl2br(e($catatanVal)) : '&nbsp;' !!}
                    </div>
                </div>

                <!-- Bagian 5: Tabel Tanda Tangan 4 Kolom Sesuai Excel -->
                <table style="width: 100%; border-collapse: collapse; margin-top: 10px; page-break-inside: avoid;">
                    <thead>
                        <tr>
                            <th style="width: 25%; border: 1px solid #111; padding: 4px; text-align: center; font-size: 10px; font-weight: bold; background-color: #f8fafc;">Yang menugaskan</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 4px; text-align: center; font-size: 10px; font-weight: bold; background-color: #f8fafc;">Atasan Langsung</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 4px; text-align: center; font-size: 10px; font-weight: bold; background-color: #f8fafc;">Pejabat Setempat</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 4px; text-align: center; font-size: 10px; font-weight: bold; background-color: #f8fafc;">Yang ditugaskan</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 3px;">
                                @if($pemberiQr)
                                    <img src="data:image/svg+xml;base64,{{ $pemberiQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 3px;">
                                @if($atasanQr)
                                    <img src="data:image/svg+xml;base64,{{ $atasanQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 3px;">
                                <div style="height: 70px; color: #999; font-size: 8px; line-height: 70px; font-style: italic;">(Cap / Paraf Cabang)</div>
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 3px;">
                                @if($penerimaQr)
                                    <img src="data:image/svg+xml;base64,{{ $penerimaQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #111; padding: 3px; text-align: center; font-size: 9px;">
                                <div style="font-weight: bold;">( {{ $pemberiNama }} )</div>
                                <div style="margin-top: 2px; font-size: 8px; color: #333;">Tgl : {{ $pemberiDate }}</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 3px; text-align: center; font-size: 9px;">
                                <div style="font-weight: bold;">( {{ $atasanNama }} )</div>
                                <div style="margin-top: 2px; font-size: 8px; color: #333;">Tgl : {{ $atasanDate }}</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 3px; text-align: center; font-size: 9px;">
                                <div style="font-weight: bold;">( &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; )</div>
                                <div style="margin-top: 2px; font-size: 8px; color: #333;">Tgl : &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 3px; text-align: center; font-size: 9px;">
                                <div style="font-weight: bold;">( {{ $penerimaNama }} )</div>
                                <div style="margin-top: 2px; font-size: 8px; color: #333;">Tgl : {{ $penerimaDate }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

            @elseif($isUmbpd)
                {{-- ========================================================================= --}}
                {{-- PRESET HR_UMBPD: TIKET & UANG MUKA PERJALANAN DINAS (SESUAI EXCEL LEMBAR 2)--}}
                {{-- ========================================================================= --}}
                @php
                    $pemohonNama = $metadata['nama_pemohon'] ?? ($pemohon->name ?? '-');
                    $divisiDept = $metadata['divisi_dept'] ?? (is_string($pemohon?->department) ? $pemohon->department : ($pemohon?->department?->name ?? '-'));
                    $kotaTujuan = $metadata['kota_tujuan'] ?? '-';
                    $keperluanVal = $metadata['keperluan'] ?? '-';
                    $tglBerangkat = !empty($metadata['tgl_berangkat']) ? \Carbon\Carbon::parse($metadata['tgl_berangkat'])->translatedFormat('d/m/Y') : '-';
                    $tglKembali = !empty($metadata['tgl_kembali']) ? \Carbon\Carbon::parse($metadata['tgl_kembali'])->translatedFormat('d/m/Y') : '-';
                    $catatanTiket = $metadata['catatan_tiket'] ?? '-';

                    $totalEstimasi = !empty($metadata['total_estimasi_pemohon']) ? (float)$metadata['total_estimasi_pemohon'] : 0;

                    $diketahuiApprover = $document->approvals->get(0);
                    $hrdApprover = $document->approvals->get(1);
                    $financeApprover = $document->approvals->get(2);

                    $diketahuiName = ($diketahuiApprover && $diketahuiApprover->approver) ? format_acronyms($diketahuiApprover->approver->name) : '...........................';
                    $diketahuiDate = ($diketahuiApprover && $diketahuiApprover->action_at) ? $diketahuiApprover->action_at->translatedFormat('d/m/Y') : '............';
                    $diketahuiQr = ($diketahuiApprover && $checkShowInPdf($diketahuiApprover) && $diketahuiApprover->status === 'approved') ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $diketahuiApprover->id])) : null;
                    $diketahuiLabel = $getSignerLabel($diketahuiApprover, 'Diketahui');

                    $hrdName = ($hrdApprover && $hrdApprover->approver) ? format_acronyms($hrdApprover->approver->name) : ($metadata['hrd_name'] ?? '...........................');
                    $hrdDate = ($hrdApprover && $hrdApprover->action_at) ? $hrdApprover->action_at->translatedFormat('d/m/Y') : '............';
                    $hrdQr = ($hrdApprover && $checkShowInPdf($hrdApprover) && $hrdApprover->status === 'approved') ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $hrdApprover->id])) : null;
                    $hrdLabel = $getSignerLabel($hrdApprover, 'HRD');

                    $financeName = ($financeApprover && $financeApprover->approver) ? format_acronyms($financeApprover->approver->name) : ($metadata['finance_name'] ?? '...........................');
                    $financeDate = ($financeApprover && $financeApprover->action_at) ? $financeApprover->action_at->translatedFormat('d/m/Y') : '............';
                    $financeQr = ($financeApprover && $checkShowInPdf($financeApprover) && $financeApprover->status === 'approved') ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'step' => $financeApprover->id])) : null;
                    $financeLabel = $getSignerLabel($financeApprover, 'Finance');

                    $pemohonQr = $verificationUrl ? $genSignerQr(route('verify.show', ['hash' => $document->approval_hash, 'signer' => 'requester'])) : null;
                @endphp

                <!-- Form Identifier Top Right -->
                <div style="text-align: right; font-size: 9.5px; font-weight: bold; margin-top: -6px; margin-bottom: 2px; letter-spacing: 0.5px; color: #111;">
                    Form 01A/HR-UMBPD/05
                </div>

                <!-- Document Title -->
                <div class="doc-title-banner" style="margin-top: 2px; margin-bottom: 10px; font-size: 11.5px; line-height: 1.35;">
                    PEMESANAN TIKET &amp; PERMINTAAN UANG MUKA<br>PERJALANAN DINAS
                </div>

                <!-- Info Pemohon Table -->
                <table class="info-table" style="margin-bottom: 8px;">
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Nama Pemohon</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{{ $pemohonNama }}</td>
                    </tr>
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Divisi / Dept</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{{ $divisiDept }}</td>
                    </tr>
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Kota Tujuan</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{{ $kotaTujuan }}</td>
                    </tr>
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Keperluan</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{!! nl2br(e($keperluanVal)) !!}</td>
                    </tr>
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Tgl Berangkat</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{{ $tglBerangkat }}</td>
                    </tr>
                    <tr>
                        <th style="width: 120px; padding: 2px 0;">Tgl Kembali</th>
                        <td style="width: 12px; padding: 2px 0;">:</td>
                        <td style="padding: 2px 0;">{{ $tglKembali }}</td>
                    </tr>
                </table>

                <!-- Tabel 1: Pemesanan Tiket (8 Kolom Sesuai Excel) -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 2px; font-size: 9px;">
                    <thead>
                        <tr style="background-color: #cbd5e1;">
                            <th style="width: 25px; border: 1px solid #111; padding: 2.5px; text-align: center;">No</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Tanggal</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Dari</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Tujuan</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Jam</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Jenis Transportasi*</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center;">Kelas*</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center; width: 90px;">Harga Tiket</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $tiketRows = $metadata['pemesanan_tiket'] ?? [];
                            if (!is_array($tiketRows) || count($tiketRows) === 0) {
                                $tiketRows = [];
                            }
                            while (count($tiketRows) < 5) {
                                $tiketRows[] = [];
                            }
                        @endphp
                        @foreach($tiketRows as $idx => $tRow)
                            @php $tRow = (array)$tRow; @endphp
                            <tr>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $idx + 1 }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['tanggal'] ?? ($tRow['Tanggal'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['dari'] ?? ($tRow['Dari'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['tujuan'] ?? ($tRow['Tujuan'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['jam'] ?? ($tRow['Jam'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['jenis_transportasi'] ?? ($tRow['Jenis Transportasi'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center;">{{ $tRow['kelas'] ?? ($tRow['Kelas'] ?? '') }}</td>
                                <td style="border: 1px solid #111; padding: 2px; text-align: center; font-size: 8px; color: #333;">Team HR</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="7" style="border: 1px solid #111; padding: 2px 6px; text-align: right; font-weight: bold;">Total</td>
                            <td style="border: 1px solid #111; padding: 2px; text-align: right; font-weight: bold; font-size: 8.5px;">Rp0</td>
                        </tr>
                    </tbody>
                </table>
                <div style="border: 1px solid #111; border-top: none; padding: 2.5px 6px; margin-bottom: 8px; font-size: 9px;">
                    <strong>Catatan :</strong> {{ $catatanTiket }}
                </div>

                <!-- Tabel 2: Perincian Uang Muka Sesuai Excel -->
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px; font-size: 9px;">
                    <thead>
                        <tr style="background-color: #cbd5e1;">
                            <th colspan="2" style="border: 1px solid #111; padding: 2.5px; text-align: center; font-weight: bold;">Perincian Uang Muka</th>
                            <th style="border: 1px solid #111; padding: 2.5px; text-align: center; width: 140px; font-weight: bold;">Jumlah (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="width: 25px; border: 1px solid #111; padding: 2px; text-align: center;">1</td>
                            <td style="border: 1px solid #111; padding: 2px 6px;">Airport Tax</td>
                            <td style="border: 1px solid #111; padding: 2px; text-align: center; color: #444; font-size: 8px;">{{ !empty($metadata['um_airport_tax']) ? number_format((float)$metadata['um_airport_tax'], 0, ',', '.') : 'Team HR' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25px; border: 1px solid #111; padding: 2px; text-align: center;">2</td>
                            <td style="border: 1px solid #111; padding: 2px 6px;">Akomodasi (hotel / mess / kel)</td>
                            <td style="border: 1px solid #111; padding: 2px; text-align: center; color: #444; font-size: 8px;">{{ !empty($metadata['um_akomodasi']) ? number_format((float)$metadata['um_akomodasi'], 0, ',', '.') : 'Team HR' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #111; padding: 2px; text-align: center; vertical-align: top;" rowspan="2">3</td>
                            <td style="border: 1px solid #111; padding: 2px 6px;">
                                Transportasi :<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;a.&nbsp;&nbsp;Antar kota (Taxi)
                            </td>
                            <td style="border: 1px solid #111; padding: 2px; text-align: center; vertical-align: middle; color: #444; font-size: 8px;">{{ !empty($metadata['um_transport_antar_kota']) ? number_format((float)$metadata['um_transport_antar_kota'], 0, ',', '.') : 'Team HR' }}</td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #111; padding: 2px 6px;">
                                &nbsp;&nbsp;&nbsp;&nbsp;b.&nbsp;&nbsp;Dalam kota (tujuan & domisili)
                            </td>
                            <td style="border: 1px solid #111; padding: 2px 6px; text-align: right;">{{ !empty($metadata['um_transport_dalam_kota']) ? number_format((float)$metadata['um_transport_dalam_kota'], 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25px; border: 1px solid #111; padding: 2px; text-align: center;">4</td>
                            <td style="border: 1px solid #111; padding: 2px 6px;">Uang Saku x Hr</td>
                            <td style="border: 1px solid #111; padding: 2px 6px; text-align: right;">{{ !empty($metadata['um_uang_saku']) ? number_format((float)$metadata['um_uang_saku'], 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25px; border: 1px solid #111; padding: 2px 6px;">Insidental x Hr</td>
                            <td style="border: 1px solid #111; padding: 2px 6px; text-align: right;">{{ !empty($metadata['um_insidental']) ? number_format((float)$metadata['um_insidental'], 0, ',', '.') : '-' }}</td>
                        </tr>
                        <tr>
                            <td style="width: 25px; border: 1px solid #111; padding: 2px; text-align: center; vertical-align: top;">6</td>
                            <td style="border: 1px solid #111; padding: 2px 6px;">
                                Lain – lain :<br>
                                &nbsp;&nbsp;&nbsp;&nbsp;a.&nbsp;&nbsp;Biaya tak terduga didalam kota tujuan
                            </td>
                            <td style="border: 1px solid #111; padding: 2px; text-align: center; vertical-align: middle; font-size: 8px; color: #444;">{{ !empty($metadata['um_biaya_tak_terduga']) ? number_format((float)$metadata['um_biaya_tak_terduga'], 0, ',', '.') : 'Perhitungan dari Team HR' }}</td>
                        </tr>
                        <tr style="background-color: #f1f5f9; font-weight: bold;">
                            <td colspan="2" style="border: 1px solid #111; padding: 3px 6px; text-align: center; text-transform: uppercase;">TOTAL ( Estimasi dari Pemohon )</td>
                            <td style="border: 1px solid #111; padding: 3px 6px; text-align: right;">Rp {{ number_format($totalEstimasi, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Tabel Tanda Tangan 4 Kolom Sesuai Excel -->
                <table style="width: 100%; border-collapse: collapse; margin-top: 8px; page-break-inside: avoid;">
                    <thead>
                        <tr>
                            <th style="width: 25%; border: 1px solid #111; padding: 3.5px; text-align: center; font-size: 9.5px; font-weight: bold; background-color: #f8fafc;">Pemohon</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 3.5px; text-align: center; font-size: 9.5px; font-weight: bold; background-color: #f8fafc;">{{ $diketahuiLabel }}</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 3.5px; text-align: center; font-size: 9.5px; font-weight: bold; background-color: #f8fafc;">{{ $hrdLabel }}</th>
                            <th style="width: 25%; border: 1px solid #111; padding: 3.5px; text-align: center; font-size: 9.5px; font-weight: bold; background-color: #f8fafc;">{{ $financeLabel }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 2px;">
                                @if($pemohonQr)
                                    <img src="data:image/svg+xml;base64,{{ $pemohonQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 2px;">
                                @if($diketahuiQr)
                                    <img src="data:image/svg+xml;base64,{{ $diketahuiQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 2px;">
                                @if($hrdQr)
                                    <img src="data:image/svg+xml;base64,{{ $hrdQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                            <td style="border: 1px solid #111; height: 76px; vertical-align: middle; text-align: center; padding: 2px;">
                                @if($financeQr)
                                    <img src="data:image/svg+xml;base64,{{ $financeQr }}" style="width: 70px; height: 70px; display: block; margin: 0 auto;" />
                                    <div style="font-size: 7px; color: #166534; font-weight: bold; margin-top: 1px;">Signed</div>
                                @else
                                    <div style="height: 70px;"></div>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td style="border: 1px solid #111; padding: 2.5px; text-align: center; font-size: 8.5px;">
                                <div style="font-weight: bold;">( {{ $pemohonNama }} )</div>
                                <div style="margin-top: 1.5px; font-size: 7.5px; color: #333;">Tanggal : {{ $docDate->translatedFormat('d/m/Y') }}</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 2.5px; text-align: center; font-size: 8.5px;">
                                <div style="font-weight: bold;">( {{ $diketahuiName }} )</div>
                                <div style="margin-top: 1.5px; font-size: 7.5px; color: #333;">Tanggal : {{ $diketahuiDate }}</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 2.5px; text-align: center; font-size: 8.5px;">
                                <div style="font-weight: bold;">( {{ $hrdName }} )</div>
                                <div style="margin-top: 1.5px; font-size: 7.5px; color: #333;">Tgl : {{ $hrdDate }}</div>
                            </td>
                            <td style="border: 1px solid #111; padding: 2.5px; text-align: center; font-size: 8.5px;">
                                <div style="font-weight: bold;">( {{ $financeName }} )</div>
                                <div style="margin-top: 1.5px; font-size: 7.5px; color: #333;">Tgl : {{ $financeDate }}</div>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <!-- Footer Legend Sesuai Excel -->
                <div style="font-size: 8px; line-height: 1.4; color: #333; margin-top: 6px; page-break-inside: avoid;">
                    <strong>*) Keterangan :</strong><br>
                    Jenis Transportasi &nbsp;: &nbsp;[BIS] Bis; &nbsp;[PU] Pesawat Udara; &nbsp;[KA] Kereta Api<br>
                    Kelas &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: &nbsp;[FC] Bisnis; &nbsp;[EKO] Ekonomi<br>
                    Tanggal perkiraan kembali WAJIB dicantumkan meskipun belum dapat dipastikan.
                </div>

            @else
                <!-- Document Title -->
                <div class="doc-title-banner" style="margin-top: 4px; margin-bottom: 16px;">
                    {{ $document->title }}
                </div>

            <!-- Intro Paragraph (Description) -->
            @if($document->description)
                <div style="margin-bottom: 12px; font-size: 11px; line-height: 1.6; text-align: justify; color: #222;">
                    {!! nl2br(e($document->description)) !!}
                </div>
            @endif

            {{-- Dynamic Fields: Key-Value Form Fields (Universal Dynamic Rendering) --}}
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
                                <th style="width: 175px;">{{ $fLabel }}</th>
                                <td style="width: 15px; text-align: left;">:</td>
                                <td>
                                    @if($fType === 'textarea')
                                        {!! nl2br(e($fVal ?: '-')) !!}
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
                                            <em>{{ basename($fVal) }}</em>
                                        @endif
                                    @else
                                        @php
                                            $displayVal = $fVal ?: '-';
                                            if (is_array($displayVal)) {
                                                $displayVal = implode(', ', array_filter($displayVal, 'is_scalar'));
                                            }
                                            if ($fVal && $fType === 'date' && strtotime($fVal)) {
                                                $displayVal = \Carbon\Carbon::parse($fVal)->translatedFormat('d/m/Y');
                                            } elseif ($fVal && $fType === 'time') {
                                                $displayVal = (strpos($fVal, 'WIB') !== false) ? $fVal : $fVal . ' WIB';
                                            } elseif ($fVal && $fType === 'number' && is_numeric($fVal)) {
                                                $displayVal = number_format((float)$fVal, 0, ',', '.');
                                            }
                                        @endphp

                                        {{ $displayVal }}

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
            <div class="signature-section" style="margin-top: 35px;">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 6px;">
                    <tr>
                        <td style="text-align: right; font-size: 11px; padding-bottom: 6px;">
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
                        return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->margin(1)->generate($url));
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
                    $templateSigners = $template->fields['workflow_signers'] ?? ($document->template->fields['workflow_signers'] ?? []);
                    $templateWorkflow = $template ? ($template->workflow ?? []) : [];
                    foreach ($approvals as $approval) {
                        $orderIndex = $approval->approval_order - 1;

                        // Cari konfigurasi signer: prioritas cocokkan by role di workflow template; fallback orderIndex hanya jika template tidak memiliki definisi workflow role
                        $signerCfg = null;
                        if (!empty($templateSigners) && !empty($templateWorkflow)) {
                            $approverRole = $approval->approver_role;
                            foreach ($templateWorkflow as $wfIndex => $wfRole) {
                                if ($wfRole === $approverRole || str_replace(['role:', 'hierarchy:'], '', (string)$wfRole) === str_replace(['role:', 'hierarchy:'], '', (string)$approverRole)) {
                                    $signerCfg = $templateSigners[$wfIndex] ?? null;
                                    break;
                                }
                            }
                        } elseif (!empty($templateSigners) && isset($templateSigners[$orderIndex])) {
                            $signerCfg = $templateSigners[$orderIndex];
                        }

                        $showInPdf = true;
                        if ($signerCfg !== null && isset($signerCfg['show_in_pdf'])) {
                            $showInPdf = (bool) $signerCfg['show_in_pdf'];
                        } elseif ($approval->show_in_pdf !== null) {
                            $showInPdf = (bool) $approval->show_in_pdf;
                        }

                        if (!$showInPdf) {
                            continue;
                        }

                        $customLabel = null;
                        if ($signerCfg !== null) {
                            // Jika konfigurasi template ditemukan: jika diisi custom pakai custom, jika dikosongkan kembali ke jabatan asli
                            $customLabel = !empty($signerCfg['pdf_label']) ? trim($signerCfg['pdf_label']) : null;
                        } elseif (!empty($approval->pdf_label)) {
                            $customLabel = $approval->pdf_label;
                        }

                        $roleTitle = $customLabel ? format_acronyms($customLabel) : format_acronyms($approval->approver?->position ?? ($approval->role_label ?? 'Approver'));

                        $allSigners->push([
                            'role' => $roleTitle,
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

                    // If more than 4 signers, split evenly into 2 rows (e.g. 5 => 3 & 2, 6 => 3 & 3, 7 => 4 & 3, 8 => 4 & 4)
                    $chunkSize = $allSigners->count() > 4 ? (int) ceil($allSigners->count() / 2) : $allSigners->count();
                    $signerChunks = $allSigners->chunk($chunkSize);
                @endphp

                @foreach($signerChunks as $chunkIndex => $chunk)
                    @php
                        $countInChunk = $chunk->count();
                        $tableWidth = round(($countInChunk / $chunkSize) * 100, 2) . '%';
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
            @endif

            @if(!$isUmbpd)
            <!-- Verification Footer -->
            <div class="verification-footer">
                <table>
                    <tr>
                        <td style="width: 74%; padding-right: 15px; vertical-align: middle; line-height: 1.45;">
                            <strong style="color: #0f172a; font-size: 9px;">{{ __("Catatan Verifikasi") }}:</strong><br>
                            {{ __("Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe - PT Info Solusindo Data Utama.") }}
                            {{ __("Scan QR Code di samping untuk memverifikasi keaslian dokumen.") }}<br>
                            <span style="color: #1e293b; font-size: 8px;"><strong>ID Dokumen:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...</span>
                            @php
                                $tembusanUsers = collect();
                                try { $tembusanUsers = $document->getNotifyUsers(); } catch (\Throwable $e) {}
                            @endphp
                            @if($tembusanUsers->isNotEmpty())
                                <div style="margin-top: 3px; font-size: 8px; color: #475569;">
                                    <strong>{{ __('Tembusan (Mengetahui)') }}:</strong> {{ $tembusanUsers->map(fn($u) => format_acronyms($u->name) . ' (' . format_acronyms($u->position ?? $u->department ?? 'Staff') . ')')->implode(', ') }}
                                </div>
                            @endif
                        </td>
                        <td style="width: 26%; text-align: center; vertical-align: middle;">
                            @if(isset($qrCode))
                                <div style="display: inline-block; text-align: center;">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                                    <div style="font-size: 8px; font-weight: bold; color: #334155; margin-top: 3px;">{{ __("Scan untuk verifikasi") }}</div>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
            </div>
            @else
            <!-- Compact Single-Line Security Verification for UMBPD 1-page fit -->
            <div style="margin-top: 6px; font-size: 7.5px; color: #64748b; text-align: center; border-top: 1px dashed #cbd5e1; padding-top: 3px;">
                Dokumen elektronik resmi ConfirmMe | <strong>ID:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 24) }}... | PT Info Solusindo Data Utama
            </div>
            @endif
        </div>
    @endif
</body>

</html>
