<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Laporan Dokumen - {{ now()->format('d M Y') }}</title>
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
            width: 25%;
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

        .status {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .status-approved { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-draft { background: #dbeafe; color: #1e40af; }

        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 9px;
            color: #6b7280;
        }

        .page-break {
            page-break-after: always;
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
        <h1 style="font-size: 18px; color: #991b1b; text-transform: uppercase;">Laporan Dokumen</h1>
        <p style="font-size: 10px; color: #4b5563;">Dicetak pada {{ now()->format('d F Y H:i') }}</p>
    </div>

    <div class="summary">
        <h2>Ringkasan</h2>
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none; width: 25%; text-align: center; background: transparent;">
                    <div style="font-size: 18px; font-weight: bold; color: #991b1b;">{{ $documents->count() }}</div>
                    <div style="font-size: 9px; color: #4b5563; text-transform: uppercase;">Total Dokumen</div>
                </td>
                <td style="border: none; width: 25%; text-align: center; background: transparent;">
                    <div style="font-size: 18px; font-weight: bold; color: #166534;">
                        {{ $documents->where('status', 'approved')->count() }}
                    </div>
                    <div style="font-size: 9px; color: #4b5563; text-transform: uppercase;">Disetujui</div>
                </td>
                <td style="border: none; width: 25%; text-align: center; background: transparent;">
                    <div style="font-size: 18px; font-weight: bold; color: #991b1b;">
                        {{ $documents->where('status', 'rejected')->count() }}
                    </div>
                    <div style="font-size: 9px; color: #4b5563; text-transform: uppercase;">Ditolak</div>
                </td>
                <td style="border: none; width: 25%; text-align: center; background: transparent;">
                    <div style="font-size: 18px; font-weight: bold; color: #92400e;">
                        {{ $documents->whereIn('status', ['pending', 'draft'])->count() }}
                    </div>
                    <div style="font-size: 9px; color: #4b5563; text-transform: uppercase;">Pending</div>
                </td>
            </tr>
        </table>
    </div>

    <h3 style="font-size: 12px; margin-bottom: 10px; color: #991b1b; text-transform: uppercase;">Daftar Dokumen</h3>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">No. Dokumen</th>
                <th style="width: 25%;">Judul</th>
                <th style="width: 12%;">Pengaju</th>
                <th style="width: 12%;">Jenis</th>
                <th style="width: 10%;">Status</th>
                <th style="width: 13%; text-align: right;">Anggaran</th>
                <th style="width: 13%; text-align: center;">Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($documents as $doc)
                <tr>
                    <td>{{ $doc->document_number }}</td>
                    <td><strong style="color: #111827;">{{ Str::limit($doc->title, 40) }}</strong></td>
                    <td>{{ $doc->user->name ?? '-' }}</td>
                    <td>{{ $doc->approval_type_label }}</td>
                    <td>
                        <span class="status status-{{ $doc->status }}">{{ $doc->status_label }}</span>
                    </td>
                    <td style="text-align: right;">
                        @if($doc->approved_budget)
                            Rp {{ number_format($doc->approved_budget, 0, ',', '.') }}
                        @elseif($doc->estimated_budget)
                            Rp {{ number_format($doc->estimated_budget, 0, ',', '.') }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="text-align: center;">{{ $doc->created_at->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Dokumen ini digenerate secara otomatis oleh sistem ConfirmMe</p>
        <p>Ac {{ date('Y') }} PT. Info Solusindo Data Utama</p>
    </div>
</body>

</html>