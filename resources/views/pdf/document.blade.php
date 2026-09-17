<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }}</title>
    <style>
        @page {
            margin: 130px 50px 60px 50px !important;
        }

        header {
            position: fixed;
            top: -100px;
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

        /* Sub-Header Banner */
        .sub-header-banner {
            background-color: #1e3a5f;
            color: white;
            text-align: center;
            padding: 8px 15px;
            margin-bottom: 5px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .doc-title-banner {
            text-align: center;
            padding: 8px 15px;
            margin-bottom: 20px;
            font-size: 15px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1a1a1a;
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
            padding: 8px 10px;
            background-color: #f9fafb;
            color: #4b5563;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            font-weight: 600;
            font-size: 11px;
        }

        .info-table td {
            padding: 8px 10px;
            border: none;
            border-bottom: 1px solid #e5e7eb;
            font-size: 11px;
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
            padding-top: 15px;
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

        /* Signature Block */
        .signature-section {
            margin-top: 30px;
            page-break-inside: avoid;
        }

        .signature-table {
            width: 100%;
            border-collapse: collapse;
        }

        .signature-table td {
            text-align: center;
            vertical-align: top;
            padding: 5px 8px;
        }

        .sig-role-label {
            font-size: 10px;
            font-weight: bold;
            color: #333;
            margin-bottom: 8px;
        }

        .sig-qr-box {
            width: 70px;
            height: 70px;
            margin: 8px auto;
            display: block;
        }

        .sig-qr-box img {
            width: 70px;
            height: 70px;
        }

        .sig-pending-box {
            width: 70px;
            height: 70px;
            margin: 8px auto;
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
            border-bottom: 1px solid #333;
            display: inline-block;
            padding-bottom: 2px;
            margin-bottom: 3px;
        }

        .sig-nip {
            font-size: 8px;
            color: #555;
        }

        .sig-date {
            font-size: 8px;
            color: #888;
            margin-top: 2px;
        }

        /* Verification Footer */
        .verification-footer {
            margin-top: 25px;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            font-size: 9px;
            color: #666;
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

        /* Page Break */
        .page-break {
            page-break-after: always;
        }

        /* Status colors */
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
    </style>
</head>

<body>
    <!-- Company Letterhead (Fixed - Repeated on every page) -->
    <header>
        @include('pdf.partials.company-header', [
            'accentColor' => '#7F1D1D',
            'docNumber' => $document->document_number,
            'docDate' => $document->created_at->format('d F Y'),
        ])
    </header>

    <div class="container">
        <!-- Sub-Header Banner (Category) -->
        @php
            $template = \App\Models\DocumentTemplate::where('code', $document->template)->first();
            $categoryLabel = $document->category ?? $template->name ?? $document->approval_type_label;
        @endphp
        <div class="sub-header-banner">{{ $categoryLabel }}</div>

        <!-- Document Title -->
        <div class="doc-title-banner">{{ $document->title }}</div>

        <!-- Intro paragraph (like the reference PDF) -->
        @if($document->description)
            <div style="margin-bottom: 20px; font-size: 11px; line-height: 1.7; text-align: justify;">
                {!! nl2br(e($document->description)) !!}
            </div>
        @endif

        <!-- Dynamic Fields -->
        @php
            $dynamicFields = $template ? ($template->fields['dynamic_fields'] ?? []) : [];
        @endphp

        @if(count($dynamicFields) > 0 && !empty($document->metadata))
            <div class="section">
                <div class="section-title">{{ __("INFORMASI SPESIFIK") }}</div>
                @php $inTable = false; @endphp
                @foreach($dynamicFields as $field)
                    @php 
                        $fieldName = $field['name']; 
                        $fieldValue = $document->metadata[$fieldName] ?? null; 
                        $isTableField = $field['type'] === 'table';
                    @endphp
                    
                    @if(!$isTableField)
                        @if(!$inTable)
                            <table class="info-table">
                            @php $inTable = true; @endphp
                        @endif
                        <tr style="page-break-inside: avoid;">
                            <th style="vertical-align: top; padding-top: 10px;">{{ $field['label'] }}</th>
                            <td style="vertical-align: top; padding-top: 10px;">
                                @if($field['type'] === 'textarea')
                                    : {!! nl2br(e($fieldValue ?: '-')) !!}
                                @elseif($field['type'] === 'file' && $fieldValue)
                                    @php
                                        $ext = strtolower(pathinfo($fieldValue, PATHINFO_EXTENSION));
                                    @endphp
                                    @if(in_array($ext, ['jpg', 'jpeg', 'png']))
                                        @php
                                            $imagePath = storage_path('app/public/' . ltrim(str_replace('public/', '', $fieldValue), '/'));
                                            $imgSrc = $imagePath;
                                            
                                            if (file_exists($imagePath)) {
                                                $size = @getimagesize($imagePath);
                                                if ($size && $size[1] > $size[0]) {
                                                    $source = null;
                                                    if ($ext == 'jpg' || $ext == 'jpeg') {
                                                        $source = @imagecreatefromjpeg($imagePath);
                                                    } elseif ($ext == 'png') {
                                                        $source = @imagecreatefrompng($imagePath);
                                                    }
                                                    
                                                    if ($source) {
                                                        $rotated = imagerotate($source, 90, 0);
                                                        ob_start();
                                                        if ($ext == 'png') {
                                                            imagepng($rotated);
                                                        } else {
                                                            imagejpeg($rotated);
                                                        }
                                                        $imageData = ob_get_clean();
                                                        imagedestroy($source);
                                                        imagedestroy($rotated);
                                                        $imgSrc = 'data:image/' . ($ext == 'jpg' ? 'jpeg' : $ext) . ';base64,' . base64_encode($imageData);
                                                    }
                                                }
                                            }
                                        @endphp
                                        <div style="background-color: #f8fafc; padding: 5px; border: 1px solid #e2e8f0; border-radius: 4px; display: inline-block;">
                                            <img src="{{ $imgSrc }}" alt="{{ $field['label'] }}" style="max-width: 250px; height: auto;" />
                                        </div>
                                    @else
                                        : <em style="font-size: 10px; color: #555;">{{ __("Terdapat lampiran berkas") }} ({{ pathinfo($fieldValue, PATHINFO_EXTENSION) }}) - {{ __("silakan unduh melalui sistem.") }}</em>
                                    @endif
                                @else
                                    @if($fieldValue)
                                        @if($field['type'] === 'date')
                                            : {{ \Carbon\Carbon::parse($fieldValue)->format('d F Y') }}
                                        @elseif($field['type'] === 'number')
                                            : {{ is_numeric($fieldValue) ? number_format((float)$fieldValue, 0, ',', '.') : $fieldValue }}
                                        @else
                                            : {{ $fieldValue }}
                                        @endif
                                    @else
                                        : -
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @else
                        @if($inTable)
                            </table>
                            @php $inTable = false; @endphp
                        @endif
                        
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
                    @endif
                @endforeach
                @if($inTable)
                    </table>
                @endif
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

        <!-- Signature Block (Horizontal - like reference PDF) -->
        @if(in_array($document->status, ['approved', 'completed', 'paid', 'presiden_review', 'director_review', 'finance_review']))
            @php
                $approvals = $document->approvals;
                $totalSigners = 1 + count($approvals); // requester + approvers
                $verificationUrl = $document->approval_hash ? route('verify.show', $document->approval_hash) : null;
            @endphp

            <div class="signature-section" style="page-break-before: always;">
                <div class="section-title">{{ __("LEMBAR PERSETUJUAN") }}</div>
                
                <!-- Location & Approval text -->
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; font-size: 11px;">
                                Jakarta,
                            </td>
                            <td style="width: 50%; text-align: right; vertical-align: top; font-size: 11px;">
                                {{ __("Disetujui") }},
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Signature Columns -->
                <table class="signature-table">
                    {{-- Role labels row --}}
                    <tr>
                        <td>
                            <div class="sig-role-label">{{ __("Pemohon") }}</div>
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                <div class="sig-role-label">{{ $approval->approver?->position ?? $approval->role_label ?? 'Approver' }}</div>
                            </td>
                        @endforeach
                    </tr>

                    {{-- QR Code / Signature area row --}}
                    <tr>
                        {{-- Requester QR --}}
                        <td>
                            @if($verificationUrl)
                                <div class="sig-qr-box">
                                    <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                                </div>
                            @else
                                <div class="sig-pending-box"><span>-</span></div>
                            @endif
                        </td>
                        {{-- Approvers QR --}}
                        @foreach($approvals as $approval)
                            <td>
                                @if($approval->status === 'approved' && $verificationUrl)
                                    <div class="sig-qr-box">
                                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR">
                                    </div>
                                @else
                                    <div class="sig-pending-box"><span>Pending</span></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Name row (underlined) --}}
                    <tr>
                        <td>
                            <span class="sig-name">{{ $document->user->name }}</span><br>
                            <span class="sig-nip">NIP. {{ $document->user->employee_id ?? '-' }}</span>
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                @if($approval->approver)
                                    <span class="sig-name">{{ $approval->approver->name }}</span><br>
                                    <span class="sig-nip">NIP. {{ $approval->approver->employee_id ?? '-' }}</span>
                                @else
                                    <span class="sig-name">-</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>

                    {{-- Date row --}}
                    <tr>
                        <td>
                            <span class="sig-date">
                                {{ $document->submitted_at ? $document->submitted_at->format('d/m/Y H:i') : $document->created_at->format('d/m/Y H:i') }}
                            </span>
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                <span class="sig-date">
                                    {{ $approval->action_at ? $approval->action_at->format('d/m/Y H:i') : '-' }}
                                </span>
                            </td>
                        @endforeach
                    </tr>
                </table>
            </div>
        @endif

        <!-- Verification Footer -->
        <div class="verification-footer">
            <table>
                <tr>
                    <td style="width: 75%; padding-right: 15px;">
                        <strong>{{ __("Catatan Verifikasi") }}:</strong><br>
                        {{ __("Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe.") }}
                        {{ __("Scan QR Code untuk memverifikasi keaslian dokumen ini.") }}<br>
                        <strong>Hash:</strong> {{ substr($document->approval_hash ?? 'N/A', 0, 32) }}...
                    </td>
                    <td style="width: 25%; text-align: center;">
                        @if(isset($qrCode))
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 60px; height: 60px;">
                            <div style="font-size: 7px; color: #888; margin-top: 3px;">{{ __("Scan untuk verifikasi") }}</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 20px; text-align: center; font-size: 9px; color: #999;">
            {{ __("Dicetak pada") }}: {{ now()->format('d F Y H:i:s') }} | ConfirmMe - PT. Info Solusindo Data Utama
        </div>
    </div>
</body>

</html>
