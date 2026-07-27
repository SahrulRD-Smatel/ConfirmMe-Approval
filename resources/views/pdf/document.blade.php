<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $document->document_number }}</title>
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
            padding: 8px;
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            font-weight: 600;
        }

        .info-table td {
            padding: 8px;
            border: 1px solid #e2e8f0;
        }

        /* Content */
        .section {
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #7F1D1D;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #e2e8f0;
        }

        .content-box {
            background-color: #f8fafc;
            padding: 15px;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
        }

        /* Approval Timeline */
        .approval-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
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
    <div class="container">
        <!-- Company Letterhead -->
        @include('pdf.partials.company-header', ['accentColor' => '#7F1D1D'])

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
            <div class="section-title">INFORMASI DOKUMEN</div>
            <table class="info-table">
                <tr>
                    <th>Nomor Dokumen</th>
                    <td>{{ $document->document_number }}</td>
                </tr>
                <tr>
                    <th>Judul</th>
                    <td>{{ $document->title }}</td>
                </tr>
                <tr>
                    <th>Jenis Approval</th>
                    <td>{{ $document->approval_type_label }}</td>
                </tr>
                <tr>
                    <th>Prioritas</th>
                    <td>{{ $document->priority_label }}</td>
                </tr>
                <tr>
                    <th>Departemen</th>
                    <td>{{ $document->department ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Pengaju</th>
                    <td>{{ $document->user->name }} ({{ $document->user->employee_id ?? $document->user->email }})</td>
                </tr>
                <tr>
                    <th>Tanggal Pengajuan</th>
                    <td>{{ $document->submitted_at ? $document->submitted_at->format('d F Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><strong>{{ strtoupper($document->status_label) }}</strong></td>
                </tr>
                @if($document->approval_type === 'loan' && isset($document->metadata['loan_amount']))
                    <tr>
                        <th>Jumlah Pinjaman</th>
                        <td><strong>Rp {{ number_format($document->metadata['loan_amount'], 0, ',', '.') }}</strong></td>
                    </tr>
                    @if(isset($document->metadata['installment_count']))
                        <tr>
                            <th>Tenor Cicilan</th>
                            <td>{{ $document->metadata['installment_count'] }} bulan</td>
                        </tr>
                    @endif
                @elseif($document->estimated_budget)
                    <tr>
                        <th>Estimasi Anggaran</th>
                        <td>Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}</td>
                    </tr>
                @endif
                @if($document->approved_budget)
                    <tr>
                        <th>Anggaran Disetujui</th>
                        <td><strong>Rp {{ number_format($document->approved_budget, 0, ',', '.') }}</strong></td>
                    </tr>
                @endif
            </table>
        </div>

        <!-- Description -->
        <div class="section">
            <div class="section-title">DESKRIPSI / KETERANGAN</div>
            <div class="content-box">
                {!! nl2br(e($document->description)) !!}
            </div>
        </div>



        <!-- Footer with QR Code -->
        <div class="footer">
            <div class="footer-left">
                <div class="verification-note">
                    <strong>Catatan Verifikasi:</strong><br>
                    Dokumen ini diterbitkan secara elektronik oleh sistem ConfirmMe.
                    Scan QR Code untuk memverifikasi keaslian dokumen ini.<br>
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
                    <div class="qr-label">Scan untuk verifikasi</div>
                </div>
            </div>
        </div>

        <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #999;">
            Dicetak pada: {{ now()->format('d F Y H:i:s') }} | ConfirmMe - PT. Info Solusindo Data Utama
        </div>
    </div>
</body>

</html>