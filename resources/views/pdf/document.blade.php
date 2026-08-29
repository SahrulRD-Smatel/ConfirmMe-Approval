<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }}</title>
    <style>
        @page {
            /* Margin atas yang besar (130px) adalah SATU-SATUNYA cara agar semua halaman konsisten di Dompdf */
            margin: 130px 50px 50px 50px !important;
        }

        header {
            position: fixed;
            top: -100px; /* Header ditarik 100px ke atas dari margin 130px */
            left: 0;
            right: 0;
            height: 80px;
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
            font-size: 12px;
            line-height: 1.5;
            color: #333;
        }

        .container {
            width: 100%;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #7F1D1D;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header-left {
            display: table-cell;
            width: 70%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 30%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 18px;
            font-weight: bold;
            color: #7F1D1D;
        }

        .company-tagline {
            font-size: 10px;
            color: #666;
        }

        .doc-number {
            font-size: 14px;
            font-weight: bold;
            color: #7F1D1D;
        }

        .doc-date {
            font-size: 10px;
            color: #666;
        }

        /* Title */
        .title {
            text-align: center;
            margin-bottom: 25px;
        }

        .title h1 {
            font-size: 16px;
            text-transform: uppercase;
            color: #7F1D1D;
            border-bottom: 2px solid #7F1D1D;
            display: inline-block;
            padding-bottom: 5px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .info-table th {
            text-align: left;
            width: 150px;
            padding: 10px;
            background-color: #f9fafb;
            color: #4b5563;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
        }

        .info-table td {
            padding: 10px;
            border: none;
            border-bottom: 1px solid #e5e7eb;
        }

        /* Data Table (Dynamic Fields) */
        .data-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
            table-layout: auto;
            page-break-inside: auto;
        }

        .data-table tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
        
        .data-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        .data-table th, .data-table td {
            padding: 10px 8px;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            font-size: 10px;
            word-wrap: break-word;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            text-align: left;
            background-color: #7F1D1D;
            color: white;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
        }

        /* Content */
        .section {
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #7F1D1D;
            margin-bottom: 12px;
            padding-top: 40px;
            padding-bottom: 6px;
            border-bottom: 2px solid #7F1D1D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .content-box {
            background-color: #f8fafc;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            page-break-inside: avoid;
        }

        /* Approval Timeline */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            page-break-inside: avoid;
        }

        .approval-table th {
            background-color: #7F1D1D;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 11px;
        }

        .approval-table td {
            padding: 10px;
            border: 1px solid #e2e8f0;
            font-size: 11px;
        }

        .approval-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .status-approved {
            color: #059669;
            font-weight: bold;
        }

        .status-rejected {
            color: #DC2626;
            font-weight: bold;
        }

        .status-pending {
            color: #D97706;
            font-weight: bold;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 70%;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            width: 30%;
            text-align: center;
            vertical-align: bottom;
        }

        .qr-code {
            text-align: center;
        }

        .qr-code img {
            width: 100px;
            height: 100px;
        }

        .qr-label {
            font-size: 8px;
            color: #666;
            margin-top: 5px;
        }

        .signature-box {
            border: 1px dashed #ccc;
            padding: 10px;
            text-align: center;
            margin-top: 10px;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
        }

        .verification-note {
            font-size: 9px;
            color: #666;
            margin-top: 20px;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        /* Page Break */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>

<body>
    <!-- Company Letterhead (Fixed - Repeated on every page) -->
    <header>
        @include('pdf.partials.company-header', ['accentColor' => '#7F1D1D'])
    </header>

    <div class="container">
        <!-- Document Number Bar -->
        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 50%; vertical-align: middle;"></div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: middle;">
                <div class="doc-number">{{ $document->document_number }}</div>
                <div class="doc-date">{{ $document->created_at->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            <h1>{{ $document->approval_type_label }}</h1>
        </div>

        <!-- Document Info -->
        <div class="section">
            <div class="section-title">{{ __("INFORMASI DOKUMEN") }}</div>
            <table class="info-table">
                <tr>
                    <th>{{ __("Nomor Dokumen") }}</th>
                    <td>{{ $document->document_number }}</td>
                </tr>
                <tr>
                    <th>{{ __("Judul") }}</th>
                    <td>{{ $document->title }}</td>
                </tr>
                <tr>
                    <th>{{ __("Jenis Approval") }}</th>
                    <td>{{ $document->approval_type_label }}</td>
                </tr>
                <tr>
                    <th>{{ __("Prioritas") }}</th>
                    <td>{{ $document->priority_label }}</td>
                </tr>
                <tr>
                    <th>{{ __("Departemen") }}</th>
                    <td>{{ $document->department ?? '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __("Pengaju") }}</th>
                    <td>{{ $document->user->name }} ({{ $document->user->employee_id ?? $document->user->email }})</td>
                </tr>
                <tr>
                    <th>{{ __("Tanggal Pengajuan") }}</th>
                    <td>{{ $document->submitted_at ? $document->submitted_at->format('d F Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <th>{{ __("Status") }}</th>
                    <td><strong>{{ strtoupper($document->status_label) }}</strong></td>
                </tr>
                @if($document->approval_type === 'loan' && isset($document->metadata['loan_amount']))
                    <tr>
                        <th>{{ __("Jumlah Pinjaman") }}</th>
                        <td><strong>Rp {{ number_format($document->metadata['loan_amount'], 0, ',', '.') }}</strong></td>
                    </tr>
                    @if(isset($document->metadata['installment_count']))
                        <tr>
                            <th>{{ __("Tenor Cicilan") }}</th>
                            <td>{{ $document->metadata['installment_count'] }} {{ __("bulan") }}</td>
                        </tr>
                    @endif
                @elseif($document->estimated_budget)
                    <tr>
                        <th>{{ __("Estimasi Anggaran") }}</th>
                        <td>Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($document->approved_budget)
                    <tr>
                        <th>{{ __("Anggaran Disetujui") }}</th>
                        <td><strong>Rp {{ number_format($document->approved_budget, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Description -->
        <div class="section">
            <div class="section-title">{{ __("DESKRIPSI / KETERANGAN") }}</div>
            <div class="content-box">
                {!! nl2br(e($document->description)) !!}
            </div>
        </div>
        <!-- Dynamic Fields -->
        @php
            $template = \App\Models\DocumentTemplate::where('code', $document->template)->first();
            $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
        @endphp

        @if(count($dynamicFields) > 0 && !empty($document->metadata))
            <div class="section">
                <div class="section-title">{{ __("INFORMASI SPESIFIK") }}</div>
                @foreach($dynamicFields as $field)
                    @php 
                        $fieldName = $field['name']; 
                        $fieldValue = $document->metadata[$fieldName] ?? null; 
                    @endphp
                    
                    @if($field['type'] === 'table')
                        <div style="page-break-before: always;"></div>
                        <div class="section-title">{{ strtoupper($field['label']) }}</div>
                        @if(is_array($fieldValue) && count($fieldValue) > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        @php $cols = array_map('trim', explode(',', $field['columns'])); @endphp
                                        @foreach($cols as $col)
                                            <th>{{ $col }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($fieldValue as $row)
                                        <tr>
                                            @foreach($cols as $col)
                                                <td>{{ $row[$col] ?? '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div style="font-style: italic; color: #666; margin-bottom: 15px; font-size: 10px;">{{ __("Tidak ada data") }}</div>
                        @endif
                    @elseif($field['type'] === 'textarea')
                        <div style="margin-top: 15px; margin-bottom: 5px; font-size: 11px; font-weight: bold;">{{ $field['label'] }}</div>
                        <div class="content-box" style="margin-bottom: 15px;">
                            {!! nl2br(e($fieldValue ?: '-')) !!}
                        </div>
                    @elseif($field['type'] === 'file' && $fieldValue)
                        <div style="margin-top: 15px; margin-bottom: 5px; font-size: 11px; font-weight: bold;">{{ $field['label'] }}</div>
                        <div style="margin-bottom: 15px;">
                            @if(in_array(strtolower(pathinfo($fieldValue, PATHINFO_EXTENSION)), ['jpg', 'jpeg', 'png']))
                                <img src="{{ storage_path('app/public/' . ltrim(str_replace('public/', '', $fieldValue), '/')) }}" alt="{{ $field['label'] }}" style="max-width: 300px; max-height: 300px; object-fit: contain; border: 1px solid #ccc; border-radius: 4px; padding: 3px;" />
                            @else
                                <em style="font-size: 10px; color: #555;">{{ __("Terdapat lampiran berkas") }} ({{ pathinfo($fieldValue, PATHINFO_EXTENSION) }}) - {{ __("silakan unduh melalui sistem.") }}</em>
                            @endif
                        </div>
                    @else
                        <div style="margin-bottom: 8px;">
                            <span style="display: inline-block; width: 150px; font-weight: bold; color: #555;">{{ $field['label'] }}:</span>
                            <span>
                                @if($fieldValue)
                                    @if($field['type'] === 'date')
                                        {{ \Carbon\Carbon::parse($fieldValue)->format('d F Y') }}
                                    @elseif($field['type'] === 'number')
                                        {{ is_numeric($fieldValue) ? number_format((float)$fieldValue, 0, ',', '.') : $fieldValue }}
                                    @else
                                        {{ $fieldValue }}
                                    @endif
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
        @php
            $terms = $template ? ($template->fields['terms_and_conditions'] ?? null) : null;
        @endphp
        @if($terms)
            <div class="section" style="page-break-inside: avoid;">
                <div class="section-title">{{ __("SYARAT & KETENTUAN") }}</div>
                <div style="font-size: 11px; color: #333; line-height: 1.6; text-align: justify; padding: 15px; background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 4px;">
                    <ol style="margin: 0; padding-left: 15px;">
                        @foreach(explode("\n", str_replace("\r", "", trim($terms))) as $item)
                            @if(trim($item) !== '')
                                <li style="margin-bottom: 8px;">{{ trim($item) }}</li>
                            @endif
                        @endforeach
                    </ol>
                </div>
            </div>
        @endif

        <!-- Approval Timeline -->
        @if(in_array($document->status, ['approved', 'completed', 'paid', 'presiden_review', 'director_review', 'finance_review']))
            <div class="section" style="page-break-before: always;">
                <div class="section-title">{{ __("RIWAYAT PERSETUJUAN (DIGITAL SIGNATURE)") }}</div>
                <table class="approval-table">
                    <thead>
                        <tr>
                            <th style="width: 25%">{{ __('Role') }}</th>
                            <th style="width: 30%">{{ __('Nama') }}</th>
                            <th style="width: 20%">{{ __('Status') }}</th>
                            <th style="width: 25%">{{ __('Waktu') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Requester (Pengaju) -->
                        <tr>
                            <td>{{ __('Pembuat Pengajuan') }}</td>
                            <td>{{ $document->user->name }}</td>
                            <td><span class="status-approved">{{ __('DIKIRIM') }}</span></td>
                            <td>
                                {{ $document->submitted_at ? $document->submitted_at->format('d M Y, H:i') : $document->created_at->format('d M Y, H:i') }}<br>
                                <span style="font-size: 8px; color: #888;">(Digital Sign)</span>
                            </td>
                        </tr>
                        <!-- Approvers -->
                        @foreach($document->approvals as $approval)
                            <tr>
                                <td>{{ $approval->approver?->position ?? $approval->role_label ?? 'Approver' }}</td>
                                <td>{{ $approval->approver->name }}</td>
                                <td>
                                    @if($approval->status === 'approved')
                                        <span class="status-approved">{{ __('DISETUJUI') }}</span>
                                    @elseif($approval->status === 'rejected')
                                        <span class="status-rejected">{{ __('DITOLAK') }}</span>
                                    @elseif($approval->status === 'pending')
                                        <span class="status-pending">{{ __('MENUNGGU') }}</span>
                                    @else
                                        <span>{{ strtoupper($approval->status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($approval->action_at)
                                        {{ $approval->action_at->format('d M Y, H:i') }}<br>
                                        <span style="font-size: 8px; color: #888;">(Digital Sign)</span>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <!-- Footer with QR Code -->
        <div class="footer">
            <div class="footer-left">
                <div class="verification-note">
                    <strong>{{ __("Catatan Verifikasi") }}:</strong><br>
                    {{ __("Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe.") }}
                    {{ __("Scan QR Code untuk memverifikasi keaslian dokumen ini.") }}<br>
                    <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...
                </div>
            </div>
            <div class="footer-right">
                <div class="qr-code">
                    @if(isset($qrCode))
                        @if(isset($qrFormat) && $qrFormat === 'svg')
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                        @else
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                        @endif
                    @endif
                    <div class="qr-label">{{ __("Scan untuk verifikasi") }}</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #999;">
            {{ __("Dicetak pada") }}: {{ now()->format('d F Y H:i:s') }} | ConfirmMe - PT. Info Solusindo Data Utama
        </div>
    </div>
</body>

</html>
