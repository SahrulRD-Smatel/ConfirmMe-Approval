<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $po->po_number }}</title>
    <style>
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
            padding: 20px 40px;
        }

        /* Document Number Bar */
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
            width: 120px;
            padding: 6px;
            font-weight: bold;
            vertical-align: top;
        }

        .info-table td {
            padding: 6px;
            vertical-align: top;
        }

        /* Data Table (Dynamic Fields) */
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

        .qr-code img {
            width: 100px;
            height: 100px;
        }

        .qr-label {
            font-size: 8px;
            color: #666;
            margin-top: 5px;
        }

        .verification-note {
            font-size: 9px;
            color: #666;
            padding: 10px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
        }
        
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
    </style>
</head>

<body>
    <div class="container">
        <!-- Company Letterhead -->
        @include('pdf.partials.company-header', ['accentColor' => '#7F1D1D'])

        <!-- Document Number Bar -->
        <div style="display: table; width: 100%; margin-bottom: 15px;">
            <div style="display: table-cell; width: 50%; vertical-align: middle;"></div>
            <div style="display: table-cell; width: 50%; text-align: right; vertical-align: middle;">
                <div class="doc-number">{{ $po->po_number }}</div>
                <div class="doc-date">{{ \Carbon\Carbon::parse($po->order_date)->format('d F Y') }}</div>
            </div>
        </div>

        <!-- Title -->
        <div class="title">
            <h1>PURCHASE ORDER</h1>
        </div>

        <!-- Vendor & Order Info -->
        <div style="display: table; width: 100%; margin-bottom: 20px;">
            <div style="display: table-cell; width: 50%; vertical-align: top; padding-right: 15px;">
                <div class="section-title">Kepad Yth. (Vendor)</div>
                <div style="font-weight: bold; font-size: 14px;">{{ $po->vendor_name }}</div>
                <div>{{ $po->vendor_address }}</div>
                @if($po->vendor_phone)<div>Phone: {{ $po->vendor_phone }}</div>@endif
                @if($po->vendor_email)<div>Email: {{ $po->vendor_email }}</div>@endif
            </div>
            <div style="display: table-cell; width: 50%; vertical-align: top;">
                <div class="section-title">Informasi Pesanan</div>
                <table class="info-table">
                    <tr>
                        <th>Tgl Order</th>
                        <td>: {{ \Carbon\Carbon::parse($po->order_date)->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <th>Estimasi Kirim</th>
                        <td>: {{ $po->expected_delivery_date ? \Carbon\Carbon::parse($po->expected_delivery_date)->format('d F Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Referensi Dokumen</th>
                        <td>: {{ $po->document ? $po->document->document_number : '-' }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Items Table -->
        <div class="section">
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
                    @foreach($po->items as $index => $item)
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
            
            <div style="overflow: hidden; margin-top: 10px;">
                <table class="totals-table">
                    <tr>
                        <th>Subtotal</th>
                        <td>Rp {{ number_format($po->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <th>Pajak</th>
                        <td>Rp {{ number_format($po->tax_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="totals-total">
                        <th>Total Keseluruhan</th>
                        <td>Rp {{ number_format($po->total_amount, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
            <div style="clear: both;"></div>
        </div>

        @if($po->notes)
            <div class="section" style="page-break-before: always; padding-top: 20px;">
                <div class="section-title">Catatan</div>
                <div class="content-box">
                    {!! nl2br(e($po->notes)) !!}
                </div>
            </div>
        @endif

        @if($po->terms_conditions)
            <div class="section" style="page-break-inside: avoid;">
                <div class="section-title">Syarat & Ketentuan</div>
                <div class="content-box" style="font-size: 11px;">
                    {!! nl2br(e($po->terms_conditions)) !!}
                </div>
            </div>
        @endif

        <!-- Footer with QR Code -->
        <div class="footer" style="page-break-inside: avoid;">
            <div class="footer-left">
                <div class="verification-note">
                    <strong>Catatan Verifikasi:</strong><br>
                    Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe.<br>
                    Scan QR Code untuk memverifikasi keaslian dokumen ini.<br>
                    <strong>Hash:</strong> {{ substr($po->approval_hash ?? $po->document->approval_hash ?? 'N/A', 0, 32) }}...
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
    </div>
</body>
</html>
