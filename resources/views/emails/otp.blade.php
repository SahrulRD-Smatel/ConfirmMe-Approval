<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Kode Verifikasi OTP - ConfirmMe</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background-color: #1E40AF;
            color: white;
            padding: 20px;
            text-align: center;
        }

        .content {
            padding: 30px 20px;
            background-color: #f8fafc;
        }

        .otp-box {
            background-color: white;
            border: 2px dashed #1E40AF;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .otp-code {
            font-size: 32px;
            font-weight: bold;
            letter-spacing: 8px;
            color: #1E40AF;
            font-family: monospace;
        }

        .document-info {
            background-color: white;
            border: 1px solid #e2e8f0;
            padding: 15px;
            margin: 20px 0;
        }

        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            padding: 20px;
        }

        .warning {
            background-color: #FEF3C7;
            border-left: 4px solid #D97706;
            padding: 10px 15px;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="header">

        <h1 style="margin: 0;">ConfirmMe</h1>
        <p style="margin: 5px 0 0;">Sistem Approval Dokumen - PT. Info Solusindo Data Utama</p>
    </div>

    <div class="content">
        <p>Halo <strong>{{ $user->name }}</strong>,</p>

        <p>Anda telah meminta verifikasi 2FA untuk menyetujui dokumen. Berikut adalah kode OTP Anda:</p>

        <div class="otp-box">
            <div class="otp-code">{{ $otp }}</div>
        </div>

        <div class="document-info">
            <h3 style="margin-top: 0;">Detail Dokumen:</h3>
            <p><strong>Nomor:</strong> {{ $document->document_number }}</p>
            <p><strong>Judul:</strong> {{ $document->title }}</p>
            <p><strong>Jenis:</strong> {{ $document->approval_type_label }}</p>
            @if($document->estimated_budget)
                @php
                    $financeApprovalOtp = $document->approvals()
                        ->where('approver_role', 'finance')
                        ->where('status', 'approved')
                        ->whereNotNull('approved_amount')
                        ->first();
                @endphp
                @if($financeApprovalOtp && (float) $financeApprovalOtp->approved_amount !== (float) $document->estimated_budget)
                    <p><strong>Nominal Pengajuan:</strong> <s>Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}</s></p>
                    <p><strong>Nominal Disetujui Finance:</strong> <span style="color: #1E40AF; font-weight: bold;">Rp {{ number_format($financeApprovalOtp->approved_amount, 0, ',', '.') }}</span></p>
                @else
                    <p><strong>Nominal:</strong> Rp {{ number_format($document->estimated_budget, 0, ',', '.') }}</p>
                @endif
            @endif
        </div>

        <div class="warning">
            <strong>Perhatian:</strong>
            <ul style="margin: 5px 0; padding-left: 20px;">
                <li>Kode OTP ini berlaku selama <strong>10 menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapapun</li>
                <li>Jika Anda tidak meminta verifikasi ini, abaikan email ini</li>
            </ul>
        </div>
    </div>

    <div class="footer">
        <p>Email ini dikirim secara otomatis oleh sistem ConfirmMe.</p>
        <p>© {{ date('Y') }} PT. Info Solusindo Data Utama. All rights reserved.</p>
    </div>
</body>

</html>