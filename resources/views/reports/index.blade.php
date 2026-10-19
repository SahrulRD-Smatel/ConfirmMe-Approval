<x-app-layout>
    <x-slot name="title">{{ __('Laporan') }}</x-slot>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-down">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Laporan & Statistik') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Ringkasan performa dan aktivitas dokumen') }}
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-ui.button href="{{ route('reports.documents') }}" variant="secondary" icon="document-text"
                    class="hover:scale-105 transition-transform">
                    {{ __('Laporan Dokumen') }}
                </x-ui.button>
                <x-ui.button href="{{ route('reports.finance') }}" variant="secondary" icon="banknotes"
                    class="hover:scale-105 transition-transform">
                    {{ __('Laporan Keuangan') }}
                </x-ui.button>
                <x-ui.button href="{{ route('reports.export') }}" variant="secondary" icon="arrow-down-tray"
                    class="hover:scale-105 transition-transform">
                    {{ __('Export PDF') }}
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="mb-7 grid grid-cols-2 gap-4 md:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Total Dokumen') }}</p>
                    <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-white">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="p-2.5 bg-primary-50 dark:bg-primary-950/50 rounded-xl text-primary-600 dark:text-primary-400">
                    <x-icon name="document-text" class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                <span class="bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 px-1.5 py-0.5 rounded font-semibold">+{{ $stats['this_month'] }}</span>
                <span>{{ __('bulan ini') }}</span>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Disetujui') }}</p>
                    <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-white">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="p-2.5 bg-green-50 dark:bg-green-950/50 rounded-xl text-green-600 dark:text-green-400">
                    <x-icon name="check-circle" class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                <span class="font-medium text-green-600 dark:text-green-400">{{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0 }}%</span> {{ __('completion rate') }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Ditolak') }}</p>
                    <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-white">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="p-2.5 bg-red-50 dark:bg-red-950/50 rounded-xl text-red-600 dark:text-red-400">
                    <x-icon name="x-circle" class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Action required') }}
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Pending') }}</p>
                    <p class="text-3xl font-bold mt-1 text-gray-900 dark:text-white">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="p-2.5 bg-amber-50 dark:bg-amber-950/50 rounded-xl text-amber-600 dark:text-amber-400">
                    <x-icon name="clock" class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Avg Process') }}: {{ $stats['avg_days'] ?? '~' }} {{ __('hari') }}
            </div>
        </div>
    </div>

    <div class="mb-7 grid grid-cols-1 gap-5 lg:grid-cols-3">
        <!-- Main Chart -->
        <div
            class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-soft">
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Tren Dokumen Departemen') }}</h2>
            <div class="h-64">
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        <!-- Secondary Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-soft">
            <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Komposisi Dokumen') }}</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200/80 dark:border-gray-700/80 shadow-soft">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Aktivitas Terbaru') }}</h2>
            <span class="text-xs text-gray-500">{{ __('5 Dokumen Terakhir') }}</span>
        </div>

        <div class="overflow-hidden">
            <table class="min-w-[760px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-l-lg">{{ __('Dokumen') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('User') }}</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Waktu') }}</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-r-lg">{{ __('Status') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                        <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-700/50 transition duration-150 group cursor-pointer"
                            onclick="window.location='{{ route('documents.show', $doc) }}'">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white group-hover:text-primary-600">
                                {{ $doc->title }}
                                <div class="text-xs text-gray-400 font-normal">{{ $doc->document_number }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300">
                                <div class="flex items-center gap-2">
                                    <div
                                        class="w-6 h-6 rounded-full bg-gray-200 dark:bg-gray-600 flex items-center justify-center text-xs font-bold">
                                        {{ substr($doc->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="truncate max-w-[100px]">{{ $doc->user->name ?? '-' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-500 text-xs">
                                {{ $doc->created_at->diffForHumans() }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <x-ui.badge :status="$doc->status_color" size="sm">{{ $doc->status_label }}</x-ui.badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">{{ __('Belum ada aktivitas dokumen.') }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Data from Controller
            const deptLabels = @json($byDepartment->keys());
            const deptData = @json($byDepartment->values());

            const typeLabels = @json($byType->keys()->map(fn($t) => strtoupper($t)));
            const typeData = @json($byType->values());

            // Dept Chart (Bar)
            const deptCtx = document.getElementById('deptChart');
            if (deptCtx) {
                new Chart(deptCtx, {
                    type: 'bar',
                    data: {
                        labels: deptLabels,
                        datasets: [{
                            label: 'Jumlah Dokumen',
                            data: deptData,
                            backgroundColor: 'rgba(99, 102, 241, 0.7)',
                            borderColor: 'rgb(99, 102, 241)',
                            borderWidth: 1,
                            borderRadius: 6,
                            barThickness: 30
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 2000,
                            easing: 'easeOutQuart'
                        },
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Type Chart (Doughnut)
            const typeCtx = document.getElementById('typeChart');
            if (typeCtx) {
                new Chart(typeCtx, {
                    type: 'doughnut',
                    data: {
                        labels: typeLabels,
                        datasets: [{
                            data: typeData,
                            backgroundColor: [
                                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'
                            ],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        animation: {
                            animateScale: true,
                            animateRotate: true
                        },
                        plugins: {
                            legend: { position: 'bottom', labels: { usePointStyle: true, boxWidth: 8 } }
                        }
                    }
                });
            }
        });
    </script>

    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
    </style>
</x-app-layout>