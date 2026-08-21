<x-app-layout>
    <x-slot name="title">Company Insights - Executive</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    Company Insights
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan kinerja approval dan keuangan {{ $year }}
                </p>
            </div>
            <form method="GET" class="flex items-center gap-2">
                <select name="year" onchange="this.form.submit()"
                    class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-xl">
                    @for($y = now()->year; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </form>
        </div>
    </x-slot>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        {{-- Total PO --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-blue-100 dark:bg-blue-900/30">
                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total PO Disetujui</p>
                    <p class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp
                        {{ number_format($totalPO ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $countPO ?? 0 }} dokumen</p>
                </div>
            </div>
        </div>

        {{-- Total Invoice --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-emerald-100 dark:bg-emerald-900/30">
                    <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Invoice Dibayar</p>
                    <p class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp
                        {{ number_format($totalInvoice ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $countInvoice ?? 0 }} invoice</p>
                </div>
            </div>
        </div>

        {{-- Total Loans --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-purple-100 dark:bg-purple-900/30">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Loan Aktif</p>
                    <p class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">Rp
                        {{ number_format($totalLoans ?? 0, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activeLoans->count() ?? 0 }} pinjaman</p>
                </div>
            </div>
        </div>

        {{-- Avg Approval Time --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <div class="flex items-center gap-4">
                <div class="p-3 rounded-xl bg-orange-100 dark:bg-orange-900/30">
                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata Waktu Approval</p>
                    <p class="text-xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ round($avgApprovalTime ?? 0) }} jam
                    </p>
                    <p class="text-xs text-gray-400 dark:text-gray-500">dari submit hingga approved</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
        {{-- Monthly Breakdown --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Pengeluaran Bulanan {{ $year }}</h3>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- Division Breakdown --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80 p-6">
            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Distribusi per Divisi {{ $year }}</h3>
            <div class="h-64">
                @if($divisionBreakdown->isEmpty())
                    <div class="flex items-center justify-center h-full text-gray-400">Belum ada data</div>
                @else
                    <canvas id="divisionChart"></canvas>
                @endif
            </div>
        </div>
    </div>

    {{-- Division Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft border border-gray-200/80 dark:border-gray-700/80">
        <div class="px-6 py-4 border-b border-gray-200/80 dark:border-gray-700/80">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Detail per Divisi</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Divisi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Total Nilai</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                            Jumlah Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    @forelse($divisionBreakdown as $division)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                {{ $division->department ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-900 dark:text-white">Rp
                                {{ number_format($division->total, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">{{ $division->count }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">Belum ada data
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Monthly Chart
                const monthlyData = @json($monthlyBreakdown);
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

                // Process data for chart
                const poData = new Array(12).fill(0);
                const invoiceData = new Array(12).fill(0);
                const loanData = new Array(12).fill(0);

                monthlyData.forEach(item => {
                    const idx = item.month - 1;
                    if (item.approval_type === 'po') poData[idx] = item.total;
                    else if (item.approval_type === 'invoice') invoiceData[idx] = item.total;
                    else if (item.approval_type === 'loan') loanData[idx] = item.total;
                });

                new Chart(document.getElementById('monthlyChart'), {
                    type: 'bar',
                    data: {
                        labels: months,
                        datasets: [
                            { label: 'PO', data: poData, backgroundColor: 'rgb(59, 130, 246)' },
                            { label: 'Invoice', data: invoiceData, backgroundColor: 'rgb(16, 185, 129)' },
                            { label: 'Loan', data: loanData, backgroundColor: 'rgb(147, 51, 234)' }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            x: { stacked: true },
                            y: {
                                stacked: true,
                                ticks: {
                                    callback: function (value) {
                                        return 'Rp ' + (value / 1000000).toFixed(0) + 'M';
                                    }
                                }
                            }
                        }
                    }
                });

                // Division Chart
                @if(!$divisionBreakdown->isEmpty())
                    new Chart(document.getElementById('divisionChart'), {
                        type: 'doughnut',
                        data: {
                            labels: {!! json_encode($divisionBreakdown->pluck('department')) !!},
                            datasets: [{
                                data: {!! json_encode($divisionBreakdown->pluck('total')) !!},
                                backgroundColor: ['#3B82F6', '#10B981', '#8B5CF6', '#F59E0B', '#EC4899']
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false }
                    });
                @endif
            });
        </script>
    @endpush
</x-app-layout>