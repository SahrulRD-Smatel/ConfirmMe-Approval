<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }} - {{ $document->title }}</title>
    <style>
        @page {
            size: A4 {{ $orientation ?? 'portrait' }};
            margin: 8mm 10mm 8mm 10mm;
        }

        html, body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '9.5px' }};
            line-height: 1.3;
            color: #111;
            background: #fff;
        }

        table {
            border-collapse: collapse;
        }

        .w-full {
            width: 100%;
        }

        /* Header / Kop Surat Info Data */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .doc-title-main {
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '13px' : '14px' }};
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .doc-title-sub {
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '11px' : '11.5px' }};
            font-weight: bold;
            text-align: center;
            font-style: italic;
        }

        /* Metadata Tables */
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '9px' }};
        }

        .meta-table td {
            padding: 2px 0;
            vertical-align: middle;
        }

        .meta-label {
            width: {{ ($orientation ?? 'portrait') === 'landscape' ? '105px' : '120px' }};
            color: #222;
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

        /* Dynamic Non-Table Fields Section */
        .info-card {
            width: 100%;
            border: 1px solid #ddd;
            background-color: #fafafa;
            border-radius: 4px;
            margin-top: 6px;
            margin-bottom: 8px;
            padding: 6px 10px;
            page-break-inside: avoid;
        }

        .info-card-title {
            font-weight: bold;
            font-size: 10px;
            color: #7F1D1D;
            text-transform: uppercase;
            margin-bottom: 4px;
            border-bottom: 1px solid #eee;
            padding-bottom: 3px;
        }

        /* Data Tables (Dynamic Table Fields) */
        .data-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            margin-top: 6px;
            margin-bottom: 8px;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '9px' }};
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

        /* Terms and Conditions */
        .terms-box {
            border: 1px solid #ddd;
            background-color: #fcfcfc;
            padding: 8px 12px;
            margin-top: 6px;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .terms-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            margin-bottom: 4px;
        }

        /* Signatures Block (4 Corporate Boxes) */
        .sig-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .sig-box-cell {
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
            height: {{ ($orientation ?? 'portrait') === 'landscape' ? '50px' : '55px' }};
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

        /* Verification Footer */
        .verification-footer {
            margin-top: 12px;
            padding: 6px 10px;
            background-color: #f9fafb;
            border: 1px solid #e5e7eb;
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
    </style>
</head>

<body>
    @php
        $metadata = $document->metadata ?? [];
        $templateModel = null;
        if ($document->relationLoaded('documentTemplate')) {
            $templateModel = $document->documentTemplate;
        } elseif (is_object($document->template) && $document->template instanceof \App\Models\DocumentTemplate) {
            $templateModel = $document->template;
        } else {
            try {
                $templateModel = \App\Models\DocumentTemplate::where('code', (string)$document->template)->first();
            } catch (\Throwable $e) {
                $templateModel = null;
            }
        }
        $dynamicFields = $templateModel ? ($templateModel->fields['dynamic_fields'] ?? []) : [];

        // 1. Resolve Document Titles
        $mainTitle = $templateModel ? $templateModel->name : ($document->title ?: 'DOKUMEN PENGAJUAN');
        
        $subTitle = null;
        if (!empty($metadata['kategori_pembelian'])) {
            $subTitle = $metadata['kategori_pembelian'];
        } elseif ($templateModel && strtolower(trim($document->title)) !== strtolower(trim($templateModel->name))) {
            $subTitle = $document->title;
        }
        if ($subTitle) {
            $subTitle = ucwords(strtolower(trim($subTitle)));
        }

        // 2. Resolve Logo Info Data (Base64)
        $logoPath = public_path('images/logo-infodata.jpg');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        // 3. Resolve Dynamic Tables & Items
        $tableFields = [];
        $nonTableFields = [];
        foreach ($dynamicFields as $field) {
            if (($field['type'] ?? '') === 'table') {
                $tableFields[] = $field;
            } else {
                $nonTableFields[] = $field;
            }
        }

        // Check if metadata has default 'items' or PurchaseOrder items even without explicit table field
        $hasItemsData = !empty($metadata['items']) || ($document->purchaseOrder && $document->purchaseOrder->items);
        if (empty($tableFields) && $hasItemsData) {
            $tableFields[] = [
                'name' => 'items',
                'label' => 'Rincian Barang / Jasa',
                'columns' => 'Nama Barang, Keterangan, Max Perbulan, Saat ini, Permintaan, Harga Perk./Item, Jml harga perkiraan'
            ];
        }

        // 4. Approver & Signatures resolution (4-Box Corporate Grid)
        $approvals = $document->approvals ?? collect();
        $pemohon = $document->user;

        // Disetujui: First managerial approver
        $disetujuiApproval = $approvals->first(function($app) {
            $r = strtolower($app->approver_role ?? '');
            return !str_contains($r, 'finance') && !str_contains($r, 'keuangan') && !str_contains($r, 'ga');
        }) ?? $approvals->first();

        // Mengetahui: Observer / Pihak Mengetahui / GA / HR
        $notifyUsers = collect();
        try {
            $notifyUsers = $document->getNotifyUsers();
        } catch (\Throwable $e) {
            $notifyUsers = collect();
        }
        $mengetahuiUser = $notifyUsers->first();
        if (!$mengetahuiUser && $approvals->count() >= 2) {
            $mengetahuiApproval = $approvals->first(function($app) {
                $r = strtolower($app->approver_role ?? '');
                return str_contains($r, 'ga') || str_contains($r, 'hrga') || str_contains($r, 'observer');
            });
            $mengetahuiUser = $mengetahuiApproval?->approver;
        }

        // Bagian Keuangan: Finance approver or last step
        $financeApproval = $approvals->first(function($app) {
            $r = strtolower($app->approver_role ?? '');
            return str_contains($r, 'finance') || str_contains($r, 'keuangan');
        });
        if (!$financeApproval && $approvals->count() > 1) {
            $financeApproval = $approvals->last();
        }
        $financeUser = $financeApproval?->approver;

        $verificationUrl = $document->approval_hash ? route('verify.show', $document->approval_hash) : null;
        $isLandscape = ($orientation ?? 'portrait') === 'landscape';
    @endphp

    {{-- KOP HEADER --}}
    <table class="header-table">
        <tr>
            {{-- Kiri: Logo & Metadata Dokumen --}}
            <td style="width: {{ $isLandscape ? '37%' : '44%' }};">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" style="height: {{ $isLandscape ? '46px' : '42px' }}; width: auto; margin-bottom: 5px; display: block;" alt="Info Data" />
                @endif
                <table class="meta-table">
                    <tr>
                        <td class="meta-label">{{ __('No Permintaan') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $document->document_number }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ __('Tanggal') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ __('Dept/Divisi') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $document->department ?? ($document->user->department ?? '-') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ __('Keterangan/Projek') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $metadata['keterangan'] ?? ($document->description ?? $document->title) }}</td>
                    </tr>
                </table>
            </td>

            {{-- Tengah: Judul Dokumen --}}
            <td style="width: {{ $isLandscape ? '26%' : '20%' }}; text-align: center; padding-top: {{ $isLandscape ? '10px' : '6px' }};">
                <div class="doc-title-main">{{ $mainTitle }}</div>
                @if($subTitle)
                    <div class="doc-title-sub">"{{ $subTitle }}"</div>
                @endif
            </td>

            {{-- Kanan: Lokasi & Jenis Pembelian / Metadata Tambahan --}}
            <td style="width: {{ $isLandscape ? '37%' : '36%' }}; padding-left: 15px;">
                <table class="meta-table" style="margin-top: {{ $isLandscape ? '22px' : '15px' }};">
                    <tr>
                        <td class="meta-label" style="width: 130px;">{{ __('Lokasi Permintaan') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">{{ $metadata['lokasi_permintaan'] ?? ($metadata['lokasi'] ?? 'GMH') }}</td>
                    </tr>
                    <tr>
                        <td class="meta-label">{{ __('Perkiraan Tgl Penerimaan') }}</td>
                        <td class="meta-colon">:</td>
                        <td class="meta-value-line">
                            @php
                                $tglPenerimaan = $metadata['perkiraan_tgl_penerimaan'] ?? ($metadata['tanggal_dibutuhkan'] ?? null);
                            @endphp
                            {{ $tglPenerimaan ? (is_string($tglPenerimaan) && strtotime($tglPenerimaan) ? \Carbon\Carbon::parse($tglPenerimaan)->format('d F Y') : $tglPenerimaan) : ($document->created_at ? $document->created_at->format('d F Y') : now()->format('d F Y')) }}
                        </td>
                    </tr>
                    <tr>
                        <td class="meta-label" style="vertical-align: top;">{{ __('Jenis Pembelian') }}</td>
                        <td class="meta-colon" style="vertical-align: top;">:</td>
                        <td style="vertical-align: top; padding: 0;">
                            <table style="width: 100%; border-collapse: collapse; font-size: inherit;">
                                <tr>
                                    <td style="padding: 1px 0; width: 85px;">{{ __('Investasi') }}</td>
                                    <td style="padding: 1px 0; width: 8px;">:</td>
                                    <td style="padding: 1px 0;">Non Proyek/Proyek *)</td>
                                </tr>
                                <tr>
                                    <td style="padding: 1px 0;">{{ __('Non Investasi') }}</td>
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

    {{-- NON-TABLE DYNAMIC FIELDS (IF ANY) --}}
    @php
        $customNonTableFields = array_filter($nonTableFields, function($f) {
            return !in_array($f['name'], ['keterangan', 'lokasi_permintaan', 'lokasi', 'perkiraan_tgl_penerimaan', 'tanggal_dibutuhkan', 'kategori_pembelian']);
        });
    @endphp

    @if(count($customNonTableFields) > 0)
        <div class="info-card">
            <div class="info-card-title">{{ __('Informasi Tambahan') }}</div>
            <table class="meta-table">
                @foreach($customNonTableFields as $f)
                    @php
                        $val = $metadata[$f['name']] ?? null;
                    @endphp
                    @if($val !== null && $val !== '')
                        <tr>
                            <td class="meta-label" style="width: 160px; font-weight: bold;">{{ $f['label'] }}</td>
                            <td class="meta-colon">:</td>
                            <td>
                                @if($f['type'] === 'date' && strtotime($val))
                                    {{ \Carbon\Carbon::parse($val)->format('d F Y') }}
                                @elseif($f['type'] === 'number')
                                    {{ is_numeric($val) ? number_format((float)$val, 0, ',', '.') : $val }}
                                @elseif($f['type'] === 'textarea')
                                    {!! nl2br(e($val)) !!}
                                @elseif($f['type'] === 'file')
                                    @php
                                        $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                        @php
                                            $imgPath = storage_path('app/public/' . ltrim(str_replace('public/', '', $val), '/'));
                                            $imgData = file_exists($imgPath) ? 'data:image/' . $ext . ';base64,' . base64_encode(file_get_contents($imgPath)) : null;
                                        @endphp
                                        @if($imgData)
                                            <img src="{{ $imgData }}" style="max-height: 80px; border: 1px solid #ccc; margin-top: 4px;" alt="Lampiran" />
                                        @else
                                            <em>{{ __('Lampiran Foto') }} ({{ basename($val) }})</em>
                                        @endif
                                    @else
                                        <em>{{ __('Lampiran Berkas') }} ({{ basename($val) }})</em>
                                    @endif
                                @else
                                    {{ $val }}
                                @endif
                            </td>
                        </tr>
                    @endif
                @endforeach
            </table>
        </div>
    @endif

    {{-- DYNAMIC TABLES --}}
    @foreach($tableFields as $tField)
        @php
            $fieldName = $tField['name'];
            $tableData = $metadata[$fieldName] ?? [];
            if (empty($tableData) && $fieldName === 'items' && $document->purchaseOrder && $document->purchaseOrder->items) {
                $tableData = $document->purchaseOrder->items->map(function($poItem) {
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

            // Columns resolution
            $cols = !empty($tField['columns']) 
                ? array_filter(array_map('trim', explode(',', $tField['columns'])))
                : (!empty($tableData[0]) ? array_keys($tableData[0]) : []);

            // Check if table contains stock sub-headers (ATK/P3K standard)
            $colsLower = array_map('strtolower', $cols);
            $hasMax = in_array('max perbulan', $colsLower);
            $hasSaatIni = in_array('saat ini', $colsLower) || in_array('stock saat ini', $colsLower);
            $hasPermintaan = in_array('permintaan', $colsLower);
            $isStockLayout = $hasMax && $hasSaatIni && $hasPermintaan;

            $hasExpDate = in_array('exp date', $colsLower) || in_array('kadaluarsa', $colsLower);

            // Grand Total accumulator
            $grandTotal = 0;
            $hasCurrencyCol = false;
        @endphp

        @if(!empty($tableData) && count($tableData) > 0)
            <table class="data-table">
                <thead>
                    @if($isStockLayout)
                        {{-- 2-tier Corporate Stock Header --}}
                        <tr>
                            <th rowspan="2" style="width: {{ $hasExpDate ? '20%' : '26%' }};">{{ __('Nama Barang') }}</th>
                            <th rowspan="2" style="width: {{ $hasExpDate ? '17%' : '21%' }};">{{ __('Keterangan') }}</th>
                            @if($hasExpDate)
                                <th rowspan="2" style="width: 9%;">{{ __('Exp Date') }}</th>
                            @endif
                            <th colspan="3" style="width: 25%;">{{ __('STOCK') }}</th>
                            <th rowspan="2" style="width: 14%;">{{ __('Harga Perk./Item') }}</th>
                            <th rowspan="2" style="width: 14%;">{{ __('Jml harga perkiraan') }}</th>
                        </tr>
                        <tr>
                            <th style="width: 9%;">{{ __('Max Perbulan') }}</th>
                            <th style="width: 8%;">{{ __('Saat ini') }}</th>
                            <th style="width: 8%;">{{ __('Permintaan') }}</th>
                        </tr>
                    @else
                        {{-- Universal 1-tier Header for any custom columns --}}
                        <tr>
                            @foreach($cols as $col)
                                <th>{{ $col }}</th>
                            @endforeach
                        </tr>
                    @endif
                </thead>
                <tbody>
                    @php
                        $currentCategory = null;
                    @endphp

                    @foreach($tableData as $row)
                        @php
                            // Check if row is a category header row
                            $isCatRow = false;
                            $catTitle = '';
                            if (!empty($row['_category'])) {
                                $isCatRow = true;
                                $catTitle = $row['_category'];
                            } elseif (!empty($row['kategori']) && count(array_filter($row)) === 1) {
                                $isCatRow = true;
                                $catTitle = $row['kategori'];
                            } elseif (count($row) === 1 && !empty(reset($row))) {
                                $isCatRow = true;
                                $catTitle = reset($row);
                            }

                            // Dynamic category mapping for ATK / P3K / Pantry items
                            if (!$isCatRow && !empty($row['Nama Barang'])) {
                                $itemLower = strtolower(trim($row['Nama Barang']));
                                if ($currentCategory === null) {
                                    if (str_contains($itemLower, 'tolak angin') || str_contains($itemLower, 'sangobion') || str_contains($itemLower, 'bodrex') || str_contains($itemLower, 'obat')) {
                                        $currentCategory = 'Obat-obatan';
                                        echo '<tr class="category-row"><td colspan="' . (count($cols) + ($isStockLayout ? 1 : 0)) . '">' . $currentCategory . '</td></tr>';
                                    } elseif (str_contains($itemLower, 'double tape') || str_contains($itemLower, 'lakban') || str_contains($itemLower, 'tinta printer')) {
                                        $currentCategory = 'Lemari ATK No.1';
                                        echo '<tr class="category-row"><td colspan="' . (count($cols) + ($isStockLayout ? 1 : 0)) . '">' . $currentCategory . '</td></tr>';
                                    }
                                } elseif ($currentCategory === 'Lemari ATK No.1' && (str_contains($itemLower, 'paper bag finnace') || str_contains($itemLower, 'kertas a4') || str_contains($itemLower, 'amplop coklat'))) {
                                    $currentCategory = 'Lemari ATK No.2';
                                    echo '<tr class="category-row"><td colspan="' . (count($cols) + ($isStockLayout ? 1 : 0)) . '">' . $currentCategory . '</td></tr>';
                                }
                            }
                        @endphp

                        @if($isCatRow)
                            <tr class="category-row">
                                <td colspan="{{ count($cols) + ($isStockLayout ? 1 : 0) }}">{{ $catTitle }}</td>
                            </tr>
                        @else
                            <tr>
                                @if($isStockLayout)
                                    {{-- Render structured Stock row --}}
                                    @php
                                        $nama = $row['Nama Barang'] ?? ($row['nama_barang'] ?? ($row['item_name'] ?? '-'));
                                        $ket = $row['Keterangan'] ?? ($row['keterangan'] ?? ($row['description'] ?? '-'));
                                        $exp = $row['Exp Date'] ?? ($row['exp_date'] ?? ($row['Kadaluarsa'] ?? '-'));
                                        $max = $row['Max Perbulan'] ?? ($row['max_perbulan'] ?? '-');
                                        $saatIni = $row['Saat ini'] ?? ($row['saat_ini'] ?? ($row['Stock Saat Ini'] ?? '0'));
                                        $permintaan = $row['Permintaan'] ?? ($row['permintaan'] ?? ($row['quantity'] ?? '0'));
                                        
                                        $hargaRaw = $row['Harga Perk./Item'] ?? ($row['harga'] ?? ($row['unit_price'] ?? 0));
                                        $harga = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$hargaRaw));
                                        
                                        $totalRaw = $row['Jml harga perkiraan'] ?? ($row['total'] ?? ($row['total_price'] ?? ($harga * (float)$permintaan)));
                                        $total = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$totalRaw));
                                        
                                        $grandTotal += $total;
                                        $hasCurrencyCol = true;
                                    @endphp
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
                                @else
                                    {{-- Render generic dynamic row --}}
                                    @foreach($cols as $col)
                                        @php
                                            $val = $row[$col] ?? '-';
                                            $isPrice = preg_match('/harga|biaya|nominal|tarif|price|rp|total|subtotal/i', $col);
                                            $isQty = preg_match('/qty|jumlah|kuantitas|unit|pcs/i', $col);
                                            $isNum = is_numeric(str_replace(['.', ','], '', (string)$val));

                                            if ($isPrice && $isNum) {
                                                $numericVal = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$val));
                                                if (preg_match('/total|jml/i', $col)) {
                                                    $grandTotal += $numericVal;
                                                    $hasCurrencyCol = true;
                                                }
                                            }
                                        @endphp
                                        <td class="{{ $isPrice ? 'text-right' : ($isQty ? 'text-center' : 'text-left') }}">
                                            @if($isPrice && $isNum)
                                                <table style="width: 100%; border-collapse: collapse; border: none;">
                                                    <tr>
                                                        <td style="border: none; padding: 0; text-align: left; width: 18px;">Rp</td>
                                                        <td style="border: none; padding: 0; text-align: right;">{{ number_format((float)preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$val)), 0, ',', '.') }}</td>
                                                    </tr>
                                                </table>
                                            @else
                                                {{ $val }}
                                            @endif
                                        </td>
                                    @endforeach
                                @endif
                            </tr>
                        @endif
                    @endforeach

                    {{-- Grand Total Row with golden-yellow background --}}
                    @if($hasCurrencyCol || $grandTotal > 0)
                        <tr class="total-row">
                            @if($isStockLayout)
                                <td colspan="{{ $hasExpDate ? 6 : 5 }}" style="border: none;"></td>
                                <td class="bg-yellow text-center" style="font-weight: bold; border: 1px solid #000;">Rp</td>
                                <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                    {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            @else
                                <td colspan="{{ count($cols) - 1 }}" class="text-right" style="font-weight: bold; padding: 4px 6px;">{{ __('TOTAL') }}</td>
                                <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                    Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                </td>
                            @endif
                        </tr>
                    @endif
                </tbody>
            </table>
        @endif
    @endforeach

    {{-- TERMS & CONDITIONS (IF ANY) --}}
    @php
        $terms = $templateModel ? ($templateModel->fields['terms_and_conditions'] ?? null) : null;
    @endphp
    @if($terms)
        <div class="terms-box">
            <div class="terms-title">{{ __('Syarat & Ketentuan') }}</div>
            <ol style="margin: 0; padding-left: 15px; font-size: {{ $isLandscape ? '8px' : '8.5px' }}; line-height: 1.4;">
                @foreach(explode("\n", str_replace("\r", "", trim($terms))) as $termItem)
                    @if(trim($termItem) !== '')
                        <li style="margin-bottom: 2px;">{{ trim($termItem) }}</li>
                    @endif
                @endforeach
            </ol>
        </div>
    @endif

    {{-- 4-BOX CORPORATE SIGNATURE GRID --}}
    <table class="sig-container">
        <tr>
            {{-- Kotak 1: Pemohon --}}
            <td class="sig-box-cell" style="width: 25%;">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">{{ __('Pemohon') }}</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($verificationUrl)
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" alt="QR" />
                            @else
                                <div style="font-size: 8px; color: #888;">{{ __('Diajukan') }}</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            <div>{{ $pemohon->name ?? '-' }}</div>
                            <div style="font-size: 7.5px; font-weight: normal; color: #555;">
                                {{ $document->submitted_at ? $document->submitted_at->format('d/m/Y') : ($document->created_at ? $document->created_at->format('d/m/Y') : '') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 2: Disetujui (Atasan / Manager) --}}
            <td class="sig-box-cell" style="width: 25%;">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">{{ __('Disetujui') }}</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($disetujuiApproval && $disetujuiApproval->status === 'approved' && $verificationUrl)
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" alt="QR" />
                            @elseif($disetujuiApproval && $disetujuiApproval->status === 'approved')
                                <div style="font-size: 8.5px; font-weight: bold; color: #059669;">APPROVED</div>
                            @else
                                <div style="font-size: 8px; color: #aaa; border: 1px dashed #ccc; width: 44px; height: 44px; margin: 0 auto; line-height: 44px;">Pending</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            <div>{{ $disetujuiApproval?->approver?->name ?? '-' }}</div>
                            <div style="font-size: 7.5px; font-weight: normal; color: #555;">
                                {{ $disetujuiApproval?->action_at ? $disetujuiApproval->action_at->format('d/m/Y') : '' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 3: Mengetahui (Pihak Mengetahui / Observer / GA / HR) --}}
            <td class="sig-box-cell" style="width: 25%;">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">{{ __('Mengetahui') }}</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($mengetahuiUser && $document->status === 'completed' && $verificationUrl)
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" alt="QR" />
                            @elseif($mengetahuiUser)
                                <div style="font-size: 8px; color: #666; padding-top: 15px;">{{ __('Mengetahui') }}</div>
                            @else
                                <div style="height: 44px;"></div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            <div>{{ $mengetahuiUser->name ?? '-' }}</div>
                            <div style="font-size: 7.5px; font-weight: normal; color: #555;">
                                {{ $document->completed_at ? $document->completed_at->format('d/m/Y') : '' }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>

            {{-- Kotak 4: Bagian Keuangan (Finance / Final Approver) --}}
            <td class="sig-box-cell" style="width: 25%;">
                <table class="sig-card">
                    <tr>
                        <td class="sig-card-header">{{ __('Bagian Keuangan') }}</td>
                    </tr>
                    <tr>
                        <td class="sig-card-body">
                            @if($financeApproval && $financeApproval->status === 'approved' && $verificationUrl)
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 44px; height: 44px; display: inline-block;" alt="QR" />
                            @elseif($financeUser && in_array($document->status, ['completed', 'approved', 'paid']))
                                <div style="font-size: 8.5px; font-weight: bold; color: #059669;">VERIFIED</div>
                            @else
                                <div style="font-size: 8px; color: #aaa; border: 1px dashed #ccc; width: 44px; height: 44px; margin: 0 auto; line-height: 44px;">Pending</div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="sig-card-footer">
                            <div>{{ $financeUser->name ?? '-' }}</div>
                            <div style="font-size: 7.5px; font-weight: normal; color: #555;">
                                {{ $financeApproval?->action_at ? $financeApproval->action_at->format('d/m/Y') : ($document->completed_at ? $document->completed_at->format('d/m/Y') : '') }}
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ELECTRONIC VERIFICATION FOOTER --}}
    <div class="verification-footer">
        <table>
            <tr>
                <td style="width: 80%;">
                    <strong>{{ __('Catatan Keaslian Dokumen') }}:</strong> {{ __('Dokumen ini diterbitkan secara elektronik oleh ConfirmMe (PT. Info Solusindo Data Utama). QR Code di atas berfungsi sebagai tanda tangan digital sah dan dapat dipindai untuk memverifikasi keabsahan dokumen.') }}
                    <br>
                    <strong>ID Dokumen:</strong> {{ $document->document_number }} | <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}... | <strong>Dicetak:</strong> {{ now()->format('d/m/Y H:i:s') }}
                </td>
                <td style="width: 20%; text-align: right;">
                    <div style="font-size: 8px; font-weight: bold; color: #7F1D1D;">CONFIRMME VERIFIED</div>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>
