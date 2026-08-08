<x-app-layout>
    <x-slot name="title">Finance Dashboard</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Finance Dashboard</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Overview Keuangan & Approval</p>
            </div>
            <div class="flex items-center gap-2">
                <x-ui.button href="{{ route('finance.budgets.index') }}" variant="secondary" icon="banknotes" size="sm">
                    Budget
                </x-ui.button>
                <x-ui.button href="{{ route('reports.index') }}" variant="secondary" icon="document-chart-bar"
                    size="sm">
                    Laporan
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    {{-- Alerts Section --}}
    @if(count($alerts) > 0)
        <div class="mb-8 space-y-2">
            @foreach($alerts as $alert)
                <div
                    class="flex items-center gap-3 p-4 rounded-xl border {{ $alert['type'] === 'danger' ? 'bg-red-50 border-red-100 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-400' : ($alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:border-yellow-800 dark:text-yellow-400' : 'bg-blue-50 border-blue-100 text-blue-800 dark:bg-blue-900/20 dark:border-blue-800 dark:text-blue-400') }}">
                    <x-icon name="{{ $alert['icon'] }}" class="w-5 h-5 flex-shrink-0" />
                    <span class="text-sm font-medium">{{ $alert['message'] }}</span>
                </div>
            @endforeach
        </div>
    @endif

    {{-- SECTION 1: Financial Health (Budget KPIs) --}}
    <section class="mb-8 animate-fade-in-down">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">Financial Health (Tahun {{ date('Y') }})</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Budget --}}
            <x-ui.stat-card title="Total Budget" :value="'Rp ' . number_format($budgetKPIs['total'] / 1000000000, 1, ',', '.') . ' M'" icon="banknotes" color="primary">
                <p class="text-xs text-gray-500 mt-1">Rp {{ number_format($budgetKPIs['total'], 0, ',', '.') }}</p>
            </x-ui.stat-card>

            {{-- Committed --}}
            <x-ui.stat-card title="Committed (PO)" :value="'Rp ' . number_format($budgetKPIs['committed'] / 1000000, 0, ',', '.') . ' Jt'" icon="lock-closed" color="info">
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-blue-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $budgetKPIs['committed_percentage'] }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500">{{ $budgetKPIs['committed_percentage'] }}%</span>
                </div>
            </x-ui.stat-card>

            {{-- Used --}}
            <x-ui.stat-card title="Terpakai (Actual)" :value="$budgetKPIs['used'] == 0 ? 'Rp 0' : 'Rp ' . number_format($budgetKPIs['used'] / 1000000, 0, ',', '.') . ' Jt'" icon="arrow-trending-up" color="warning">
                <div class="flex items-center gap-2 mt-1">
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5 overflow-hidden">
                        <div class="bg-orange-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: {{ $budgetKPIs['usage_percentage'] }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500 font-medium">{{ $budgetKPIs['usage_percentage'] }}%</span>
                </div>
            </x-ui.stat-card>

            {{-- Remaining --}}
            <x-ui.stat-card title="Sisa Budget" :value="$budgetKPIs['remaining'] == 0 ? 'Rp 0' : 'Rp ' . number_format($budgetKPIs['remaining'] / 1000000000, 1, ',', '.') . ' M'" icon="currency-dollar" color="success">
                <p class="text-xs text-success-600/70 mt-1 font-medium tracking-wide uppercase">Available for use</p>
            </x-ui.stat-card>
        </div>
    </section>

    {{-- SECTION 2: Action Center (Layout Grid: 2/3 Content + 1/3 Side) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        {{-- Main Column: Approval Queue & Charts --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Approval Queue --}}
            <div>
                <div class="mb-4">
                    <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Menunggu
                        Persetujuan</h2>
                </div>

                @if($approvalQueue->count() > 0)
                    <x-ui.card :padding="false">
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 dark:bg-gray-700/50 text-gray-500 font-medium">
                                    <tr>
                                        <th class="px-4 py-3">Dokumen</th>
                                        <th class="px-4 py-3">Type</th>
                                        <th class="px-4 py-3 text-right">Nominal</th>
                                        <th class="px-4 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($approvalQueue as $doc)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                                            <td class="px-4 py-3">
                                                <div class="font-medium text-gray-900 dark:text-white">
                                                    {{ $doc->document_number }}</div>
                                                <div class="text-xs text-gray-500">{{ $doc->title }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span
                                                    class="px-2 py-0.5 text-xs rounded-full {{ $doc->approval_type === 'po' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                                    {{ strtoupper($doc->approval_type) }}
                                                </span>
                                            </td>
                                            <td
                                                class="px-4 py-3 text-right font-medium {{ $doc->estimated_budget > 50000000 ? 'text-red-600' : 'text-gray-700 dark:text-gray-300' }}">
                                                Rp {{ number_format($doc->estimated_budget, 0, ',', '.') }}
                                            </td>
                                            <td class="px-4 py-3 text-right">
                                                <x-ui.button href="{{ route('approvals.show', $doc) }}" size="xs"
                                                    variant="primary">Review</x-ui.button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-ui.card>
                @else
                    <x-ui.card class="text-center py-12">
                        <div class="w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 flex items-center justify-center mx-auto mb-4">
                            <x-icon name="document-check" class="w-8 h-8 text-gray-300 dark:text-gray-600" />
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-1">Semua Selesai!</h3>
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada dokumen menunggu persetujuan Anda saat ini.</p>
                    </x-ui.card>
                @endif
            </div>

            {{-- Analytics Charts --}}
            <div>
                <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
                    Analytics</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Cash Flow --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold mb-4 text-gray-900 dark:text-white">Cash Flow (YTD)</h3>
                        <div class="h-48">
                            <canvas id="cashFlowChart"></canvas>
                        </div>
                    </div>
                    {{-- Budget vs Actual --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold mb-4 text-gray-900 dark:text-white">Budget vs Actual</h3>
                        <div class="h-48">
                            <canvas id="budgetChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Side Column: Operational --}}
        <div class="space-y-6">
            {{-- Outstanding Invoices Table --}}
            <x-ui.card>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Invoice Outstanding</h3>
                    <span class="text-xs font-bold text-red-600 bg-red-50 dark:bg-red-900/30 px-2 py-1 rounded">
                        {{ $outstandingTotal == 0 ? 'Rp 0' : 'Rp ' . number_format($outstandingTotal / 1000000, 0, ',', '.') . ' Jt' }}
                    </span>
                </div>
                <div class="space-y-3">
                    @forelse($outstandingInvoices as $invoice)
                        <div class="flex justify-between items-center text-sm p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer"
                            onclick="window.location='{{ route('finance.invoices.show', $invoice) }}'">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $invoice->invoice_number }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $invoice->due_date->format('d M') }} •
                                    {{ $invoice->vendor_name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium {{ $invoice->isOverdue() ? 'text-red-600' : 'text-gray-700' }}">Rp
                                    {{ number_format(($invoice->total_amount - $invoice->paid_amount) / 1000000, 1) }}jt</div>
                                @if($invoice->isOverdue()) <span class="text-[10px] text-red-500">OVERDUE</span> @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-2">Semua aman!</p>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Pending PO --}}
            <x-ui.card>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-gray-900 dark:text-white">Pending Invoice (PO)</h3>
                    <span class="text-xs font-bold text-blue-600 bg-blue-50 dark:bg-blue-900/30 px-2 py-1 rounded">
                        {{ $pendingPOTotal == 0 ? 'Rp 0' : 'Rp ' . number_format($pendingPOTotal / 1000000, 0, ',', '.') . ' Jt' }}
                    </span>
                </div>
                <div class="space-y-3">
                    @forelse($pendingPO as $po)
                        <div class="flex justify-between items-center text-sm p-2 hover:bg-gray-50 dark:hover:bg-gray-700/50 rounded-lg cursor-pointer"
                            onclick="window.location='{{ route('finance.po.show', $po) }}'">
                            <div>
                                <div class="font-medium text-gray-800 dark:text-gray-200">{{ $po->po_number }}</div>
                                <div class="text-xs text-gray-500">{{ $po->created_at->format('d M') }} •
                                    {{ $po->vendor_name }}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-medium text-gray-700 dark:text-gray-300">Rp
                                    {{ number_format($po->total_amount / 1000000, 1) }}jt</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-2">Tidak ada PO pending.</p>
                    @endforelse
                </div>
            </x-ui.card>

            {{-- Distribution Stats --}}
            <x-ui.card>
                <h3 class="font-semibold text-gray-900 dark:text-white mb-2">Distribusi</h3>
                <div class="h-40 flex justify-center">
                    <canvas id="distributionChart"></canvas>
                </div>
            </x-ui.card>
        </div>
    </div>

    {{-- SECTION 3: Employee Loans --}}
    <section class="mb-8">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pinjaman
                Karyawan</h2>
        </div>

        <x-ui.card>
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 divide-x divide-gray-100 dark:divide-gray-700">
                <div class="px-4">
                    <p class="text-xs text-gray-500">Total Outstanding</p>
                    <p class="text-xl font-bold text-gray-900 dark:text-white">{{ $loanStats['total_outstanding'] == 0 ? 'Rp 0' : 'Rp ' . number_format($loanStats['total_outstanding'] / 1000000, 1, ',', '.') . ' Jt' }}</p>
                </div>
                <div class="px-4">
                    <p class="text-xs text-gray-500">Aktif</p>
                    <p class="text-xl font-bold text-blue-600">{{ $loanStats['active'] }} <span
                            class="text-xs text-gray-400 font-normal">Karyawan</span></p>
                </div>
                <div class="px-4">
                    <p class="text-xs text-gray-500">Menunggu Cair</p>
                    <p class="text-xl font-bold text-yellow-600">{{ $loanStats['pending_disbursement'] }} <span
                            class="text-xs text-gray-400 font-normal">Request</span></p>
                </div>
                <div class="px-4">
                    <p class="text-xs text-gray-500">Total Cair</p>
                    <p class="text-xl font-bold text-green-600">{{ $loanStats['total_disbursed'] == 0 ? 'Rp 0' : 'Rp ' . number_format($loanStats['total_disbursed'] / 1000000, 1, ',', '.') . ' Jt' }}</p>
                </div>
                <div class="px-4 flex items-center justify-end">
                    <x-ui.button href="{{ route('finance.loans.index') }}" size="sm" variant="primary">Detail
                        Loans</x-ui.button>
                </div>
            </div>
        </x-ui.card>
    </section>

    {{-- Scripts --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Chart Configs
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: true, color: 'rgba(0,0,0,0.03)', drawBorder: false },
                        ticks: { 
                            callback: function (val) { return (val / 1000000).toFixed(0) + 'M'; },
                            color: '#9ca3af',
                            font: { size: 10 }
                        }
                    },
                    x: { 
                        grid: { display: false },
                        ticks: { color: '#9ca3af', font: { size: 10 } }
                    }
                },
                interaction: { intersect: false, mode: 'index' }
            };

            // Budget vs Actual
            const budgetCtx = document.getElementById('budgetChart');
            if (budgetCtx) {
                new Chart(budgetCtx, {
                    type: 'bar',
                    data: {
                        labels: @json($budgetChartData['labels']),
                        datasets: [
                            { label: 'Budget', data: @json($budgetChartData['budget']), backgroundColor: 'rgba(99, 102, 241, 0.15)', hoverBackgroundColor: 'rgba(99, 102, 241, 0.25)', borderRadius: 6, barPercentage: 0.6 },
                            { label: 'Used', data: @json($budgetChartData['used']), backgroundColor: '#6366f1', borderRadius: 6, barPercentage: 0.6 }
                        ]
                    },
                    options: chartOptions
                });
            }

            // Cash Flow
            const cashFlowCtx = document.getElementById('cashFlowChart');
            if (cashFlowCtx) {
                const ctx = cashFlowCtx.getContext('2d');
                const gradientFill = ctx.createLinearGradient(0, 0, 0, 300);
                gradientFill.addColorStop(0, 'rgba(14, 165, 233, 0.4)');
                gradientFill.addColorStop(1, 'rgba(14, 165, 233, 0.0)');

                new Chart(cashFlowCtx, {
                    type: 'line',
                    data: {
                        labels: @json($cashFlowData['labels']),
                        datasets: [
                            {
                                label: 'Out',
                                data: @json($cashFlowData['used']),
                                borderColor: '#0ea5e9',
                                backgroundColor: gradientFill,
                                fill: true,
                                tension: 0.4,
                                pointRadius: 0,
                                borderWidth: 3
                            }
                        ]
                    },
                    options: chartOptions
                });
            }

            // Distribution
            const distCtx = document.getElementById('distributionChart');
            if (distCtx) {
                new Chart(distCtx, {
                    type: 'doughnut',
                    data: {
                        labels: @json($distributionData['labels']),
                        datasets: [{
                            data: @json($distributionData['values']),
                            backgroundColor: ['#3b82f6', '#10b981', '#f97316', '#8b5cf6', '#ec4899', '#06b6d4'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: { legend: { display: false } }
                    }
                });
            }
        });
    </script>
</x-app-layout>