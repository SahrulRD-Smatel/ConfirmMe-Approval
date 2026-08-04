<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>[ConfirmMe] User Baru Mendaftar</title>
</head>

<body style="margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f3f4f6;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f3f4f6;">
        <tr>
            <td style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600"
                    style="margin: 0 auto; background-color: #ffffff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">

                    {{-- Header with Logo --}}
                    <tr>
                        <td style="padding: 30px 40px; border-bottom: 3px solid #F59E0B;">
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
                                        <span style="display: inline-block; padding: 6px 14px; background-color: #F59E0B; color: #ffffff; font-size: 12px; font-weight: 600; border-radius: 20px;">
                                            USER BARU
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
                                Yth. {{ $admin->name }},
                            </p>
                        </td>
                    </tr>

                    {{-- Body --}}
                    <tr>
                        <td style="padding: 10px 40px 24px;">
                            <p style="margin: 0; font-size: 15px; color: #4b5563; line-height: 1.7;">
                                Ada user baru yang mendaftar di sistem <strong>ConfirmMe</strong> dan membutuhkan persetujuan Anda untuk aktivasi serta penugasan role.
                            </p>
                        </td>
                    </tr>

                    {{-- New User Info Card --}}
                    <tr>
                        <td style="padding: 0 40px 24px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                style="background-color: #fffbeb; border-radius: 8px; border: 1px solid #fde68a;">
                                <tr>
                                    <td style="padding: 20px;">
                                        <p style="margin: 0 0 12px; font-size: 13px; font-weight: 700; color: #92400e; text-transform: uppercase; letter-spacing: 0.05em;">
                                            Informasi User Baru
                                        </p>
                                        <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 14px; color: #6b7280; width: 120px;">Nama</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #111827; font-weight: 600;">{{ $newUser->name }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Email</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #111827; font-weight: 600;">{{ $newUser->email }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Waktu Daftar</td>
                                                <td style="padding: 6px 0; font-size: 14px; color: #111827; font-weight: 600;">{{ $newUser->created_at->format('d M Y, H:i') }} WIB</td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Status</td>
                                                <td style="padding: 6px 0;">
                                                    <span style="display: inline-block; padding: 3px 10px; background-color: #fef3c7; color: #92400e; font-size: 12px; font-weight: 600; border-radius: 12px;">
                                                        Menunggu Aktivasi
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding: 6px 0; font-size: 14px; color: #6b7280;">Role</td>
                                                <td style="padding: 6px 0;">
                                                    <span style="display: inline-block; padding: 3px 10px; background-color: #fee2e2; color: #991b1b; font-size: 12px; font-weight: 600; border-radius: 12px;">
                                                        Belum Ditugaskan
                                                    </span>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- What to do --}}
                    <tr>
                        <td style="padding: 0 40px 24px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%"
                                style="background-color: #f0f9ff; border-radius: 8px; border: 1px solid #bae6fd;">
                                <tr>
                                    <td style="padding: 16px 20px;">
                                        <p style="margin: 0 0 8px; font-size: 13px; font-weight: 600; color: #0369a1;">
                                            Yang Perlu Dilakukan:
                                        </p>
                                        <p style="margin: 0; font-size: 14px; color: #0c4a6e; line-height: 1.6;">
                                            1. Buka halaman <strong>Manajemen User</strong><br>
                                            2. Aktivasi akun user baru<br>
                                            3. Tugaskan role dan departemen yang sesuai
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Action Button --}}
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                                <tr>
                                    <td style="text-align: center;">
                                        <a href="{{ $usersUrl }}" target="_blank"
                                            style="display: inline-block; padding: 14px 40px; font-size: 15px; font-weight: 600; color: #ffffff; text-decoration: none; background-color: #82181a; border-radius: 6px;">
                                            Buka Manajemen User
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- Footer --}}
                    <tr>
                        <td style="padding: 20px 40px; background-color: #f9fafb; border-top: 1px solid #e5e7eb; border-radius: 0 0 8px 8px;">
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
