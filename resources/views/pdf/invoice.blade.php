<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - {{ $invoice->invoice_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #333;
        }

        .container {
            padding: 20px 30px;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 3px solid #7C3AED;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }

        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: middle;
        }

        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: middle;
        }

        .company-name {
            font-size: 16px;
            font-weight: bold;
            color: #7C3AED;
        }

        .company-tagline {
            font-size: 9px;
            color: #666;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #7C3AED;
        }

        .doc-number {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
        }

        /* Status Badge */
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-top: 10px;
        }

        .status-paid {
            background-color: #059669;
            color: white;
        }

        .status-partial {
            background-color: #D97706;
            color: white;
        }

        .status-unpaid {
            background-color: #DC2626;
            color: white;
        }

        /* Info Section */
        .info-section {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }

        .info-left,
        .info-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 10px;
            margin-bottom: 10px;
        }

        .info-box h3 {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .info-box p {
            font-size: 11px;
            margin-bottom: 2px;
        }

        .info-box .value {
            font-weight: bold;
        }

        /* Amount Table */
        .amount-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .amount-table th {
            background-color: #7C3AED;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .amount-table td {
            padding: 12px 10px;
            border: 1px solid #e2e8f0;
        }

        .amount-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .amount-table .number {
            text-align: right;
            font-family: monospace;
            font-size: 12px;
        }

        .amount-table .total-row {
            background-color: #7C3AED;
            color: white;
            font-weight: bold;
        }

        .amount-table .total-row td {
            border-color: #7C3AED;
            padding: 12px 10px;
            font-size: 14px;
        }

        /* Payment Summary */
        .payment-summary {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin-bottom: 20px;
        }

        .payment-row {
            display: table;
            width: 100%;
            margin-bottom: 5px;
        }

        .payment-label {
            display: table-cell;
            width: 70%;
        }

        .payment-value {
            display: table-cell;
            width: 30%;
            text-align: right;
            font-weight: bold;
            font-family: monospace;
        }

        .payment-outstanding {
            background-color: #FEE2E2;
            padding: 10px;
            margin-top: 10px;
            border-radius: 5px;
        }

        .payment-outstanding .payment-value {
            color: #DC2626;
            font-size: 14px;
        }

        /* Notes */
        .notes {
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }

        .notes h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .notes p {
            font-size: 9px;
            color: #666;
        }

        /* Footer */
        .footer {
            margin-top: 30px;
            display: table;
            width: 100%;
        }

        .footer-left {
            display: table-cell;
            width: 60%;
            vertical-align: bottom;
        }

        .footer-right {
            display: table-cell;
            width: 40%;
            text-align: center;
            vertical-align: bottom;
        }

        .qr-code img {
            width: 80px;
            height: 80px;
        }

        .qr-label {
            font-size: 8px;
            color: #666;
            margin-top: 5px;
        }

        .signature-box {
            border: 1px dashed #ccc;
            padding: 30px 20px 10px;
            text-align: center;
        }

        .signature-label {
            font-size: 10px;
            color: #666;
        }

        .signature-name {
            font-size: 11px;
            font-weight: bold;
            margin-top: 5px;
        }

        .print-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Company Letterhead -->
        @include('pdf.partials.company-header', ['accentColor' => '#7C3AED'])

        <!-- Document Title Bar -->
        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 50%; vertical-align: middle;"></div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: middle;">
                <div class="doc-title">INVOICE</div>
                <div class="doc-number">{{ $invoice->invoice_number }}</div>
                <span class="status-badge 
                    @if($invoice->status === 'paid') status-paid
                    @elseif($invoice->status === 'partial') status-partial
                    @else status-unpaid
                    @endif">
                    {{ $invoice->status === 'paid' ? 'LUNAS' : ($invoice->status === 'partial' ? 'SEBAGIAN' : 'BELUM BAYAR') }}
                </span>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Vendor / Penerima</h3>
                    <p class="value">{{ $invoice->vendor_name }}</p>
                    <p>{{ $invoice->vendor_address ?? '-' }}</p>
                </div>
                <div class="info-box">
                    <h3>Referensi</h3>
                    <p>No. Dokumen: <span class="value">{{ $invoice->document->document_number }}</span></p>
                    <p>Judul: {{ $invoice->document->title }}</p>
                    @if($invoice->purchaseOrder)
                        <p>No. PO: <span class="value">{{ $invoice->purchaseOrder->po_number }}</span></p>
                    @endif
                </div>
            </div>
            <div class="info-right" style="padding-left: 15px;">
                <div class="info-box">
                    <h3>Detail Invoice</h3>
                    <p>Tanggal Invoice: <span class="value">{{ $invoice->invoice_date->format('d F Y') }}</span></p>
                    <p>Jatuh Tempo: <span class="value">{{ $invoice->due_date->format('d F Y') }}</span></p>
                    @if($invoice->due_date->isPast() && $invoice->status !== 'paid')
                        <p style="color: #DC2626; font-weight: bold;">⚠️ OVERDUE</p>
                    @endif
                </div>
                <div class="info-box">
                    <h3>Dibuat Oleh</h3>
                    <p class="value">{{ $invoice->creator->name }}</p>
                    <p>{{ $invoice->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Amount Table -->
        <table class="amount-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Deskripsi</th>
                    <th style="width: 30%; text-align: right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $invoice->document->title }}</td>
                    <td class="number">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Pajak</td>
                    <td class="number">Rp {{ number_format($invoice->tax_amount, 0, ',', '.') }}</td>
                </tr>
                <tr class="total-row">
                    <td><strong>TOTAL</strong></td>
                    <td class="number">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Summary -->
        <div class="payment-summary">
            <h4 style="font-size: 10px; text-transform: uppercase; color: #666; margin-bottom: 10px;">Ringkasan
                Pembayaran</h4>

            <div class="payment-row">
                <div class="payment-label">Total Invoice</div>
                <div class="payment-value">Rp {{ number_format($invoice->total_amount, 0, ',', '.') }}</div>
            </div>
            <div class="payment-row">
                <div class="payment-label">Sudah Dibayar</div>
                <div class="payment-value">Rp {{ number_format($invoice->paid_amount, 0, ',', '.') }}</div>
            </div>

            @if($invoice->outstanding_amount > 0)
                <div class="payment-outstanding">
                    <div class="payment-row" style="margin-bottom: 0;">
                        <div class="payment-label"><strong>Sisa Pembayaran</strong></div>
                        <div class="payment-value">Rp {{ number_format($invoice->outstanding_amount, 0, ',', '.') }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Notes -->
        @if($invoice->notes)
            <div class="notes">
                <h4>Catatan</h4>
                <p>{{ $invoice->notes }}</p>
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <div class="signature-box" style="width: 200px;">
                    <div class="signature-label">Disetujui oleh,</div>
                    <div class="signature-name">
                        {{ $invoice->document->approvals->where('status', 'approved')->last()?->approver?->name ?? '________________' }}
                    </div>
                </div>
            </div>
            <div class="footer-right">
                <div class="qr-code">
                    @if(isset($qrCode))
                        <img src="data:image/svg+xml;base64,{{ $qrCode }}" alt="QR Code">
                    @endif
                    <div class="qr-label">Scan untuk verifikasi</div>
                </div>
            </div>
        </div>

        <div class="print-footer">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }} | ConfirmMe - PT. Info Solusindo Data Utama
        </div>
    </div>
</body>

</html>