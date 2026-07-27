<x-app-layout>
    <x-slot name="title">Laporan</x-slot>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-down">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Laporan & Statistik</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan performa dan aktivitas dokumen
                </p>
            </div>

            <div class="flex flex-wrap gap-2">
                <x-ui.button href="{{ route('reports.documents') }}" variant="secondary" icon="document-text"
                    class="hover:scale-105 transition-transform">
                    Laporan Dokumen
                </x-ui.button>
                <x-ui.button href="{{ route('reports.finance') }}" variant="secondary" icon="banknotes"
                    class="hover:scale-105 transition-transform">
                    Laporan Keuangan
                </x-ui.button>
                <x-ui.button href="{{ route('reports.export') }}" variant="secondary" icon="arrow-down-tray"
                    class="hover:scale-105 transition-transform">
                    Export PDF
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div
            class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-4 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-indigo-100 text-xs font-medium uppercase tracking-wider">Total Dokumen</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['total']) }}</p>
                </div>
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="document-text" class="w-6 h-6 text-white" />
                </div>
            </div>
            <div class="mt-4 text-xs text-indigo-100 flex items-center gap-1">
                <span
                    class="bg-white/20 px-1.5 py-0.5 rounded text-white font-semibold">+{{ $stats['this_month'] }}</span>
                <span>bulan ini</span>
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-green-100 text-xs font-medium uppercase tracking-wider">Disetujui</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['approved']) }}</p>
                </div>
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="check-circle" class="w-6 h-6 text-white" />
                </div>
            </div>
            <div class="mt-4 text-xs text-green-100">
                {{ $stats['total'] > 0 ? round(($stats['approved'] / $stats['total']) * 100) : 0 }}% completion rate
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-red-500 to-pink-600 rounded-xl p-4 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-red-100 text-xs font-medium uppercase tracking-wider">Ditolak</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['rejected']) }}</p>
                </div>
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="x-circle" class="w-6 h-6 text-white" />
                </div>
            </div>
            <div class="mt-4 text-xs text-red-100">
                Action required
            </div>
        </div>

        <div
            class="bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl p-4 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-yellow-100 text-xs font-medium uppercase tracking-wider">Pending</p>
                    <p class="text-3xl font-bold mt-1">{{ number_format($stats['pending']) }}</p>
                </div>
                <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                    <x-icon name="clock" class="w-6 h-6 text-white" />
                </div>
            </div>
            <div class="mt-4 text-xs text-yellow-100">
                Avg Process: {{ $stats['avg_days'] ?? '~' }} hari
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Main Chart -->
        <div
            class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Dokumen Departemen</h2>
            <div class="h-64">
                <canvas id="deptChart"></canvas>
            </div>
        </div>

        <!-- Secondary Chart -->
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Komposisi Dokumen</h2>
            <div class="h-64 flex items-center justify-center">
                <canvas id="typeChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activity Table -->
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Aktivitas Terbaru</h2>
            <span class="text-xs text-gray-500">5 Dokumen Terakhir</span>
        </div>

        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-l-lg">Dokumen</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">User</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-r-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition duration-150 group cursor-pointer"
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
                            <td colspan="4" class="px-4 py-8 text-center text-gray-500 italic">Belum ada aktivitas dokumen.
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