<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->invoice_number }}</title>
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

        * {
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
            width: 120px;
            padding: 6px;
            font-weight: bold;
            vertical-align: top;
            font-size: 11px;
        }

        .info-table td {
            padding: 6px;
            vertical-align: top;
            font-size: 11px;
        }

        /* Data Table */
        .data-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .data-table th, .data-table td {
            padding: 10px 8px;
            border: 1px solid #e5e7eb;
            font-size: 11px;
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
            font-size: 10px;
        }

        /* Content */
        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 13px;
            font-weight: bold;
            color: #7F1D1D;
            margin-bottom: 12px;
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
        }

        /* Totals */
        .totals-table {
            width: 40%;
            float: right;
            border-collapse: collapse;
        }
        
        .totals-table th, .totals-table td {
            padding: 6px;
            text-align: right;
        }
        
        .totals-table th {
            text-align: left;
        }
        
        .totals-total {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #333;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            font-size: 11px;
            text-transform: uppercase;
        }
        .status-paid { background-color: #d1fae5; color: #065f46; border: 1px solid #34d399; }
        .status-draft { background-color: #fef3c7; color: #92400e; border: 1px solid #fbbf24; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; border: 1px solid #f87171; }

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
    </style>
</head>

<body>
    <!-- Company Letterhead (Fixed - Repeated on every page) -->
    <header>
        @include('pdf.partials.company-header', [
            'accentColor' => '#7F1D1D',
            'docNumber' => $invoice->invoice_number,
            'docDate' => \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y'),
        ])
    </header>

    <div class="container">

        <!-- Sub-Header Banner -->
        <div class="sub-header-banner">Invoice</div>
        <div class="doc-title-banner">{{ $invoice->invoice_number }}</div>

        <!-- Vendor & Invoice Info -->
        <div style="display: table; width: 100%; margin-bottom: 20px;">
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 15px;">
                <div class="section-title">Tagihan Kepada (Vendor)</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $invoice->vendor_name }}</div>
                @if($invoice->vendor_address)<div>{{ $invoice->vendor_address }}</div>@endif
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <div class="section-title">Informasi Tagihan</div>
                <table class="info-table">
                    <tr>
                        <th>Tgl Invoice</th>
                        <td>: {{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Jatuh Tempo</th>
                        <td>: {{ \Carbon\Carbon::parse($invoice->due_date)->format('d F Y') }}</td>
                    </tr>
                    @if($invoice->purchaseOrder)
                    <tr>
                        <th>No. Purchase Order</th>
                        <td>: {{ $invoice->purchaseOrder->po_number }}</td>
                    </tr>
                    @endif
                    <tr>
                        <th>Referensi Dokumen</th>
                        <td>: {{ $invoice->document ? $invoice->document->document_number : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>: 
                            <span class="status-badge status-{{ $invoice->status === 'paid' ? 'paid' : ($invoice->status === 'cancelled' ? 'cancelled' : 'draft') }}">
                                {{ $invoice->status }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table (if PO exists) -->
        <div class="section">
            @if($invoice->purchaseOrder && $invoice->purchaseOrder->items->count() > 0)
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 5%; text-align: center;">No</th>
                            <th style="width: 45%;">Nama Item</th>
                            <th style="width: 10%; text-align: center;">Qty</th>
                            <th style="width: 10%; text-align: center;">Unit</th>
                            <th style="width: 15%; text-align: right;">Harga Satuan</th>
                            <th style="width: 15%; text-align: right;">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoice->purchaseOrder->items as $index => $item)
                            <tr>
                                <td style="text-align: center;">{{ $index + 1 }}</td>
                                <td>{{ $item->item_name }}</td>
                                <td style="text-align: center;">{{ $item->quantity }}</td>
                                <td style="text-align: center;">{{ $item->unit }}</td>
                                <td style="text-align: right;">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                                <td style="text-align: right;">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
            
            <div style="overflow: hidden; margin-top: 10px;">
                <table class="totals-table">
                    <tr>
                        <th>Subtotal</th>
                        <td>Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Pajak</th>
                        <td>Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="totals-total">
                        <th>Total Tagihan</th>
                        <td>Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @if($invoice->paid_amount > 0)
                    <tr>
                        <th>Telah Dibayar</th>
                        <td style="color: #065f46;">- Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="totals-total" style="border-top: 1px solid #ccc;">
                        <th>Sisa Tagihan</th>
                        <td style="color: #b91c1c;">Rp {{ number_format($invoice->total_amount - $invoice->paid_amount, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        @if($invoice->notes)
            <div class="section" style="page-break-before: always; padding-top: 20px;">
                <div class="section-title">Catatan</div>
                <div class="content-box">
                    {!! nl2br(e($invoice->notes)) !!}
                </div>
            </div>
        @endif

        <!-- Signature Block -->
        @php
            $document = $invoice->document;
            $approvals = $document ? $document->approvals : collect();
            $verificationHash = $invoice->approval_hash ?? $document->approval_hash ?? null;
            $verificationUrl = $verificationHash ? route('verify.show', $verificationHash) : null;
        @endphp

        @if($document && in_array($document->status, ['approved', 'completed', 'paid']))
            <div class="signature-section">
                <div class="section-title">{{ __("LEMBAR PERSETUJUAN") }}</div>
                
                <div style="margin-bottom: 15px;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 50%; vertical-align: top; font-size: 11px;">Jakarta,</td>
                            <td style="width: 50%; text-align: right; vertical-align: top; font-size: 11px;">{{ __("Disetujui") }},</td>
                        </tr>
                    </table>
                </div>

                <table class="signature-table">
                    <tr>
                        <td><div class="sig-role-label">{{ __("Pemohon") }}</div></td>
                        @foreach($approvals as $approval)
                            <td><div class="sig-role-label">{{ $approval->approver?->position ?? $approval->role_label ?? 'Approver' }}</div></td>
                        @endforeach
                    </tr>
                    <tr>
                        <td>
                            @if($verificationUrl)
                                <div class="sig-qr-box"><img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR"></div>
                            @else
                                <div class="sig-pending-box"><span>-</span></div>
                            @endif
                        </td>
                        @foreach($approvals as $approval)
                            <td>
                                @if($approval->status === 'approved' && $verificationUrl)
                                    <div class="sig-qr-box"><img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR"></div>
                                @else
                                    <div class="sig-pending-box"><span>Pending</span></div>
                                @endif
                            </td>
                        @endforeach
                    </tr>
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
                    <tr>
                        <td><span class="sig-date">{{ $document->submitted_at ? $document->submitted_at->format('d/m/Y H:i') : $document->created_at->format('d/m/Y H:i') }}</span></td>
                        @foreach($approvals as $approval)
                            <td><span class="sig-date">{{ $approval->action_at ? $approval->action_at->format('d/m/Y H:i') : '-' }}</span></td>
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
                        <strong>Catatan Verifikasi:</strong><br>
                        Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe.
                        Scan QR Code untuk memverifikasi keaslian dokumen ini.<br>
                        <strong>Hash:</strong> {{ substr($verificationHash ?? 'N/A', 0, 32) }}...
                    </td>
                    <td style="width: 25%; text-align: center;">
                        @if(isset($qrCode))
                            <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code" style="width: 60px; height: 60px;">
                            <div style="font-size: 7px; color: #888; margin-top: 3px;">Scan untuk verifikasi</div>
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
