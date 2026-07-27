<x-app-layout>
    <x-slot name="title">Executive Dashboard</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
                    Executive Overview 👔
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan strategis perusahaan dan dokumen menunggu persetujuan Anda.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    Presiden Direktur
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        {{-- Pending Approval --}}
        <x-ui.stat-card title="Menunggu Approval" :value="$stats['pending_count'] ?? 0" icon="clock" color="primary" />

        {{-- Total Value --}}
        <x-ui.stat-card title="Total Nilai Pending" :value="'Rp ' . number_format($stats['pending_total_value'] ?? 0, 0, ',', '.')" icon="banknotes" color="success" />

        {{-- Urgent --}}
        <x-ui.stat-card title="Urgent" :value="$stats['urgent_count'] ?? 0" icon="exclamation-circle" color="warning" />

        {{-- Overdue --}}
        <x-ui.stat-card title="Overdue (>7 hari)" :value="$stats['overdue_count'] ?? 0" icon="exclamation-triangle" color="danger" />
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Monthly Spending Chart --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Pengeluaran 6 Bulan Terakhir</h3>
            <div class="h-64" id="monthlyChart">
                @if($monthlySpending->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                            <x-icon name="chart-bar" class="w-6 h-6 text-gray-300" />
                        </div>
                        <p class="text-sm font-medium">Belum ada data pengeluaran</p>
                    </div>
                @else
                    <canvas id="spendingChart"></canvas>
                @endif
            </div>
        </x-ui.card>

        {{-- Division Distribution --}}
        <x-ui.card>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Distribusi per Divisi (Tahun Ini)</h3>
            <div class="h-64" id="divisionChart">
                @if($divisionData->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                            <x-icon name="chart-pie" class="w-6 h-6 text-gray-300" />
                        </div>
                        <p class="text-sm font-medium">Belum ada data divisi</p>
                    </div>
                @else
                    <canvas id="divChart"></canvas>
                @endif
            </div>
        </x-ui.card>
    </div>

    {{-- Pending Approvals Table --}}
    <x-ui.card :padding="false">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dokumen Menunggu Persetujuan Anda</h3>
            <a href="{{ route('executive.approvals') }}" class="text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400">
                Lihat Semua →
            </a>
        </div>
        
        @if($pendingApprovals->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">Tidak ada dokumen yang menunggu persetujuan Anda</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dokumen</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Divisi</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($pendingApprovals->take(5) as $document)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $document->title }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $document->document_number }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'po' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                            'invoice' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                            'loan' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                        ];
                                        $typeLabels = ['po' => 'PO', 'invoice' => 'Invoice', 'loan' => 'Loan'];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $typeColors[$document->approval_type] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ $typeLabels[$document->approval_type] ?? ucfirst($document->approval_type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $document->department ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                    @if($document->approval_type === 'loan' && isset($document->metadata['loan_amount']))
                                        Rp {{ number_format($document->metadata['loan_amount'], 0, ',', '.') }}
                                    @else
                                        Rp {{ number_format($document->estimated_budget ?? 0, 0, ',', '.') }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $priorityColors = [
                                            'urgent' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                            'high' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                            'normal' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                                            'low' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-medium rounded-full {{ $priorityColors[$document->priority] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($document->priority) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <a href="{{ route('executive.approvals.show', $document) }}" class="text-purple-600 hover:text-purple-900 dark:text-purple-400 dark:hover:text-purple-300">
                                        Review →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>

    {{-- Quick Links --}}
    <div class="mt-8 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('executive.approvals') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-gray-900/5 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
            <div class="p-3 rounded-lg bg-purple-100 dark:bg-purple-900/30">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Approval Queue</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Review dokumen pending</p>
            </div>
        </a>
        
        <a href="{{ route('executive.insights') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-gray-900/5 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
            <div class="p-3 rounded-lg bg-emerald-100 dark:bg-emerald-900/30">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Company Insights</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Laporan keuangan global</p>
            </div>
        </a>
        
        <a href="{{ route('executive.audit') }}" class="flex items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] ring-1 ring-gray-900/5 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300">
            <div class="p-3 rounded-lg bg-gray-100 dark:bg-gray-700">
                <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-900 dark:text-white">Audit & History</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Riwayat keputusan strategis</p>
            </div>
        </a>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Monthly Spending Chart
            @if(!$monthlySpending->isEmpty())
            const spendingCtx = document.getElementById('spendingChart');
            if (spendingCtx) {
                new Chart(spendingCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($monthlySpending->map(fn($m) => $m->month . '/' . $m->year)) !!},
                        datasets: [{
                            label: 'Total Pengeluaran',
                            data: {!! json_encode($monthlySpending->pluck('total')) !!},
                            borderColor: 'rgb(147, 51, 234)',
                            backgroundColor: 'rgba(147, 51, 234, 0.1)',
                            tension: 0.3,
                            fill: true
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                                    }
                                }
                            }
                        }
                    }
                });
            }
            @endif

            // Division Chart
            @if(!$divisionData->isEmpty())
            const divCtx = document.getElementById('divChart');
            if (divCtx) {
                new Chart(divCtx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($divisionData->pluck('department')) !!},
                        datasets: [{
                            data: {!! json_encode($divisionData->pluck('total')) !!},
                            backgroundColor: [
                                'rgb(147, 51, 234)',
                                'rgb(59, 130, 246)',
                                'rgb(16, 185, 129)',
                                'rgb(249, 115, 22)',
                                'rgb(236, 72, 153)'
                            ]
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            }
            @endif
        });
    </script>
    @endpush
</x-app-layout>
