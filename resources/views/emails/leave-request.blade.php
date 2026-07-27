<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Cuti - ConfirmMe</title>
</head>

<body
    style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
        style="background-color: #f3f4f6;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
                    style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    {{-- Header with Logo --}}
                    <tr>
                        <td style="padding: 30px 40px; border-bottom: 3px solid {{ $actionColor }};">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="vertical-align: middle;">
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0">
                                            <tr>
                                                <td style="vertical-align: middle; padding-right: 12px;">
                                                    <img src="{{ asset('images/logo.png') }}" alt="ConfirmMe Logo"
                                                        width="42" height="42"
                                                        style="display: block; border-radius: 8px;">
                                                </td>
                                                <td style="vertical-align: middle;">
                                                    <h1 style="margin: 0; font-size: 24px; color: #82181a;">
                                                        ConfirmMe
                                                    </h1>
                                                    <p style="margin: 4px 0 0; font-size: 12px; color: #6b7280;">
                                                        PT INFO SOLUSINDO DATA UTAMA
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td style="text-align: right; vertical-align: middle;">
                                        <span
                                            style="display: inline-block; padding: 6px 14px; background-color: {{ $actionColor }}; color: #ffffff; font-size: 12px; font-weight: 600; border-radius: 20px;">
                                            CUTI
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Greeting --}}
                    <tr>
                        <td style="padding: 30px 40px 10px;">
                            <p style="margin: 0; font-size: 16px; color: #374151;">
                                {!! $greeting !!}
                            </p>
                        </td>
                    </tr>

                    {{-- Main Message --}}
                    <tr>
                        <td style="padding: 10px 40px 20px;">
                            <p style="margin: 0; font-size: 15px; color: #374151; line-height: 1.7;">
                                {!! $bodyContent !!}
                            </p>
                        </td>
                    </tr>

                    {{-- Leave Details Card --}}
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 12px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">
                                            Detail Pengajuan Cuti</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Pemohon:</span>
                                                    <span
                                                        style="font-size: 14px; font-weight: 600; color: #111827; float: right;">{{ $leaveRequest->user->name }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Departemen:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $leaveRequest->user->department }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Jenis Cuti:</span>
                                                    <span
                                                        style="font-size: 14px; font-weight: 600; color: #1E40AF; float: right;">{{ $leaveRequest->type_label }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Tanggal
                                                        Mulai:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $leaveRequest->start_date->format('d M Y') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Tanggal
                                                        Selesai:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $leaveRequest->end_date->format('d M Y') }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">Durasi:</span>
                                                    <span
                                                        style="font-size: 14px; font-weight: 700; color: #1E40AF; float: right;">{{ $leaveRequest->duration }}
                                                        hari</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span style="font-size: 13px; color: #6b7280;">Alasan:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ Str::limit($leaveRequest->reason, 50) }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Rejection Reason --}}
                    @if($action === 'rejected' && $leaveRequest->rejection_reason)
                        <tr>
                            <td style="padding: 0 40px 30px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                    style="background-color: #fef2f2; border-radius: 6px; border: 1px solid #fecaca;">
                                    <tr>
                                        <td style="padding: 16px;">
                                            <p
                                                style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #dc2626;">
                                                Alasan Penolakan:</p>
                                            <p style="margin: 0; font-size: 14px; color: #7f1d1d; line-height: 1.5;">
                                                {{ $leaveRequest->rejection_reason }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Status Info for Tier 1 --}}
                    @if($action === 'tier1_approved')
                        <tr>
                            <td style="padding: 0 40px 30px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                    style="background-color: #eff6ff; border-radius: 6px; border: 1px solid #bfdbfe;">
                                    <tr>
                                        <td style="padding: 16px;">
                                            <p
                                                style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #1e40af;">
                                                ℹ️ Informasi Status:</p>
                                            <p style="margin: 0; font-size: 14px; color: #1e3a5f; line-height: 1.5;">
                                                Cuti Anda telah mendapat persetujuan atasan. Selanjutnya akan divalidasi oleh <strong>Manager HR</strong> untuk administrasi kepegawaian.
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Action Button --}}
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $leaveUrl }}" target="_blank"
                                            style="display: inline-block; padding: 14px 40px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; background-color: #1E40AF; border-radius: 6px;">
                                            @if(in_array($action, ['submitted', 'pending_hr']))
                                                Review Pengajuan Cuti
                                            @else
                                                Lihat Detail Cuti
                                            @endif
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="padding: 20px 40px; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6;">
                                Email ini dikirim secara otomatis oleh sistem <strong>ConfirmMe</strong>. Jangan
                                membalas email ini.
                            </p>
                            <p style="margin: 12px 0 0; font-size: 11px; color: #9ca3af;">
                                © {{ date('Y') }} PT INFO SOLUSINDO DATA UTAMA - ConfirmMe Document Approval System
                            </p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>
