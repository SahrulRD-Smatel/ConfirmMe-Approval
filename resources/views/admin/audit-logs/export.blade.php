<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Audit Log - {{ now()->format('d M Y') }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            color: #111827;
            padding: 0px 40px;
        }

        .header {
            text-align: left;
            margin-bottom: 20px;
            padding-bottom: 0px;
        }

        .meta {
            margin-bottom: 20px;
            font-size: 10px;
            color: #4b5563;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px 6px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background-color: #991b1b;
            color: white;
            font-weight: bold;
            font-size: 10px;
            text-transform: uppercase;
        }

        td {
            font-size: 10px;
        }

        tr:nth-child(even) {
            background: #f9fafb;
        }

        .event-badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .event-created { background: #dcfce7; color: #166534; }
        .event-updated { background: #dbeafe; color: #1e40af; }
        .event-deleted { background: #fee2e2; color: #991b1b; }
        .event-approved { background: #dcfce7; color: #166534; }
        .event-rejected { background: #fee2e2; color: #991b1b; }
        .event-submitted { background: #fef3c7; color: #92400e; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }
        
        @media print {
            body { padding: 20px; }
        }
    </style>
</head>

<body>
    <div class="header">
        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 0;">
            <tr>
                <td style="width: 90px; vertical-align: middle; padding-right: 10px; border: none;">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ public_path('images/logo.png') }}" alt="Logo" style="height: 85px; width: auto;">
                    @endif
                </td>
                <td style="vertical-align: middle; border: none;">
                    <div style="font-size: 22px; font-weight: bold; color: #111827; line-height: 1.2; margin-bottom: 4px;">
                        PT. Info Solusindo Data Utama
                    </div>
                    <div style="font-size: 10px; color: #4b5563; line-height: 1.5;">
                        Gedung Menara Hijau | Lantai 9 Suite 903B |<br>
                        Jl. MT. Haryono, Kav. 33 | Pancoran, Jakarta Selatan 12770, Indonesia<br>
                        Ph. +6221 7986 172 | Fax. +6221 7986 143
                    </div>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 3px solid #991b1b; margin-top: 10px;"></div>
    </div>

    <div style="text-align: center; margin: 15px 0;">
        <h1 style="font-size: 18px; color: #991b1b; text-transform: uppercase;">{{ __('Audit Log Sistem') }}</h1>
    </div>
    
    <div class="meta">
        <p>{{ __('Tanggal Cetak') }}: {{ now()->format('d M Y H:i') }} | {{ __('Dicetak Oleh') }}: System</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">{{ __('Waktu') }}</th>
                <th style="width: 12%;">{{ __('Event') }}</th>
                <th style="width: 15%;">{{ __('User') }}</th>
                <th style="width: 20%;">{{ __('Model') }}</th>
                <th style="width: 38%;">{{ __('Deskripsi') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                    <td>
                        <span class="event-badge event-{{ $log->event }}">{{ $log->event }}</span>
                    </td>
                    <td><strong style="color: #111827;">{{ $log->user->name ?? 'System' }}</strong></td>
                    <td>{{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}</td>
                    <td>{{ Str::limit($log->description ?? '-', 80) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">{{ __('Tidak ada data audit log') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>{{ __('Total') }}: {{ $logs->count() }} {{ __('log entries') }}</p>
        <p>{{ __('Dokumen ini digenerate secara otomatis oleh sistem ConfirmMe') }}</p>
        <p>Ac {{ date('Y') }} PT. Info Solusindo Data Utama</p>
    </div>
</body>

</html>