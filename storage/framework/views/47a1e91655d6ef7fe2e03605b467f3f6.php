<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

     <?php $__env->slot('title', null, []); ?> Laporan Absensi <?php $__env->endSlot(); ?>

    <div class="space-y-7">
        
        <div class="card-elevated p-6 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-48 h-48 bg-gradient-to-bl from-amber-50/60 to-transparent rounded-full -mr-16 -mt-16 pointer-events-none"></div>
            <div class="relative z-10 flex flex-col gap-4">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900">Laporan Absensi</h1>
                        <p class="text-sm text-gray-500">Rekap absensi seluruh karyawan</p>
                    </div>

                    
                    <div class="flex flex-wrap items-center gap-2">
                        
                        <a href="<?php echo e(route('attendance-admin.export-pdf', ['month' => $month, 'year' => $year, 'department' => $department])); ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-red-600 text-white text-xs font-medium rounded-xl hover:bg-red-700 transition shadow-soft"
                           id="btn-export-pdf">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            PDF
                        </a>

                        
                        <a href="<?php echo e(route('attendance-admin.export-csv', ['month' => $month, 'year' => $year, 'department' => $department])); ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 text-white text-xs font-medium rounded-xl hover:bg-emerald-700 transition shadow-soft"
                           id="btn-export-csv">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            CSV
                        </a>

                        
                        <a href="<?php echo e(route('attendance-admin.export-xlsx', ['month' => $month, 'year' => $year, 'department' => $department])); ?>"
                           class="inline-flex items-center gap-1.5 px-3 py-2 bg-blue-600 text-white text-xs font-medium rounded-xl hover:bg-blue-700 transition shadow-soft"
                           id="btn-export-xlsx">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            XLSX
                        </a>

                        
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole(['admin', 'hrd'])): ?>
                            <button type="button"
                                    onclick="document.getElementById('import-modal').classList.remove('hidden')"
                                    class="inline-flex items-center gap-1.5 px-3 py-2 bg-amber-600 text-white text-xs font-medium rounded-xl hover:bg-amber-700 transition shadow-soft"
                                    id="btn-import">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import
                            </button>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>

                
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <select name="month" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($m = 1; $m <= 12; $m++): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($m); ?>" <?php echo e($month == $m ? 'selected' : ''); ?>>
                                <?php echo e(\Carbon\Carbon::create()->month($m)->translatedFormat('F')); ?>

                            </option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <select name="year" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php for($y = now()->year + 2; $y >= now()->year - 5; $y--): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($y); ?>" <?php echo e($year == $y ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endfor; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <select name="department" class="rounded-xl border-gray-300 text-sm focus:border-primary-500 focus:ring-4 focus:ring-primary-500/20/10">
                        <option value="">Semua Departemen</option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = $departments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dept): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <option value="<?php echo e($dept); ?>" <?php echo e($department == $dept ? 'selected' : ''); ?>><?php echo e($dept); ?></option>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-xl hover:bg-primary-700 transition">
                        Filter
                    </button>
                </form>
            </div>
        </div>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('import_summary')): ?>
            <?php $summary = session('import_summary'); ?>
            <div class="card-elevated overflow-hidden border border-green-200">
                <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                    <div class="flex items-center gap-2 text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="font-bold text-lg">Import Completed</h3>
                    </div>
                    <p class="text-green-100 text-xs mt-1"><?php echo e($summary['file_name'] ?? 'file'); ?> (<?php echo e($summary['file_size_kb'] ?? '0'); ?> KB)</p>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-4">
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-gray-900"><?php echo e(number_format($summary['total_rows'] ?? 0)); ?></div>
                            <div class="text-xs text-gray-500 mt-1 font-medium">Total Rows</div>
                        </div>
                        <div class="text-center p-3 bg-blue-50 rounded-xl">
                            <div class="text-2xl font-bold text-blue-700"><?php echo e(number_format($summary['new_records'] ?? 0)); ?></div>
                            <div class="text-xs text-blue-600 mt-1 font-medium">New Records</div>
                        </div>
                        <div class="text-center p-3 bg-amber-50 rounded-xl">
                            <div class="text-2xl font-bold text-amber-700"><?php echo e(number_format($summary['updated_records'] ?? 0)); ?></div>
                            <div class="text-xs text-amber-600 mt-1 font-medium">Updated Records</div>
                        </div>
                        <div class="text-center p-3 bg-gray-50 rounded-xl">
                            <div class="text-2xl font-bold text-gray-500"><?php echo e(number_format($summary['skipped_records'] ?? 0)); ?></div>
                            <div class="text-xs text-gray-400 mt-1 font-medium">Skipped Records</div>
                        </div>
                        <div class="text-center p-3 bg-red-50 rounded-xl">
                            <div class="text-2xl font-bold text-red-600"><?php echo e(number_format($summary['failed_records'] ?? 0)); ?></div>
                            <div class="text-xs text-red-500 mt-1 font-medium">Failed Records</div>
                        </div>
                    </div>

                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('import_errors') && count(session('import_errors')) > 0): ?>
                        <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-xl">
                            <p class="text-xs font-semibold text-amber-700 mb-2">Detail baris yang dilewati:</p>
                            <ul class="text-xs text-amber-600 list-disc list-inside space-y-0.5 max-h-32 overflow-y-auto">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <li><?php echo e($err); ?></li>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </ul>
                        </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('success')): ?>
            <div class="card-elevated p-4 border-l-4 border-green-500 bg-green-50">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-green-700 font-medium"><?php echo e(session('success')); ?></p>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('error')): ?>
            <div class="card-elevated p-4 border-l-4 border-red-500 bg-red-50">
                <div class="flex items-start gap-2">
                    <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm text-red-700 font-medium"><?php echo e(session('error')); ?></p>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(session('import_errors') && count(session('import_errors')) > 0 && !session('import_summary')): ?>
                            <ul class="mt-2 text-xs text-red-600 list-disc list-inside space-y-0.5 max-h-32 overflow-y-auto">
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__currentLoopData = session('import_errors'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $err): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                                    <li><?php echo e($err); ?></li>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            </ul>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

        
        <div class="card-elevated overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[1180px] w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-gray-600 text-xs uppercase tracking-wider">
                            <th class="px-6 py-3 text-left font-semibold">Karyawan</th>
                            <th class="px-4 py-3 text-center font-semibold">Departemen</th>
                            <th class="px-4 py-3 text-center font-semibold">Hadir</th>
                            <th class="px-4 py-3 text-center font-semibold">Tepat Waktu</th>
                            <th class="px-4 py-3 text-center font-semibold">Terlambat</th>
                            <th class="px-4 py-3 text-center font-semibold">WFH</th>
                            <th class="px-4 py-3 text-center font-semibold">Pulang Awal</th>
                            <th class="px-4 py-3 text-center font-semibold">Cuti Tahunan</th>
                            <th class="px-4 py-3 text-center font-semibold">Sakit</th>
                            <th class="px-4 py-3 text-center font-semibold">Tanpa Gaji</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::openLoop(); ?><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::startLoopIteration(); ?><?php endif; ?>
                            <tr class="hover:bg-primary-50/60 transition">
                                <td class="px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        <img src="<?php echo e($data['user']->avatar_url); ?>" class="w-8 h-8 rounded-full object-cover ring-2 ring-white shadow-soft">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo e($data['user']->name); ?></p>
                                            <p class="text-xs text-gray-500"><?php echo e($data['user']->position ?? '-'); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center text-gray-600"><?php echo e($data['user']->department ?? '-'); ?></td>
                                <td class="px-4 py-3 text-center font-semibold text-gray-900"><?php echo e($data['total_days']); ?></td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-700 font-bold text-xs">
                                        <?php echo e($data['on_time']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo e($data['late'] > 0 ? 'bg-orange-50 text-orange-700' : 'bg-gray-50 text-gray-400'); ?> font-bold text-xs">
                                        <?php echo e($data['late']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-xs">
                                        <?php echo e($data['wfh']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo e($data['early_leave'] > 0 ? 'bg-yellow-50 text-yellow-700' : 'bg-gray-50 text-gray-400'); ?> font-bold text-xs">
                                        <?php echo e($data['early_leave']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo e($data['leave_annual'] > 0 ? 'bg-purple-50 text-purple-700' : 'bg-gray-50 text-gray-400'); ?> font-bold text-xs">
                                        <?php echo e($data['leave_annual']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo e($data['leave_sick'] > 0 ? 'bg-pink-50 text-pink-700' : 'bg-gray-50 text-gray-400'); ?> font-bold text-xs">
                                        <?php echo e($data['leave_sick']); ?>

                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 rounded-full <?php echo e($data['leave_unpaid'] > 0 ? 'bg-gray-200 text-gray-700' : 'bg-gray-50 text-gray-400'); ?> font-bold text-xs">
                                        <?php echo e($data['leave_unpaid']); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::endLoop(); ?><?php endif; ?><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::closeLoop(); ?><?php endif; ?>
                            <tr>
                                <td colspan="10" class="px-6 py-12 text-center">
                                    <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-2">
                                        <?php if (isset($component)) { $__componentOriginalce262628e3a8d44dc38fd1f3965181bc = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon','data' => ['name' => 'users','class' => 'w-6 h-6 text-gray-300']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'users','class' => 'w-6 h-6 text-gray-300']); ?>
<?php \Livewire\Features\SupportCompiledWireKeys\SupportCompiledWireKeys::processComponentKey($component); ?>

<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $attributes = $__attributesOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__attributesOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc)): ?>
<?php $component = $__componentOriginalce262628e3a8d44dc38fd1f3965181bc; ?>
<?php unset($__componentOriginalce262628e3a8d44dc38fd1f3965181bc); ?>
<?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-400">Tidak ada data absensi</p>
                                </td>
                            </tr>
                        <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($reportData->hasPages()): ?>
                <div class="px-6 py-4 border-t border-gray-100">
                    <?php echo e($reportData->appends(request()->query())->links()); ?>

                </div>
            <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
        </div>
    </div>

    
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->user()->hasAnyRole(['admin', 'hrd'])): ?>
        <div id="import-modal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            
            <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"
                 onclick="document.getElementById('import-modal').classList.add('hidden')"></div>

            
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
                    
                    <div class="flex items-center justify-between p-6 border-b border-gray-100">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Import Data Absensi</h3>
                            <p class="text-sm text-gray-500 mt-0.5">Upload file CSV atau XLSX</p>
                        </div>
                        <button type="button"
                                onclick="document.getElementById('import-modal').classList.add('hidden')"
                                class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    
                    <form action="<?php echo e(route('attendance-admin.import')); ?>" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                        <?php echo csrf_field(); ?>

                        
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                            <div class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <div class="text-xs text-blue-700">
                                    <p class="font-semibold mb-1">Format kolom yang diharapkan:</p>
                                    <code class="text-[11px] bg-blue-100 px-1.5 py-0.5 rounded">employee_id, date, check_in_time, check_out_time, status, is_wfh, notes</code>
                                    <ul class="mt-2 space-y-0.5 text-blue-600">
                                        <li>• Status: <code class="bg-blue-100 px-1 rounded">on_time</code>, <code class="bg-blue-100 px-1 rounded">late</code>, <code class="bg-blue-100 px-1 rounded">wfh</code></li>
                                        <li>• Data yang sudah ada akan <strong>diperbarui/replace</strong></li>
                                        <li>• Format tanggal: <code class="bg-blue-100 px-1 rounded">YYYY-MM-DD</code></li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        
                        <a href="<?php echo e(route('attendance-admin.import-template')); ?>"
                           class="inline-flex items-center gap-1.5 text-sm text-primary-600 hover:text-primary-700 font-medium transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Template CSV
                        </a>

                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Pilih File</label>
                            <div class="relative">
                                <input type="file"
                                       name="file"
                                       accept=".csv,.xlsx,.xls"
                                       required
                                       id="import-file-input"
                                       class="block w-full text-sm text-gray-500
                                              file:mr-4 file:py-2.5 file:px-4
                                              file:rounded-xl file:border-0
                                              file:text-sm file:font-medium
                                              file:bg-primary-50 file:text-primary-700
                                              hover:file:bg-primary-100
                                              cursor-pointer border border-gray-200 rounded-xl
                                              focus:outline-none focus:ring-4 focus:ring-primary-500/20/10 focus:border-primary-500">
                            </div>
                            <p class="mt-1.5 text-xs text-gray-400">Format: CSV, XLSX — Maksimal 5MB</p>
                            <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__errorArgs = ['file'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <p class="mt-1 text-xs text-red-500"><?php echo e($message); ?></p>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                        </div>

                        
                        <div class="flex items-center justify-end gap-3 pt-2">
                            <button type="button"
                                    onclick="document.getElementById('import-modal').classList.add('hidden')"
                                    class="px-4 py-2.5 text-sm font-medium text-gray-700 bg-gray-100 rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-amber-600 rounded-xl hover:bg-amber-700 transition shadow-soft inline-flex items-center gap-2"
                                    id="btn-submit-import">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                </svg>
                                Import Data
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH E:\Dhani\JOB\ConfirmMe-Approval\resources\views/attendances/report.blade.php ENDPATH**/ ?>