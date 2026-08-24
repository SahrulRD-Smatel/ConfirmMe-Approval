<x-app-layout>
    <x-slot name="title">{{ __('Executive Dashboard') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ __('Executive Overview') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Ringkasan strategis perusahaan dan dokumen menunggu persetujuan Anda.') }}
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3">
                <div class="flex items-center gap-2">
                    <a href="{{ route('reports.index') }}" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-700 hover:text-primary-600 dark:hover:text-primary-400 transition-colors shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ __('Laporan & Statistik') }}
                    </a>
                </div>
                <span class="inline-flex items-center px-3 py-2 rounded-xl text-sm font-semibold bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200 shadow-sm border border-purple-200 dark:border-purple-800">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                    {{ __('Presiden Direktur') }}
                </span>
            </div>
        </div>
    </x-slot>

    {{-- Financial Overview (Pastel Cards) --}}
    <div class="mb-8 grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Pengeluaran --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Total Pengeluaran (Tahun Ini)') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1 truncate" title="Rp {{ number_format($financeStats['total']['value'], 0, ',', '.') }}">
                        Rp {{ number_format($financeStats['total']['value'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="banknotes" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-white/20 text-white text-[10px] font-medium">
                    {{ $financeStats['total']['growth'] >= 0 ? '+' : '-' }}{{ abs($financeStats['total']['growth']) }}%
                </span>
                <span class="text-white/80 text-xs ml-1">{{ __('vs bulan lalu') }}</span>
            </div>
        </div>

        {{-- Total PO --}}
        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Total PO Disetujui') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1 truncate" title="Rp {{ number_format($financeStats['po']['value'], 0, ',', '.') }}">
                        Rp {{ number_format($financeStats['po']['value'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="document-duplicate" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-white/20 text-white text-[10px] font-medium">
                    {{ $financeStats['po']['growth'] >= 0 ? '+' : '-' }}{{ abs($financeStats['po']['growth']) }}%
                </span>
                <span class="text-white/80 text-xs ml-1">{{ __('vs bulan lalu') }}</span>
            </div>
        </div>

        {{-- Total Invoice --}}
        <div class="bg-gradient-to-br from-purple-500 to-indigo-600 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Total Invoice Dibayar') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1 truncate" title="Rp {{ number_format($financeStats['invoice']['value'], 0, ',', '.') }}">
                        Rp {{ number_format($financeStats['invoice']['value'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="receipt-percent" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-white/20 text-white text-[10px] font-medium">
                    {{ $financeStats['invoice']['growth'] >= 0 ? '+' : '-' }}{{ abs($financeStats['invoice']['growth']) }}%
                </span>
                <span class="text-white/80 text-xs ml-1">{{ __('vs bulan lalu') }}</span>
            </div>
        </div>

        {{-- Total Pinjaman --}}
        <div class="bg-gradient-to-br from-orange-400 to-orange-500 rounded-2xl p-5 relative overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <p class="text-white/80 text-xs font-bold uppercase tracking-wider mb-1">{{ __('Total Pinjaman Aktif') }}</p>
                    <h3 class="text-3xl font-bold text-white mb-1 truncate" title="Rp {{ number_format($financeStats['loan']['value'], 0, ',', '.') }}">
                        Rp {{ number_format($financeStats['loan']['value'], 0, ',', '.') }}
                    </h3>
                </div>
                <div class="p-2.5 bg-white/20 rounded-xl shrink-0">
                    <x-icon name="currency-dollar" class="w-5 h-5 text-white" />
                </div>
            </div>
            <div>
                <span class="inline-flex items-center px-2 py-1 rounded bg-white/20 text-white text-[10px] font-medium">
                    {{ $financeStats['loan']['growth'] >= 0 ? '+' : '-' }}{{ abs($financeStats['loan']['growth']) }}%
                </span>
                <span class="text-white/80 text-xs ml-1">{{ __('vs bulan lalu') }}</span>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="mb-8 grid grid-cols-1 gap-5 lg:grid-cols-2">
        {{-- Monthly Spending Chart --}}
        <x-ui.card>
            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Pengeluaran 6 Bulan Terakhir') }}</h3>
            <div class="h-64" id="monthlyChart">
                @if($monthlySpending->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                            <x-icon name="chart-bar" class="w-6 h-6 text-gray-300" />
                        </div>
                        <p class="text-sm font-medium">{{ __('Belum ada data pengeluaran') }}</p>
                    </div>
                @else
                    <canvas id="spendingChart"></canvas>
                @endif
            </div>
        </x-ui.card>

        {{-- Division Distribution --}}
        <x-ui.card>
            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Distribusi per Divisi') }}</h3>
            <div class="h-64" id="divisionChart">
                @if($divisionData->isEmpty())
                    <div class="flex flex-col items-center justify-center h-full text-gray-400">
                        <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                            <x-icon name="chart-pie" class="w-6 h-6 text-gray-300" />
                        </div>
                        <p class="text-sm font-medium">{{ __('Belum ada data divisi') }}</p>
                    </div>
                @else
                    <canvas id="divChart"></canvas>
                @endif
            </div>
        </x-ui.card>
    </div>

    {{-- Action Items & Table --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        
        {{-- Action Summary --}}
        <div class="lg:col-span-1 flex flex-col gap-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Tindakan Diperlukan') }}</h3>
            
            <div class="bg-white dark:bg-gray-800 border-l-4 border-primary-500 rounded-r-xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='{{ route('executive.approvals') }}'">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Menunggu Approval') }}</p>
                <div class="flex items-end gap-2">
                    <p class="text-3xl font-black text-gray-900 dark:text-white">{{ $stats['pending_count'] ?? 0 }}</p>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('dokumen') }}</p>
                </div>
                <p class="text-sm font-semibold text-primary-600 dark:text-primary-400 mt-2">Rp {{ number_format($stats['pending_total_value'] ?? 0, 0, ',', '.') }}</p>
            </div>

            <div class="bg-white dark:bg-gray-800 border-l-4 border-red-500 rounded-r-xl shadow-sm p-4 hover:shadow-md transition-shadow cursor-pointer" onclick="window.location='{{ route('executive.approvals') }}?priority=urgent'">
                <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">{{ __('Urgent / Overdue') }}</p>
                <div class="flex items-end gap-3 mt-1">
                    <div class="flex items-center gap-1 text-red-600 dark:text-red-400">
                        <x-icon name="exclamation-circle" class="w-5 h-5" />
                        <span class="text-xl font-bold">{{ $stats['urgent_count'] ?? 0 }}</span>
                    </div>
                    <div class="flex items-center gap-1 text-orange-600 dark:text-orange-400 border-l border-gray-200 dark:border-gray-700 pl-3">
                        <x-icon name="clock" class="w-5 h-5" />
                        <span class="text-xl font-bold">{{ $stats['overdue_count'] ?? 0 }}</span>
                    </div>
                </div>
                <p class="text-xs text-gray-500 mt-2">{{ __('Segera periksa dokumen prioritas tinggi') }}</p>
            </div>
            
            <div class="mt-auto pt-4">
                <a href="{{ route('executive.approvals') }}" class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 text-white rounded-xl text-sm font-semibold hover:bg-primary-700 transition-colors shadow-sm">
                    {{ __('Buka Antrean Approval') }} →
                </a>
            </div>
        </div>

        {{-- Pending Approvals Table --}}
        <div class="lg:col-span-3">
            <x-ui.card :padding="false">
                <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ __('Dokumen Menunggu Persetujuan Anda') }}</h3>
                    <a href="{{ route('executive.approvals') }}" class="text-sm font-medium text-purple-600 hover:text-purple-800 dark:text-purple-400">
                        {{ __('Lihat Semua') }} →
                    </a>
                </div>
        
        @if($pendingApprovals->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-gray-500 dark:text-gray-400">{{ __('Tidak ada dokumen yang menunggu persetujuan Anda') }}</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    <thead class="bg-gray-50 dark:bg-gray-700/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Dokumen') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Tipe') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Divisi') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Nominal') }}</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Priority') }}</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        @foreach($pendingApprovals->take(5) as $document)
                            <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-700/50">
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
                                        {{ __('Review') }} →
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-ui.card>
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
                            label: '{{ __('Total Pengeluaran') }}',
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
