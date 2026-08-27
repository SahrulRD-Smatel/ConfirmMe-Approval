<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Permintaan Pembelian Barang</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            font-size: 11px;
            line-height: 1.5;
            color: #000;
        }

        .container {
            padding: 30px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .header h1 {
            font-size: 14px;
            font-weight: bold;
            text-decoration: underline;
        }
        
        .header h2 {
            font-size: 12px;
            font-weight: bold;
            margin-top: 5px;
        }
        
        .meta-info {
            width: 100%;
            margin-bottom: 15px;
        }
        
        .meta-info td {
            padding: 2px 5px;
        }
        
        /* Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            font-size: 10px;
        }

        .items-table th, .items-table td {
            border: 1px solid #000;
            padding: 5px;
        }

        .items-table th {
            background-color: #ffeb3b; /* Yellow header from screenshot */
            font-weight: bold;
            text-align: center;
            vertical-align: middle;
        }
        
        .items-table .center {
            text-align: center;
        }
        
        .items-table .right {
            text-align: right;
        }

        /* Signatures */
        .signature-section {
            display: table;
            width: 100%;
            margin-top: 40px;
            text-align: center;
            font-size: 11px;
        }

        .sig-col {
            display: table-cell;
            width: 25%;
            vertical-align: top;
        }
        
        .sig-title {
            margin-bottom: 60px;
            font-weight: bold;
        }
        
        .sig-name {
            display: inline-block;
            border-bottom: 1px solid #000;
            min-width: 120px;
            padding-bottom: 2px;
            font-weight: bold;
        }

    </style>
</head>

<body>
    @php
        $fields = $document->metadata ?? [];
        $items = $fields['items'] ?? [];
        $kategori = $fields['kategori_pembelian'] ?? 'ALAT TULIS KANTOR';
        
        $approvals = $document->approvals()->with('approver')->orderBy('approval_order')->get();
        // GA (Pemohon)
        $ga = $approvals->filter(fn($a) => $a->approver_role === 'role:ga')->first();
        // Manager HRGA
        $manager = $approvals->filter(fn($a) => $a->approver_role === 'role:manager_hrga')->first();
        // Direktur HR
        $dirHr = $approvals->filter(fn($a) => $a->approver_role === 'role:direktur_hr')->first();
        // Direktur Fin
        $dirFin = $approvals->filter(fn($a) => $a->approver_role === 'role:direktur_finance')->first();
        
        $totalHarga = 0;
    @endphp

    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>PT. INFO SOLUSINDO DATA UTAMA</h1>
            <h2>PERMINTAAN PEMBELIAN BARANG {{ strtoupper($kategori) }}</h2>
        </div>
        
        <table class="meta-info">
            <tr>
                <td style="width: 120px;">No. Dokumen</td>
                <td style="width: 10px;">:</td>
                <td>{{ $document->document_number }}</td>
                
                <td style="width: 150px; text-align: right;">Tanggal Request</td>
                <td style="width: 10px;">:</td>
                <td style="width: 100px;">{{ $document->created_at->format('d M Y') }}</td>
            </tr>
            <tr>
                <td>Keterangan</td>
                <td>:</td>
                <td colspan="4">{{ $fields['keterangan'] ?? '-' }}</td>
            </tr>
        </table>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 5%;">NO</th>
                    <th style="width: 25%;">NAMA BARANG</th>
                    <th style="width: 15%;">KETERANGAN</th>
                    <th style="width: 10%;">MAX PERBULAN</th>
                    <th style="width: 10%;">SAAT INI<br>(STOCK)</th>
                    <th style="width: 10%;">PERMINTAAN<br>(QTY)</th>
                    <th style="width: 12%;">HARGA PERK / ITEM</th>
                    <th style="width: 13%;">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                @forelse($items as $index => $item)
                    @php
                        $qty = intval($item['Permintaan (Qty)'] ?? 0);
                        // Clean price formatting (remove RP, commas, dots)
                        $rawPrice = preg_replace('/[^0-9]/', '', $item['Harga Perk./Item'] ?? '0');
                        $price = floatval($rawPrice);
                        $total = $qty * $price;
                        $totalHarga += $total;
                    @endphp
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $item['Nama Barang'] ?? '-' }}</td>
                    <td>{{ $item['Keterangan'] ?? '-' }}</td>
                    <td class="center">{{ $item['Max Perbulan'] ?? '-' }}</td>
                    <td class="center">{{ $item['Stock Saat Ini'] ?? '-' }}</td>
                    <td class="center">{{ $item['Permintaan (Qty)'] ?? '-' }}</td>
                    <td class="right">Rp {{ number_format($price, 0, ',', '.') }}</td>
                    <td class="right">Rp {{ number_format($total, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="center" style="padding: 20px;">Tidak ada rincian barang.</td>
                </tr>
                @endforelse
                
                <!-- Total Row -->
                <tr>
                    <td colspan="7" class="right" style="font-weight: bold;">TOTAL KESELURUHAN</td>
                    <td class="right" style="font-weight: bold;">Rp {{ number_format($totalHarga, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
        
        <!-- Signatures -->
        <div class="signature-section">
            <div class="sig-col">
                <div class="sig-title">Yang Mengajukan</div>
                <div class="sig-name">{{ $ga ? $ga->approver->name : $document->user->name }}</div>
                <div>General Affair</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Mengetahui</div>
                <div class="sig-name">{{ $manager ? $manager->approver->name : '................................' }}</div>
                <div>Manager HRGA & GA</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Disetujui 1</div>
                <div class="sig-name">{{ $dirHr ? $dirHr->approver->name : '................................' }}</div>
                <div>Direktur HRGA & GA</div>
            </div>
            <div class="sig-col">
                <div class="sig-title">Disetujui 2</div>
                <div class="sig-name">{{ $dirFin ? $dirFin->approver->name : '................................' }}</div>
                <div>Direktur Keuangan</div>
            </div>
        </div>
    </div>
</body>
</html>
