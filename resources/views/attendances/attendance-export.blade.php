<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ __('Laporan Absensi') }} - {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}</title>
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

        .report-title {
            font-size: 16px;
            font-weight: bold;
            color: #111827;
            margin-top: 15px;
            margin-bottom: 5px;
        }

        .report-subtitle {
            font-size: 11px;
            color: #6b7280;
            margin-bottom: 15px;
        }

        .summary {
            background: #f9fafb;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }

        .summary h2 {
            font-size: 12px;
            margin-bottom: 10px;
            color: #991b1b;
        }

        .summary-grid {
            display: table;
            width: 100%;
        }

        .summary-item {
            display: table-cell;
            width: 20%;
            text-align: center;
            padding: 10px;
        }

        .summary-item .number {
            font-size: 20px;
            font-weight: bold;
            color: #111827;
        }

        .summary-item .label {
            font-size: 9px;
            color: #4b5563;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
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

        .text-center {
            text-align: center;
        }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
        }

        .badge-green { background: #dcfce7; color: #166534; }
        .badge-orange { background: #ffedd5; color: #9a3412; }
        .badge-blue { background: #dbeafe; color: #1e40af; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-purple { background: #f3e8ff; color: #6b21a8; }
        .badge-pink { background: #fce7f3; color: #9d174d; }
        .badge-gray { background: #e5e7eb; color: #374151; }

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
    {{-- Company Header --}}
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

    {{-- Report Title --}}
    <div class="report-title">{{ __('Laporan Absensi Karyawan') }}</div>
    <div class="report-subtitle">
        {{ __('Periode') }}: {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }} {{ $year }}
        @if($department)
            | {{ __('Departemen') }}: {{ $department }}
        @endif
    </div>

    {{-- Summary Statistics --}}
    <div class="summary">
        <h2>{{ __('Ringkasan') }}</h2>
        <div class="summary-grid">
            <div class="summary-item">
                <div class="number">{{ $totalStats['total_employees'] }}</div>
                <div class="label">{{ __('Total Karyawan') }}</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $totalStats['total_on_time'] }}</div>
                <div class="label">{{ __('Tepat Waktu') }}</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $totalStats['total_late'] }}</div>
                <div class="label">{{ __('Terlambat') }}</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $totalStats['total_wfh'] }}</div>
                <div class="label">{{ __('WFH') }}</div>
            </div>
            <div class="summary-item">
                <div class="number">{{ $totalStats['total_early_leave'] }}</div>
                <div class="label">{{ __('Pulang Awal') }}</div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>{{ __('Nama Karyawan') }}</th>
                <th>{{ __('Employee ID') }}</th>
                <th>{{ __('Departemen') }}</th>
                <th>{{ __('Posisi') }}</th>
                <th class="text-center">{{ __('Hadir') }}</th>
                <th class="text-center">{{ __('Tepat Waktu') }}</th>
                <th class="text-center">{{ __('Terlambat') }}</th>
                <th class="text-center">{{ __('WFH') }}</th>
                <th class="text-center">{{ __('Pulang Awal') }}</th>
                <th class="text-center">{{ __('Cuti Tahunan') }}</th>
                <th class="text-center">{{ __('Sakit') }}</th>
                <th class="text-center">{{ __('Tanpa Gaji') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reportData as $index => $data)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $data['user']->name }}</td>
                    <td>{{ $data['user']->employee_id ?? '-' }}</td>
                    <td>{{ $data['user']->department ?? '-' }}</td>
                    <td>{{ $data['user']->position ?? '-' }}</td>
                    <td class="text-center"><strong>{{ $data['total_days'] }}</strong></td>
                    <td class="text-center">
                        <span class="badge badge-green">{{ $data['on_time'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-orange">{{ $data['late'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-blue">{{ $data['wfh'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-yellow">{{ $data['early_leave'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-purple">{{ $data['leave_annual'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-pink">{{ $data['leave_sick'] }}</span>
                    </td>
                    <td class="text-center">
                        <span class="badge badge-gray">{{ $data['leave_unpaid'] }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="13" class="text-center" style="padding: 20px;">
                        {{ __('Tidak ada data absensi untuk periode ini.') }}
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Footer --}}
    <div class="footer">
        <p>{{ __('Dokumen ini dicetak secara otomatis oleh sistem ConfirmMe pada') }} {{ now()->translatedFormat('d F Y, H:i') }} WIB</p>
        <p style="margin-top: 5px;">© {{ date('Y') }} PT. Info Solusindo Data Utama — Confidential</p>
    </div>
</body>

</html>
