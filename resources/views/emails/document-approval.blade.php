<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subject }}</title>
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
                                            {{ $templateId ?? 'NOTIFICATION' }}
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

                    {{-- Document Details Card --}}
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                style="background-color: #f9fafb; border-radius: 8px; border: 1px solid #e5e7eb;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p
                                            style="margin: 0 0 12px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">
                                            {{ __("Detail Dokumen") }}</p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                            width="100%">
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">{{ __('Nomor') }}:</span>
                                                    <span
                                                        style="font-size: 14px; font-weight: 600; color: #111827; float: right;">{{ $document->document_number }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">{{ __('Judul') }}:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ Str::limit($document->title, 40) }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">{{ __('Jenis') }}:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $document->approval_type_label }}</span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                    <span style="font-size: 13px; color: #6b7280;">{{ __('Pemohon') }}:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $document->user->name ?? 'Unknown' }}</span>
                                                </td>
                                            </tr>
                                            @if($document->estimated_budget)
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                        <span style="font-size: 13px; color: #6b7280;">{{ __('Total Pengajuan') }}:</span>
                                                        @php
                                                            $financeApproval = $document->approvals()
                                                                ->where('status', 'approved')
                                                                ->where('approver_role', 'finance')
                                                                ->whereNotNull('approved_amount')
                                                                ->first();
                                                            $budgetChanged = $financeApproval && (float) $financeApproval->approved_amount !== (float) $document->estimated_budget;
                                                        @endphp
                                                        <span
                                                            style="font-size: 14px; font-weight: 700; float: right; {{ $budgetChanged ? 'text-decoration: line-through; color: #9ca3af;' : 'color: #1E40AF;' }}">Rp
                                                            {{ number_format($document->estimated_budget, 0, ',', '.') }}</span>
                                                    </td>
                                                </tr>
                                                @if($budgetChanged)
                                                    <tr>
                                                        <td
                                                            style="padding: 8px 0; border-bottom: 1px solid #e5e7eb; background-color: #fefce8;">
                                                            <span style="font-size: 13px; color: #854d0e; font-weight: 600;">
                                                                {{ __('Total Disetujui (Finance)') }}:</span>
                                                            <span
                                                                style="font-size: 15px; font-weight: 700; color: #1E40AF; float: right;">Rp
                                                                {{ number_format($financeApproval->approved_amount, 0, ',', '.') }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endif
                                            @if($document->metadata && isset($document->metadata['vendor_name']))
                                                <tr>
                                                    <td style="padding: 8px 0; border-bottom: 1px solid #e5e7eb;">
                                                        <span style="font-size: 13px; color: #6b7280;">Vendor:</span>
                                                        <span
                                                            style="font-size: 14px; color: #111827; float: right;">{{ $document->metadata['vendor_name'] }}</span>
                                                    </td>
                                                </tr>
                                            @endif
                                            <tr>
                                                <td style="padding: 8px 0;">
                                                    <span style="font-size: 13px; color: #6b7280;">{{ __('Prioritas') }}:</span>
                                                    <span
                                                        style="font-size: 14px; color: #111827; float: right;">{{ $document->priority_label }}</span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Previous Approvers (Riwayat) --}}
                    @if(count($previousApprovers) > 0)
                        <tr>
                            <td style="padding: 0 40px 20px;">
                                <p
                                    style="margin: 0 0 10px; font-size: 13px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">
                                    {{ __("Riwayat Persetujuan") }}</p>
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                    style="background-color: #f0fdf4; border-radius: 6px; border: 1px solid #bbf7d0;">
                                    @foreach($previousApprovers as $index => $approver)
                                        <tr>
                                            <td
                                                style="padding: 10px 14px; {{ !$loop->last ? 'border-bottom: 1px solid #bbf7d0;' : '' }}">
                                                <table role="presentation" cellspacing="0" cellpadding="0" border="0"
                                                    width="100%">
                                                    <tr>
                                                        <td style="width: 24px; vertical-align: top;">
                                                            <span
                                                                style="display: inline-block; width: 20px; height: 20px; background-color: #22c55e; color: white; font-size: 11px; text-align: center; line-height: 20px; border-radius: 50%;">&#10003;</span>
                                                        </td>
                                                        <td style="vertical-align: middle;">
                                                            <span
                                                                style="font-size: 14px; color: #111827; font-weight: 500;">{{ $approver['name'] }}</span>
                                                            <span style="font-size: 12px; color: #6b7280;">
                                                                ({{ $approver['role'] }})</span>
                                                        </td>
                                                        <td style="text-align: right; vertical-align: middle;">
                                                            <span
                                                                style="font-size: 11px; color: #6b7280;">{{ $approver['approved_at'] }}</span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    @endforeach
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Action Button --}}
                    @if($approvalUrl)
                        <tr>
                            <td style="padding: 0 40px 30px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                    <tr>
                                        <td style="text-align: center;">
                                            <a href="{{ $approvalUrl }}" target="_blank"
                                                style="display: inline-block; padding: 14px 40px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; background-color: #1E40AF; border-radius: 6px;">
                                                @if($action === 'pending_approval')
                                                    {{ __("Buka Aplikasi ConfirmMe") }}
                                                @else
                                                    {{ __("Lihat Detail Dokumen") }}
                                                @endif
                                            </a>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Rejection Reason --}}
                    @if($action === 'rejected' && $document->rejection_reason)
                        <tr>
                            <td style="padding: 0 40px 30px;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                    style="background-color: #fef2f2; border-radius: 6px; border: 1px solid #fecaca;">
                                    <tr>
                                        <td style="padding: 16px;">
                                            <p style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #dc2626;">
                                                {{ __("Alasan Penolakan") }}:</p>
                                            <p style="margin: 0; font-size: 14px; color: #7f1d1d; line-height: 1.5;">
                                                {{ $document->rejection_reason }}
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                    @endif

                    {{-- Footer --}}
                    <tr>
                        <td
                            style="padding: 20px 40px; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
                            <p style="margin: 0; font-size: 12px; color: #6b7280; line-height: 1.6;">
                                {{ __("Email ini dikirim secara otomatis oleh sistem ConfirmMe. Jangan membalas email ini.") }}
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