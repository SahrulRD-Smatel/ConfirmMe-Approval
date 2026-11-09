<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }} - {{ $document->title }}</title>
    <style>
        @page {
            size: A4 {{ $orientation ?? 'portrait' }};
            margin: 14mm 14mm 16mm 14mm;
        }

        html, body {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'DejaVu Sans', Arial, Helvetica, sans-serif;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '9px' }};
            line-height: 1.35;
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
            margin-bottom: 12px;
        }

        .header-table td {
            vertical-align: top;
            padding: 0;
        }

        .doc-title-main {
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '13px' : '13.5px' }};
            font-weight: bold;
            text-align: center;
            letter-spacing: 0.5px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .doc-title-sub {
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '10.5px' : '11px' }};
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
            width: {{ ($orientation ?? 'portrait') === 'landscape' ? '110px' : '120px' }};
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

        /* Section Titles */
        .section-header {
            font-size: 9.5px;
            font-weight: bold;
            color: #1f2937;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
            margin-bottom: 5px;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #e5e7eb;
        }

        /* Dynamic Non-Table Fields Card */
        .info-card {
            width: 100%;
            border: 1px solid #e5e7eb;
            background-color: #fafafa;
            border-radius: 4px;
            margin-top: 8px;
            margin-bottom: 10px;
            padding: 6px 10px;
            page-break-inside: avoid;
        }

        /* Data Tables (Dynamic Table Fields) */
        .data-table-container {
            margin-top: 10px;
            margin-bottom: 12px;
            page-break-inside: auto;
        }

        .data-table {
            width: 100%;
            border: 1px solid #000;
            border-collapse: collapse;
            font-size: {{ ($orientation ?? 'portrait') === 'landscape' ? '8.5px' : '9px' }};
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
            border: 1px solid #000;
            padding: 4px 3px;
            text-align: center;
            font-weight: bold;
            background-color: #f3f4f6;
            vertical-align: middle;
        }

        .data-table td {
            border: 1px solid #000;
            padding: 3.5px 5px;
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

        /* Dynamic File / Image Attachments */
        .attachment-section {
            margin-top: 14px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        .attachment-card {
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
            padding: 6px;
            border-radius: 6px;
            text-align: center;
            display: inline-block;
            margin: 4px;
        }

        .attachment-card-title {
            font-size: 8.5px;
            font-weight: bold;
            color: #374151;
            margin-bottom: 4px;
            text-transform: uppercase;
        }

        .attachment-card img {
            max-width: {{ ($orientation ?? 'portrait') === 'landscape' ? '280px' : '220px' }};
            max-height: 160px;
            border: 1px solid #ccc;
            border-radius: 4px;
            display: block;
            margin: 0 auto;
        }

        .attachment-list-box {
            border: 1px solid #e5e7eb;
            background-color: #f9fafb;
            padding: 8px 12px;
            border-radius: 4px;
            margin-top: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }

        /* Digital Approval Block (Exact Match with Image 1) */
        .approval-block-wrapper {
            margin-top: 16px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .approval-table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #e5e7eb;
            background: #fff;
        }

        .approval-table th, .approval-table td {
            border: 1px solid #e5e7eb;
        }

        .badge-created {
            background-color: #d1fae5;
            color: #065f46;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-reviewed {
            background-color: #f3f4f6;
            color: #374151;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-approved {
            background-color: #dcfce7;
            color: #15803d;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .badge-pending {
            background-color: #fef3c7;
            color: #92400e;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8px;
            font-weight: bold;
        }

        .created-circle {
            width: 24px;
            height: 24px;
            background: #d1fae5;
            color: #059669;
            border-radius: 50%;
            display: inline-block;
            line-height: 24px;
            font-size: 14px;
            font-weight: bold;
            margin-top: 4px;
        }

        .signed-label {
            font-size: 7.5px;
            color: #059669;
            font-weight: bold;
            margin-top: 2px;
        }

        /* Verified Banner (Green Box) */
        .verified-banner {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
            background-color: #ecfdf5;
            border: 1px solid #a7f3d0;
            border-radius: 6px;
            page-break-inside: avoid;
        }

        .verified-banner td {
            border: none;
        }

        /* Utility */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
    </style>
</head>

<body>
    @php
        $metadata = $document->metadata ?? [];
        
        // 1. Resolve Document Template
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

        // 2. Resolve Titles
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

        // 3. Resolve Logo Info Data (Base64)
        $logoPath = public_path('images/logo-infodata.jpg');
        if (!file_exists($logoPath)) {
            $logoPath = public_path('images/logo.png');
        }
        $logoDataUri = file_exists($logoPath)
            ? 'data:image/' . pathinfo($logoPath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($logoPath))
            : null;

        // 4. Resolve Dynamic Tables (Data Penumpang, Items, Jadwal, dll.)
        $tableList = [];
        $registeredTableKeys = [];

        foreach ($dynamicFields as $df) {
            if (($df['type'] ?? '') === 'table') {
                $fName = $df['name'];
                $registeredTableKeys[] = $fName;
                $tableList[$fName] = [
                    'label' => $df['label'] ?? ucwords(str_replace('_', ' ', $fName)),
                    'columns' => !empty($df['columns']) ? array_filter(array_map('trim', explode(',', $df['columns']))) : [],
                    'rows' => $metadata[$fName] ?? []
                ];
            }
        }

        // Also check if metadata has tabular data not defined in dynamicFields (e.g. 'items', 'data_penumpang')
        foreach ($metadata as $mKey => $mVal) {
            if (is_array($mVal) && !empty($mVal) && is_array(reset($mVal)) && !in_array($mKey, $registeredTableKeys)) {
                $firstRow = reset($mVal);
                $tableList[$mKey] = [
                    'label' => ucwords(str_replace(['_', '-'], ' ', $mKey)),
                    'columns' => array_keys($firstRow),
                    'rows' => $mVal
                ];
            }
        }

        // Fallback for PO items
        if (empty($tableList['items']) && $document->purchaseOrder && $document->purchaseOrder->items) {
            $tableList['items'] = [
                'label' => 'Rincian Barang / Jasa',
                'columns' => ['Nama Barang', 'Keterangan', 'Max Perbulan', 'Saat ini', 'Permintaan', 'Harga Perk./Item', 'Jml harga perkiraan'],
                'rows' => $document->purchaseOrder->items->map(function($poItem) {
                    return [
                        'Nama Barang' => $poItem->item_name,
                        'Keterangan' => $poItem->description,
                        'Max Perbulan' => '-',
                        'Saat ini' => '0',
                        'Permintaan' => $poItem->quantity,
                        'Harga Perk./Item' => $poItem->unit_price,
                        'Jml harga perkiraan' => $poItem->total_price,
                    ];
                })->toArray()
            ];
        }

        // 5. Resolve Dynamic Files / Lampiran SIM
        $imageAttachments = [];
        $fileAttachments = [];
        $fileFieldNames = [];

        foreach ($dynamicFields as $df) {
            if (($df['type'] ?? '') === 'file') {
                $fileFieldNames[] = $df['name'];
                $val = $metadata[$df['name']] ?? null;
                if ($val && is_string($val)) {
                    $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                    $label = $df['label'] ?? ucwords(str_replace('_', ' ', $df['name']));
                    
                    $relPath = ltrim(str_replace('public/', '', $val), '/');
                    $localPath = storage_path('app/public/' . $relPath);
                    if (!file_exists($localPath)) {
                        $localPath = public_path('storage/' . $relPath);
                    }

                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && file_exists($localPath)) {
                        $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                        $dataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
                        $imageAttachments[] = [
                            'label' => $label,
                            'uri' => $dataUri,
                            'filename' => basename($val)
                        ];
                    } else {
                        $fileAttachments[] = [
                            'label' => $label,
                            'filename' => basename($val),
                            'ext' => $ext
                        ];
                    }
                }
            }
        }

        // Also check general attachments
        if ($document->relationLoaded('attachments') || $document->attachments) {
            foreach ($document->attachments as $att) {
                $ext = strtolower(pathinfo($att->filename, PATHINFO_EXTENSION));
                $localPath = storage_path('app/public/' . ltrim($att->file_path, '/'));
                if (!file_exists($localPath)) {
                    $localPath = public_path('storage/' . ltrim($att->file_path, '/'));
                }

                if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']) && file_exists($localPath)) {
                    $mime = ($ext === 'png') ? 'image/png' : 'image/jpeg';
                    $dataUri = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($localPath));
                    $imageAttachments[] = [
                        'label' => $att->original_filename ?? $att->filename,
                        'uri' => $dataUri,
                        'filename' => $att->filename
                    ];
                } else {
                    $fileAttachments[] = [
                        'label' => 'Lampiran Dokumen',
                        'filename' => $att->original_filename ?? $att->filename,
                        'ext' => $ext
                    ];
                }
            }
        }

        // 6. Non-table, non-file dynamic fields
        $customNonTableFields = [];
        foreach ($dynamicFields as $df) {
            if (!in_array($df['type'] ?? '', ['table', 'file']) && !in_array($df['name'], ['keterangan', 'lokasi_permintaan', 'lokasi', 'perkiraan_tgl_penerimaan', 'tanggal_dibutuhkan', 'kategori_pembelian'])) {
                $customNonTableFields[] = $df;
            }
        }

        // 7. Resolve Approvals & Digital Approval Block (Image 1)
        $approvals = $document->approvals ?? collect();
        $requester = $document->user;
        $requesterTitle = $requester?->position ?? 'Staff';

        $approvalCols = [];
        // Step 1: Requester (Created)
        $approvalCols[] = [
            'is_requester' => true,
            'badge' => 'Created',
            'badge_class' => 'badge-created',
            'sub_label' => 'Submitted by:',
            'name' => $requester->name ?? 'Staff',
            'title' => $requesterTitle,
            'date' => $document->submitted_at ?? $document->created_at,
            'ip' => $document->metadata['submitted_ip'] ?? null,
            'status' => 'approved',
        ];

        // Steps 2..N: Approvers
        $totalApps = count($approvals);
        $aIdx = 0;
        foreach ($approvals as $app) {
            $aIdx++;
            $isLastStep = ($aIdx === $totalApps);
            $badge = $isLastStep ? 'Approved' : 'Reviewed';
            if ($app->status === 'rejected') {
                $badge = 'Rejected';
            } elseif ($app->status !== 'approved') {
                $badge = 'Pending';
            }

            $approvalCols[] = [
                'is_requester' => false,
                'badge' => $badge,
                'badge_class' => ($app->status === 'approved') ? ($isLastStep ? 'badge-approved' : 'badge-reviewed') : 'badge-pending',
                'sub_label' => ($app->status === 'approved') ? 'Digitally Signed by:' : 'Awaiting:',
                'name' => $app->approver?->name ?? ($app->role_label ?? 'Approver'),
                'title' => $app->approver?->position ?? ($app->role_label ?? 'Approver'),
                'date' => $app->action_at,
                'ip' => $app->ip_address,
                'status' => $app->status,
            ];
        }

        $numCols = count($approvalCols);
        $colWidth = round(100 / max($numCols, 1), 2);

        $verificationUrl = $document->approval_hash ? route('verify.show', $document->approval_hash) : null;
        $isLandscape = ($orientation ?? 'portrait') === 'landscape';
    @endphp

    {{-- KOP HEADER RESMI INFO DATA --}}
    <table class="header-table">
        <tr>
            {{-- Kiri: Logo & Metadata Dokumen --}}
            <td style="width: {{ $isLandscape ? '37%' : '44%' }};">
                @if($logoDataUri)
                    <img src="{{ $logoDataUri }}" style="height: {{ $isLandscape ? '44px' : '40px' }}; width: auto; margin-bottom: 5px; display: block;" alt="Info Data" />
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
            <td style="width: {{ $isLandscape ? '26%' : '20%' }}; text-align: center; padding-top: {{ $isLandscape ? '8px' : '4px' }};">
                <div class="doc-title-main">{{ $mainTitle }}</div>
                @if($subTitle)
                    <div class="doc-title-sub">"{{ $subTitle }}"</div>
                @endif
            </td>

            {{-- Kanan: Lokasi & Jenis Pembelian / Metadata Tambahan --}}
            <td style="width: {{ $isLandscape ? '37%' : '36%' }}; padding-left: 15px;">
                <table class="meta-table" style="margin-top: {{ $isLandscape ? '18px' : '12px' }};">
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

    {{-- INFORMASI SPESIFIK / FIELD NON-TABEL --}}
    @if(count($customNonTableFields) > 0)
        <div class="info-card">
            <div style="font-size: 8.5px; font-weight: bold; color: #7F1D1D; text-transform: uppercase; margin-bottom: 3px;">
                {{ __('Informasi Tambahan') }}
            </div>
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

    {{-- DYNAMIC TABLES (DATA PENUMPANG, ITEMS, JADWAL, DLL) --}}
    @foreach($tableList as $tblKey => $tbl)
        @php
            $tRows = $tbl['rows'] ?? [];
            $tCols = $tbl['columns'] ?? [];
            if (empty($tCols) && !empty($tRows[0])) {
                $tCols = array_keys($tRows[0]);
            }

            // Check if stock layout
            $colsLower = array_map('strtolower', $tCols);
            $hasMax = in_array('max perbulan', $colsLower);
            $hasSaatIni = in_array('saat ini', $colsLower) || in_array('stock saat ini', $colsLower);
            $hasPermintaan = in_array('permintaan', $colsLower);
            $isStockLayout = $hasMax && $hasSaatIni && $hasPermintaan;
            $hasExpDate = in_array('exp date', $colsLower) || in_array('kadaluarsa', $colsLower);

            $grandTotal = 0;
            $hasCurrencyCol = false;
        @endphp

        @if(!empty($tRows) && count($tRows) > 0)
            <div class="data-table-container">
                <div class="section-header">
                    {{ strtoupper($tbl['label']) }}
                </div>
                <table class="data-table">
                    <thead>
                        @if($isStockLayout)
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
                            <tr>
                                <th style="width: 35px;">No</th>
                                @foreach($tCols as $col)
                                    <th>{{ $col }}</th>
                                @endforeach
                            </tr>
                        @endif
                    </thead>
                    <tbody>
                        @php $rNum = 1; @endphp
                        @foreach($tRows as $row)
                            @php
                                $isCatRow = false;
                                $catTitle = '';
                                if (!empty($row['_category'])) {
                                    $isCatRow = true;
                                    $catTitle = $row['_category'];
                                } elseif (!empty($row['kategori']) && count(array_filter($row)) === 1) {
                                    $isCatRow = true;
                                    $catTitle = $row['kategori'];
                                }
                            @endphp

                            @if($isCatRow)
                                <tr class="category-row">
                                    <td colspan="{{ count($tCols) + ($isStockLayout ? 1 : 1) }}">{{ $catTitle }}</td>
                                </tr>
                            @else
                                <tr>
                                    @if($isStockLayout)
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
                                        <td class="text-center" style="width: 35px; color: #6b7280;">{{ $rNum++ }}</td>
                                        @foreach($tCols as $col)
                                            @php
                                                // Case-insensitive key lookup in row
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

                                                $isPrice = preg_match('/harga|biaya|nominal|tarif|price|rp|total|subtotal/i', $col);
                                                $isQty = preg_match('/qty|jumlah|kuantitas|unit|pcs/i', $col);
                                                $isNum = is_numeric(str_replace(['.', ','], '', (string)$cellVal));

                                                if ($isPrice && $isNum) {
                                                    $numericVal = (float) preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$cellVal));
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
                                                            <td style="border: none; padding: 0; text-align: right;">{{ number_format((float)preg_replace('/[^\d.]/', '', str_replace(',', '.', (string)$cellVal)), 0, ',', '.') }}</td>
                                                        </tr>
                                                    </table>
                                                @else
                                                    {{ $cellVal }}
                                                @endif
                                            </td>
                                        @endforeach
                                    @endif
                                </tr>
                            @endif
                        @endforeach

                        {{-- Total Row if applicable --}}
                        @if($hasCurrencyCol || $grandTotal > 0)
                            <tr class="total-row">
                                @if($isStockLayout)
                                    <td colspan="{{ $hasExpDate ? 6 : 5 }}" style="border: none;"></td>
                                    <td class="bg-yellow text-center" style="font-weight: bold; border: 1px solid #000;">Rp</td>
                                    <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                        {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                @else
                                    <td colspan="{{ count($tCols) }}" class="text-right" style="font-weight: bold; padding: 4px 6px;">{{ __('TOTAL') }}</td>
                                    <td class="bg-yellow text-right" style="font-weight: bold; border: 1px solid #000; padding: 3px 6px;">
                                        Rp {{ number_format($grandTotal, 0, ',', '.') }}
                                    </td>
                                @endif
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        @endif
    @endforeach

    {{-- LAMPIRAN BERKAS / FOTO (SIM, FOTO BARANG, BUKTI PENDUKUNG) --}}
    @if(count($imageAttachments) > 0)
        <div class="attachment-section">
            <div class="section-header">
                {{ __('LAMPIRAN BERKAS / FOTO') }}
            </div>
            <div style="text-align: left;">
                @foreach($imageAttachments as $img)
                    <div class="attachment-card">
                        <div class="attachment-card-title">{{ $img['label'] }}</div>
                        <img src="{{ $img['uri'] }}" alt="{{ $img['label'] }}" />
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- DAFTAR DOKUMEN LAMPIRAN LAINNYA --}}
    @if(count($fileAttachments) > 0)
        <div class="attachment-list-box">
            <div style="font-size: 8.5px; font-weight: bold; color: #374151; margin-bottom: 3px;">
                {{ __('Dokumen Lampiran Tambahan:') }}
            </div>
            <ol style="margin: 0; padding-left: 18px; font-size: 8px; color: #4b5563;">
                @foreach($fileAttachments as $f)
                    <li style="margin-bottom: 2px;">
                        <strong>{{ $f['filename'] }}</strong> ({{ strtoupper($f['ext']) }}) — <em>{{ $f['label'] }}</em>
                    </li>
                @endforeach
            </ol>
        </div>
    @endif

    {{-- SYARAT & KETENTUAN (JIKA ADA) --}}
    @php
        $terms = $templateModel ? ($templateModel->fields['terms_and_conditions'] ?? null) : null;
    @endphp
    @if($terms)
        <div style="border: 1px solid #e5e7eb; background-color: #fafafa; padding: 6px 10px; margin-top: 10px; margin-bottom: 12px; page-break-inside: avoid;">
            <div style="font-size: 8.5px; font-weight: bold; color: #374151; text-transform: uppercase; margin-bottom: 3px;">
                {{ __('Syarat & Ketentuan') }}
            </div>
            <ol style="margin: 0; padding-left: 15px; font-size: 8px; line-height: 1.4;">
                @foreach(explode("\n", str_replace("\r", "", trim($terms))) as $termItem)
                    @if(trim($termItem) !== '')
                        <li style="margin-bottom: 2px;">{{ trim($termItem) }}</li>
                    @endif
                @endforeach
            </ol>
        </div>
    @endif

    {{-- DIGITAL APPROVAL BLOCK (MATCHING IMAGE 1 RESMI SISTEM CONFIRMME) --}}
    <div class="approval-block-wrapper">
        <div class="section-header" style="border-bottom: none; margin-bottom: 4px;">
            &#128737; {{ __('Digital Approval Block') }}
        </div>

        <table class="approval-table">
            {{-- Baris 1: Status Badge --}}
            <tr>
                @foreach($approvalCols as $c)
                    <th style="width: {{ $colWidth }}%; text-align: center; padding: 4px 2px; background: #f9fafb;">
                        <span class="{{ $c['badge_class'] }}">{{ $c['badge'] }}</span>
                    </th>
                @endforeach
            </tr>

            {{-- Baris 2: QR Code / Checkmark Circle --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; vertical-align: middle; padding: 5px 2px; height: 58px;">
                        @if($c['is_requester'])
                            <div class="created-circle">&#10003;</div>
                            <div class="signed-label" style="color: #059669;">Created</div>
                        @elseif($c['status'] === 'approved' && $verificationUrl)
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 42px; height: 42px; display: inline-block;" alt="QR" />
                            <div class="signed-label">Signed</div>
                        @elseif($c['status'] === 'approved')
                            <div style="font-size: 8px; font-weight: bold; color: #059669; padding: 15px 0;">APPROVED</div>
                        @else
                            <div style="font-size: 8px; color: #9ca3af; border: 1px dashed #d1d5db; width: 42px; height: 42px; line-height: 42px; margin: 0 auto; border-radius: 4px;">Pending</div>
                        @endif
                    </td>
                @endforeach
            </tr>

            {{-- Baris 3: Sub-label --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; padding: 2px; font-size: 6.5px; color: #6b7280; font-style: italic; background: #fafafa;">
                        {{ $c['sub_label'] }}
                    </td>
                @endforeach
            </tr>

            {{-- Baris 4: Nama Lengkap (Bold, Underlined) --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; padding: 3px 2px; font-size: 7.5px; font-weight: bold; color: #111;">
                        <span style="text-decoration: underline;">{{ $c['name'] }}</span>
                    </td>
                @endforeach
            </tr>

            {{-- Baris 5: Jabatan / Posisi --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; padding: 2px; font-size: 7px; color: #4b5563;">
                        {{ $c['title'] }}
                    </td>
                @endforeach
            </tr>

            {{-- Baris 6: Tanggal & Jam (WIB) --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; padding: 2px; font-size: 6.5px; color: #6b7280;">
                        @if($c['date'])
                            <div>{{ \Carbon\Carbon::parse($c['date'])->format('d/m/Y') }}</div>
                            <div>{{ \Carbon\Carbon::parse($c['date'])->format('H:i') }} WIB</div>
                        @else
                            -
                        @endif
                    </td>
                @endforeach
            </tr>

            {{-- Baris 7: IP Address --}}
            <tr>
                @foreach($approvalCols as $c)
                    <td style="text-align: center; padding: 2px; font-size: 6.5px; color: #9ca3af;">
                        @if(!empty($c['ip']))
                            IP: {{ $c['ip'] }}
                        @else
                            &nbsp;
                        @endif
                    </td>
                @endforeach
            </tr>
        </table>

        {{-- Green Verification Box (Official Document - Digitally Verified) --}}
        @if($verificationUrl)
            <table class="verified-banner">
                <tr>
                    <td style="width: 48px; text-align: center; vertical-align: middle; padding: 5px;">
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" style="width: 42px; height: 42px;" alt="QR" />
                    </td>
                    <td style="vertical-align: middle; padding: 4px 8px;">
                        <div style="font-size: 8.5px; font-weight: bold; color: #065f46;">
                            &#128737; Official Document - Digitally Verified
                        </div>
                        <div style="font-size: 7.5px; color: #047857; margin-top: 1px;">
                            Scan QR Code to verify document authenticity.
                        </div>
                        <div style="font-size: 7px; color: #059669; word-break: break-all; margin-top: 1px;">
                            {{ $verificationUrl }}
                        </div>
                    </td>
                </tr>
            </table>
        @endif
    </div>
</body>

</html>
