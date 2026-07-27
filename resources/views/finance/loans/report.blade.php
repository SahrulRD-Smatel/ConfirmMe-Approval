<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laporan Piutang Karyawan - {{ config('app.name', 'Laravel') }}</title>

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

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .badge {
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-pending { background-color: #fef3c7; color: #92400e; }
        .badge-disbursed { background-color: #dbeafe; color: #1e40af; }
        .badge-repaying { background-color: #fef3c7; color: #92400e; }
        .badge-paid { background-color: #dcfce7; color: #166534; }

        .summary {
            margin-top: 20px;
            float: right;
            width: 300px;
            font-size: 10px;
            color: #4b5563;
        }

        @media print {
            .no-print { display: none; }
            body { padding: 20px; }
        }

        .print-btn {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 8px 16px;
            background: #b91c1c;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            z-index: 1000;
            font-weight: bold;
            font-family: inherit;
        }
        .print-btn:hover { background: #991b1b; }
    </style>
</head>

<body>
    <button onclick="window.print()" class="no-print print-btn">Cetak Laporan</button>

    <div class="header">
        <table style="width: 100%; border-collapse: collapse; border: none; margin-bottom: 0;">
            <tr>
                <td style="width: 90px; vertical-align: middle; padding-right: 10px; border: none;">
                    @if(file_exists(public_path('images/logo.png')))
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height: 85px; width: auto;">
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
        <h1 style="font-size: 18px; color: #991b1b; text-transform: uppercase;">Laporan Piutang Karyawan</h1>
    </div>

    <div class="meta">
        <p>Tanggal Cetak: {{ now()->format('d M Y H:i') }} | Dicetak Oleh: {{ auth()->user()->name }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 15%;">No. Pinjaman</th>
                <th style="width: 20%;">Karyawan</th>
                <th style="width: 15%;">Tanggal Cair</th>
                <th class="text-right" style="width: 15%;">Total Pinjaman</th>
                <th class="text-right" style="width: 10%;">Sudah Dibayar</th>
                <th class="text-right" style="width: 10%;">Sisa Hutang</th>
                <th class="text-center" style="width: 10%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($loans as $index => $loan)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $loan->loan_number }}</td>
                    <td>
                        <strong style="color: #111827;">{{ $loan->employee->name }}</strong><br>
                        <span style="font-size: 9px; color: #6b7280;">{{ $loan->employee->department }}</span>
                    </td>
                    <td>{{ $loan->disbursement_date ? \Carbon\Carbon::parse($loan->disbursement_date)->format('d M Y') : '-' }}</td>
                    <td class="text-right">Rp {{ number_format($loan->loan_amount, 0, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($loan->total_paid, 0, ',', '.') }}</td>
                    <td class="text-right"><strong style="color: #111827;">Rp {{ number_format($loan->outstanding_amount, 0, ',', '.') }}</strong></td>
                    <td class="text-center">
                        <?php
                            $badgeClass = match ($loan->status) {
                                'pending_disbursement' => 'badge-pending',
                                'disbursed' => 'badge-disbursed',
                                'repaying' => 'badge-repaying',
                                'paid_off' => 'badge-paid',
                                default => ''
                            };
                            $statusLabel = match ($loan->status) {
                                'pending_disbursement' => 'Menunggu Cair',
                                'disbursed' => 'Dicairkan',
                                'repaying' => 'Sedang Nyicil',
                                'paid_off' => 'Lunas',
                                default => $loan->status
                            };
                        ?>
                        <span class="badge {{ $badgeClass }}">{{ $statusLabel }}</span>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center" style="padding: 20px;">Tidak ada data piutang.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr style="background-color: #f3f4f6;">
                <td colspan="6" class="text-right" style="font-weight: bold; color: #111827; border-top: 2px solid #e5e7eb;">Total Piutang Karyawan:</td>
                <td class="text-right" style="font-weight: bold; color: #991b1b; border-top: 2px solid #e5e7eb;">Rp {{ number_format($totalReceivables, 0, ',', '.') }}</td>
                <td style="border-top: 2px solid #e5e7eb;"></td>
            </tr>
        </tfoot>
    </table>

    <div class="summary no-print">
        <p><i>Catatan: Laporan ini hanya menampilkan pinjaman dengan status aktif/belum lunas, kecuali difilter lain.</i></p>
    </div>
</body>

</html>