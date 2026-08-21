<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Audit Log - <?php echo e(now()->format('d M Y')); ?></title>
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
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(file_exists(public_path('images/logo.png'))): ?>
                        <img src="<?php echo e(public_path('images/logo.png')); ?>" alt="Logo" style="height: 85px; width: auto;">
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
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
        <h1 style="font-size: 18px; color: #991b1b; text-transform: uppercase;">Audit Log Sistem</h1>
    </div>
    
    <div class="meta">
        <p>Tanggal Cetak: <?php echo e(now()->format('d M Y H:i')); ?> | Dicetak Oleh: System</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Waktu</th>
                <th style="width: 12%;">Event</th>
                <th style="width: 15%;">User</th>
                <th style="width: 20%;">Model</th>
                <th style="width: 38%;">Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                <tr>
                    <td><?php echo e($log->created_at->format('d/m/Y H:i:s')); ?></td>
                    <td>
                        <span class="event-badge event-<?php echo e($log->event); ?>"><?php echo e($log->event); ?></span>
                    </td>
                    <td><strong style="color: #111827;"><?php echo e($log->user->name ?? 'System'); ?></strong></td>
                    <td><?php echo e(class_basename($log->auditable_type)); ?> #<?php echo e($log->auditable_id); ?></td>
                    <td><?php echo e(Str::limit($log->description ?? '-', 80)); ?></td>
                </tr>
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                <tr>
                    <td colspan="5" style="text-align: center; padding: 20px;">Tidak ada data audit log</td>
                </tr>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </tbody>
    </table>

    <div class="footer">
        <p>Total: <?php echo e($logs->count()); ?> log entries</p>
        <p>Dokumen ini digenerate secara otomatis oleh sistem ConfirmMe</p>
        <p>Ac <?php echo e(date('Y')); ?> PT. Info Solusindo Data Utama</p>
    </div>
</body>

</html><?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/admin/audit-logs/export.blade.php ENDPATH**/ ?>