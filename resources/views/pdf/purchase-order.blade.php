<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $po->po_number }}</title>
    <style>
        @page {
            margin: 140px 45px 35px 45px !important;
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10.5px;
            line-height: 1.35;
            color: #333;
            background: #fff;
        }

        .container {
            width: 100%;
        }

        .doc-title-banner {
            text-align: center;
            margin-top: 4px;
            margin-bottom: 14px;
            font-size: 13px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #1a1a1a;
        }

        .section-title {
            font-size: 10px;
            font-weight: bold;
            color: #7F1D1D;
            margin-bottom: 4px;
            padding-bottom: 2px;
            border-bottom: 1.5px solid #7F1D1D;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }

        .info-table th {
            text-align: left;
            width: 125px;
            padding: 1.5px 0;
            color: #4b5563;
            font-weight: normal;
            font-size: 10px;
            vertical-align: top;
            border: none;
        }

        .info-table td {
            padding: 1.5px 0;
            font-size: 10px;
            color: #111827;
            vertical-align: top;
            border: none;
        }

        /* Data Table (Flat Default Material matching document.blade.php) */
        .data-table {
            width: 100%;
            margin-top: 4px;
            margin-bottom: 8px;
            border-collapse: collapse;
            table-layout: auto;
        }

        .data-table thead {
            display: table-header-group;
        }

        .data-table th {
            text-align: left;
            background-color: #f3f4f6;
            color: #1f2937;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 9px;
            letter-spacing: 0.5px;
            padding: 5px 8px;
            border: 1px solid #d1d5db;
        }

        .data-table td {
            padding: 4px 8px;
            border: 1px solid #e5e7eb;
            font-size: 9.5px;
            color: #222;
        }

        .data-table tr:nth-child(even) td {
            background-color: #f9fafb;
        }

        /* Signature Section - Standardized Boxed Cards matching PERMINTAAN PEMBELIAN BARANG */
        .signature-section {
            margin-top: 8px;
            page-break-inside: avoid;
        }

        .sig-container {
            width: 100%;
            border-collapse: collapse;
            margin-top: 6px;
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
            font-size: 8.5px;
            padding: 4px 2px;
            background-color: #fff;
            height: 16px;
            color: #000;
        }

        .sig-card-body {
            text-align: center;
            vertical-align: middle;
            height: 74px;
            min-height: 74px;
            padding: 2px;
            background-color: #fff;
        }

        .sig-card-footer {
            border-top: 1px solid #000;
            text-align: center;
            font-size: 8.5px;
            font-weight: bold;
            padding: 4px 2px;
            background-color: #fff;
            min-height: 18px;
            color: #000;
        }

        .sig-status-signed {
            font-size: 7.5px;
            color: #059669;
            font-weight: bold;
            margin-top: 1px;
            letter-spacing: 0.3px;
        }

        /* Verification Footer matching document.blade.php */
        .verification-footer {
            margin-top: 10px;
            padding: 6px 10px;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            font-size: 8px;
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
    </style>
</head>

<body>
    <!-- Company Letterhead (Fixed - Repeated on every page) -->
    <header>
        @include('pdf.partials.company-header', [
            'accentColor' => '#7F1D1D',
            'docNumber' => $po->po_number,
            'docDate' => \Carbon\Carbon::parse($po->order_date)->translatedFormat('d F Y'),
        ])
    </header>

    <div class="container">

        <!-- Document Title -->
        <div class="doc-title-banner">PURCHASE ORDER</div>

        <!-- Vendor & Order Info (Clean 2-Column Layout) -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 8px;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding-right: 15px;">
                    <div class="section-title">{{ __('Kepada Yth. (Vendor)') }}</div>
                    <div style="font-weight: bold; font-size: 11px; color: #111827; margin-bottom: 2px;">{{ $po->vendor_name }}</div>
                    <div style="font-size: 9.5px; color: #4b5563; line-height: 1.35;">{{ $po->vendor_address }}</div>
                    @if($po->vendor_phone)<div style="font-size: 9.5px; color: #4b5563;">Phone: {{ $po->vendor_phone }}</div>@endif
                    @if($po->vendor_email)<div style="font-size: 9.5px; color: #4b5563;">Email: {{ $po->vendor_email }}</div>@endif
                </td>
                <td style="width: 50%; vertical-align: top;">
                    <div class="section-title">{{ __('Informasi Pesanan') }}</div>
                    <table class="info-table">
                        <tr>
                            <th>{{ __('Tgl Order') }}</th>
                            <td style="width: 10px;">:</td>
                            <td>{{ \Carbon\Carbon::parse($po->order_date)->translatedFormat('d F Y') }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Estimasi Kirim') }}</th>
                            <td>:</td>
                            <td>{{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->translatedFormat('d F Y') : '-' }}</td>
                        </tr>
                        <tr>
                            <th>{{ __('Referensi Dokumen') }}</th>
                            <td>:</td>
                            <td>{{ $po->document ? $po->document->document_number : '-' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Items Table with Integrated Totals (No CSS floats) -->
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">No</th>
                    <th style="width: 45%;">{{ __('Nama Item') }}</th>
                    <th style="width: 10%; text-align: center;">{{ __('Qty') }}</th>
                    <th style="width: 10%; text-align: center;">{{ __('Unit') }}</th>
                    <th style="width: 15%; text-align: right;">{{ __('Harga Satuan') }}</th>
                    <th style="width: 15%; text-align: right;">{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($po->items as $index => $item)
                    <tr>
                        <td style="text-align: center; color: #6b7280;">{{ $index + 1 }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td style="text-align: center;">{{ $item->quantity }}</td>
                        <td style="text-align: center;">{{ $item->unit }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td style="text-align: right;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f9fafb; border: 1px solid #e5e7eb;">{{ __('Subtotal') }}</td>
                    <td colspan="2" style="text-align: right; font-weight: bold; border: 1px solid #e5e7eb;">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                </tr>
                @if($po->tax_amount > 0)
                    <tr>
                        <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f9fafb; border: 1px solid #e5e7eb;">{{ __('Pajak') }}</td>
                        <td colspan="2" style="text-align: right; font-weight: bold; border: 1px solid #e5e7eb;">Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                @endif
                <tr style="background-color: #f3f4f6; font-weight: bold;">
                    <td colspan="4" style="text-align: right; padding: 4px 8px; border: 1px solid #d1d5db; font-size: 10px;">{{ __('Total Keseluruhan') }}</td>
                    <td colspan="2" style="text-align: right; padding: 4px 8px; border: 1px solid #d1d5db; font-size: 10px; color: #111;">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <!-- Catatan & Syarat Ketentuan (Clean Formal Text) -->
        @if($po->notes)
            <div style="margin-top: 5px; margin-bottom: 5px; font-size: 9.5px; color: #222; page-break-inside: avoid;">
                <strong>{{ __('Catatan') }}:</strong> {!! nl2br(e($po->notes)) !!}
            </div>
        @endif

        @if($po->terms_conditions)
            <div style="margin-top: 5px; margin-bottom: 5px; font-size: 9px; color: #4b5563; page-break-inside: avoid;">
                <strong>{{ __('Syarat & Ketentuan') }}:</strong> {!! nl2br(e($po->terms_conditions)) !!}
            </div>
        @endif

        <!-- Adaptive Signature Block matching document.blade.php -->
        @php
            $document = $po->document;
            $approvals = $document ? $document->approvals : collect();
            $verificationHash = $po->approval_hash ?? $document->approval_hash ?? null;
            $verificationUrl = $verificationHash ? route('verify.show', $verificationHash) : null;

            $genSignerQr = function(string $url) {
                return base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')->size(300)->margin(1)->generate($url));
            };

            $allSigners = collect();

            // 1. Pemohon (Always first in document workflow)
            if ($document && $document->user) {
                $allSigners->push([
                    'role' => __('Pemohon'),
                    'name' => format_acronyms($document->user->name ?? 'Pemohon'),
                    'nip' => $document->user->employee_id ?? null,
                    'is_signed' => (bool)$verificationUrl,
                    'signed_label' => 'Signed',
                    'status' => 'approved',
                    'qr_code' => $verificationUrl ? $genSignerQr(route('verify.show', ['hash' => $verificationHash, 'signer' => 'requester'])) : null,
                ]);
            }

            // 2. Approvers in sequential workflow order
            $templateSigners = $document ? ($document->documentTemplate->fields['workflow_signers'] ?? (\App\Models\DocumentTemplate::where('code', (string)$document->template)->first()?->fields['workflow_signers'] ?? [])) : [];
            foreach ($approvals as $approval) {
                $orderIndex = $approval->approval_order - 1;
                $signerCfg = (!empty($templateSigners) && isset($templateSigners[$orderIndex])) ? $templateSigners[$orderIndex] : null;

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
                if (!empty($signerCfg['pdf_label'])) {
                    $customLabel = $signerCfg['pdf_label'];
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
                        ? $genSignerQr(route('verify.show', ['hash' => $verificationHash, 'step' => $approval->id]))
                        : null,
                ]);
            }

            // Adaptive chunking: If more than 4 signers, split evenly into 2 balanced rows
            $chunkSize = $allSigners->count() > 4 ? (int) ceil($allSigners->count() / 2) : max(1, $allSigners->count());
            $signerChunks = $allSigners->chunk($chunkSize);
        @endphp

        @if($allSigners->isNotEmpty() && $document && in_array($document->status, ['approved', 'completed', 'paid']))
            <div class="signature-section">
                <table style="width: 100%; border-collapse: collapse; margin-bottom: 4px;">
                    <tr>
                        <td style="text-align: right; font-size: 9.5px; padding-bottom: 2px;">
                            Jakarta, {{ \Carbon\Carbon::parse($po->order_date)->translatedFormat('d F Y') }}
                        </td>
                    </tr>
                    <tr>
                        <td style="text-align: center; font-size: 9.5px; padding-bottom: 4px; font-weight: bold;">
                            {{ __('Disetujui') }},
                        </td>
                    </tr>
                </table>

                @foreach($signerChunks as $chunkIndex => $chunk)
                    @php
                        $countInChunk = $chunk->count();
                        $tableWidth = round(($countInChunk / $chunkSize) * 100, 2) . '%';
                        $colWidth = round(100 / $countInChunk, 2);
                    @endphp

                    <table class="sig-container" align="center" style="width: {{ $tableWidth }}; margin: {{ $chunkIndex > 0 ? '6px' : '0' }} auto 0 auto;">
                        <tr>
                            @foreach($chunk as $signer)
                                <td class="sig-box-cell" style="width: {{ $colWidth }}%;">
                                    <table class="sig-card">
                                        <tr>
                                            <td class="sig-card-header">{{ $signer['role'] }}</td>
                                        </tr>
                                        <tr>
                                            <td class="sig-card-body">
                                                @if($signer['is_signed'] && $signer['qr_code'])
                                                    <img src="data:image/svg+xml;base64,{{ $signer['qr_code'] }}" alt="QR Code" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                                                @elseif($signer['status'] === 'approved')
                                                    <div style="height: 70px; line-height: 70px; color: #059669; font-size: 9px; font-weight: bold; text-align: center;">APPROVED</div>
                                                @else
                                                    <div style="height: 70px; line-height: 70px; color: #aaa; font-size: 8.5px; text-align: center;">{{ ucfirst($signer['status'] ?? 'pending') }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="sig-card-footer">
                                                <div>{{ $signer['name'] }}</div>
                                                @if(!empty($signer['nip']))
                                                    <div style="font-size: 7.5px; font-weight: normal; color: #555;">NIP. {{ $signer['nip'] }}</div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                            @endforeach
                        </tr>
                    </table>
                @endforeach
            </div>
        @endif

        <!-- Verification Footer matching document.blade.php -->
        <div class="verification-footer">
            <table>
                <tr>
                    <td style="width: 75%; padding-right: 15px; vertical-align: middle; line-height: 1.45;">
                        <strong style="color: #0f172a; font-size: 8.5px;">{{ __('Catatan Verifikasi') }}:</strong><br>
                        {{ __('Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe - PT Info Solusindo Data Utama.') }}
                        {{ __('Scan QR Code di samping untuk memverifikasi keaslian dokumen.') }}<br>
                        <span style="color: #1e293b; font-size: 8px;"><strong>ID Dokumen:</strong> {{ $po->po_number }} | <strong>Hash:</strong> {{ substr($verificationHash ?? 'N/A', 0, 32) }}...</span>
                    </td>
                    <td style="width: 25%; text-align: center; vertical-align: middle;">
                        @if(isset($qrCode))
                            <div style="display: inline-block; text-align: center;">
                                <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 70px; height: 70px; display: block; margin: 0 auto;">
                                <div style="font-size: 7.5px; font-weight: bold; color: #334155; margin-top: 2px;">{{ __('Scan untuk verifikasi') }}</div>
                            </div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

    </div>
</body>

</html>
