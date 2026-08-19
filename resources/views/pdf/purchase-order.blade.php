<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PO - {{ $po->po_number }}</title>
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
            border-bottom: 3px solid #1E40AF;
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
            color: #1E40AF;
        }

        .company-tagline {
            font-size: 9px;
            color: #666;
        }

        .doc-title {
            font-size: 20px;
            font-weight: bold;
            color: #1E40AF;
        }

        .doc-number {
            font-size: 12px;
            color: #666;
            margin-top: 3px;
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

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table th {
            background-color: #1E40AF;
            color: white;
            padding: 8px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }

        .items-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
            font-size: 10px;
        }

        .items-table tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .items-table .number {
            text-align: right;
        }

        /* Totals */
        .totals {
            float: right;
            width: 250px;
        }

        .totals-row {
            display: table;
            width: 100%;
            border-bottom: 1px solid #e2e8f0;
            padding: 5px 0;
        }

        .totals-label {
            display: table-cell;
            width: 50%;
            text-align: right;
            padding-right: 10px;
        }

        .totals-value {
            display: table-cell;
            width: 50%;
            text-align: right;
            font-weight: bold;
        }

        .totals-row.grand-total {
            background-color: #1E40AF;
            color: white;
            font-size: 12px;
        }

        .totals-row.grand-total .totals-label,
        .totals-row.grand-total .totals-value {
            padding: 8px 10px;
        }

        /* Terms */
        .terms {
            clear: both;
            margin-top: 30px;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }

        .terms h4 {
            font-size: 10px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 5px;
        }

        .terms p {
            font-size: 9px;
            color: #666;
        }

        /* Footer */
        .footer {
            clear: both;
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
        @include('pdf.partials.company-header', ['accentColor' => '#1E40AF'])

        <!-- Document Title Bar -->
        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 50%; vertical-align: middle;"></div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: middle;">
                <div class="doc-title">PURCHASE ORDER</div>
                <div class="doc-number">{{ $po->po_number }}</div>
            </div>
        </div>

        <!-- Info Section -->
        <div class="info-section">
            <div class="info-left">
                <div class="info-box">
                    <h3>Vendor</h3>
                    <p class="value">{{ $po->vendor_name }}</p>
                    <p>{{ $po->vendor_address ?? '-' }}</p>
                </div>
                <div class="info-box">
                    <h3>Referensi Dokumen</h3>
                    <p>No. Dokumen: <span class="value">{{ $po->document->document_number }}</span></p>
                    <p>Judul: {{ $po->document->title }}</p>
                </div>
            </div>
            <div class="info-right" style="padding-left: 15px;">
                <div class="info-box">
                    <h3>Detail PO</h3>
                    <p>Tanggal PO: <span class="value">{{ $po->order_date->format('d F Y') }}</span></p>
                    <p>Expected Delivery: <span
                            class="value">{{ $po->expected_delivery_date?->format('d F Y') ?? '-' }}</span></p>
                    <p>Status: <span class="value">{{ strtoupper($po->status) }}</span></p>
                </div>
                <div class="info-box">
                    <h3>Dibuat Oleh</h3>
                    <p class="value">{{ $po->creator->name }}</p>
                    <p>{{ $po->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 30px;">No</th>
                    <th>Deskripsi Item</th>
                    <th style="width: 60px;">Qty</th>
                    <th style="width: 50px;">Satuan</th>
                    <th style="width: 100px;">Harga Satuan</th>
                    <th style="width: 100px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @forelse($po->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>
                            <strong>{{ $item->item_name }}</strong>
                            @if($item->description)
                                <br><small>{{ $item->description }}</small>
                            @endif
                        </td>
                        <td class="number">{{ $item->quantity }}</td>
                        <td>{{ $item->unit }}</td>
                        <td class="number">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                        <td class="number">Rp {{ number_format($item->total_price, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 20px;">Tidak ada item</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">Rp {{ number_format($po->subtotal, 0, ',', '.') }}</div>
            </div>
            <div class="totals-row">
                <div class="totals-label">Pajak ({{ $po->tax_percentage ?? 0 }}%):</div>
                <div class="totals-value">Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</div>
            </div>
            <div class="totals-row grand-total">
                <div class="totals-label">TOTAL:</div>
                <div class="totals-value">Rp {{ number_format($po->total_amount, 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Terms -->
        @if($po->terms || $po->notes)
            <div class="terms">
                @if($po->terms)
                    <h4>Syarat & Ketentuan</h4>
                    <p>{{ $po->terms }}</p>
                @endif
                @if($po->notes)
                    <h4 style="margin-top: 10px;">Catatan</h4>
                    <p>{{ $po->notes }}</p>
                @endif
            </div>
        @endif

        <!-- Footer -->
        <div class="footer">
            <div class="footer-left">
                <div class="signature-box" style="width: 200px;">
                    <div class="signature-label">Disetujui oleh,</div>
                    <div class="signature-name">
                        {{ $po->document->approvals->where('status', 'approved')->last()?->approver?->name ?? '________________' }}
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