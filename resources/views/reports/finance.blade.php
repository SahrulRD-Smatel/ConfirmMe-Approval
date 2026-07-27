@php
    if (!function_exists('smartMoney')) {
        function smartMoney($value) {
            $abs = abs($value);
            if ($abs >= 1000000000000) {
                return 'Rp ' . number_format($value / 1000000000000, 1, ',', '.') . ' T';
            } elseif ($abs >= 1000000000) {
                return 'Rp ' . number_format($value / 1000000000, 1, ',', '.') . ' M';
            } elseif ($abs >= 1000000) {
                return 'Rp ' . number_format($value / 1000000, 1, ',', '.') . ' Jt';
            } elseif ($abs >= 1000) {
                return 'Rp ' . number_format($value / 1000, 0, ',', '.') . ' Rb';
            }
            return 'Rp ' . number_format($value, 0, ',', '.');
        }
    }
@endphp
<x-app-layout>
    <x-slot name="title">Laporan Keuangan</x-slot>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 animate-fade-in-down">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Laporan Keuangan</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Ringkasan anggaran, pembayaran, dan pinjaman — Tahun {{ $currentYear }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                {{-- Year Selector --}}
                <form method="GET" class="flex items-center gap-2">
                    <select name="year" onchange="this.form.submit()"
                        class="text-sm border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500">
                        @for($y = now()->year; $y >= now()->year - 3; $y--)
                            <option value="{{ $y }}" {{ $currentYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endfor
                    </select>
                </form>
                <x-ui.button href="{{ route('reports.index') }}" variant="secondary" icon="arrow-left" size="sm">
                    Kembali
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    {{-- SECTION 1: Budget Summary Cards --}}
    <section class="mb-8 animate-fade-in-down">
        <h2 class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-4">
            Ringkasan Anggaran {{ $currentYear }}
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            {{-- Total Budget --}}
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-5 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-indigo-100 text-xs font-medium uppercase tracking-wider">Total Anggaran</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ smartMoney($budgetSummary['total']) }}
                        </p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <x-icon name="banknotes" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="mt-3 text-xs text-indigo-100">Rp {{ number_format($budgetSummary['total'], 0, ',', '.') }}</p>
            </div>

            {{-- Budget Terpakai --}}
            <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-xl p-5 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-orange-100 text-xs font-medium uppercase tracking-wider">Terpakai (Actual)</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ smartMoney($budgetSummary['used']) }}
                        </p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <x-icon name="arrow-trending-up" class="w-6 h-6 text-white" />
                    </div>
                </div>
                @php
                    $usagePct = $budgetSummary['total'] > 0 ? round(($budgetSummary['used'] / $budgetSummary['total']) * 100, 1) : 0;
                @endphp
                <div class="mt-3">
                    <div class="flex justify-between text-xs text-orange-100 mb-1">
                        <span>{{ $usagePct }}% terpakai</span>
                    </div>
                    <div class="w-full bg-white/20 rounded-full h-1.5">
                        <div class="bg-white h-1.5 rounded-full transition-all duration-1000" style="width: {{ min($usagePct, 100) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- Budget Terikat (Committed) --}}
            <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-xl p-5 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-blue-100 text-xs font-medium uppercase tracking-wider">Committed (PO)</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ smartMoney($budgetSummary['committed']) }}
                        </p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <x-icon name="lock-closed" class="w-6 h-6 text-white" />
                    </div>
                </div>
                @php
                    $commitPct = $budgetSummary['total'] > 0 ? round(($budgetSummary['committed'] / $budgetSummary['total']) * 100, 1) : 0;
                @endphp
                <p class="mt-3 text-xs text-blue-100">{{ $commitPct }}% dari total anggaran</p>
            </div>

            {{-- Sisa Budget --}}
            <div class="bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl p-5 text-white shadow-lg transform hover:-translate-y-1 transition duration-300">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="text-green-100 text-xs font-medium uppercase tracking-wider">Sisa Anggaran</p>
                        <p class="text-2xl font-bold mt-1">
                            {{ smartMoney($budgetSummary['remaining']) }}
                        </p>
                    </div>
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm">
                        <x-icon name="currency-dollar" class="w-6 h-6 text-white" />
                    </div>
                </div>
                <p class="mt-3 text-xs text-green-100">Available for use</p>
            </div>
        </div>
    </section>

    {{-- SECTION 2: Charts & Invoice Stats --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Monthly Spending Chart (2/3) --}}
        <div class="lg:col-span-2 bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tren Pengeluaran Bulanan {{ $currentYear }}</h2>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        {{-- Invoice Summary (1/3) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Ringkasan Invoice</h2>

            <div class="space-y-4">
                {{-- Paid --}}
                <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-xl border border-green-100 dark:border-green-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-green-100 dark:bg-green-900/40 rounded-lg">
                            <x-icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
                        </div>
                        <div>
                            <p class="text-xs text-green-600 dark:text-green-400 font-medium">Total Terbayar</p>
                            <p class="text-lg font-bold text-green-700 dark:text-green-300">
                                Rp {{ number_format($invoiceStats['paid_total'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Outstanding --}}
                <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl border border-yellow-100 dark:border-yellow-800">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-yellow-100 dark:bg-yellow-900/40 rounded-lg">
                            <x-icon name="clock" class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                        </div>
                        <div>
                            <p class="text-xs text-yellow-600 dark:text-yellow-400 font-medium">Outstanding</p>
                            <p class="text-lg font-bold text-yellow-700 dark:text-yellow-300">
                                Rp {{ number_format($invoiceStats['outstanding_total'], 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Overdue --}}
                <div class="p-4 {{ $invoiceStats['overdue_count'] > 0 ? 'bg-red-50 dark:bg-red-900/20 border-red-100 dark:border-red-800' : 'bg-gray-50 dark:bg-gray-700/30 border-gray-100 dark:border-gray-600' }} rounded-xl border">
                    <div class="flex items-center gap-3">
                        <div class="p-2 {{ $invoiceStats['overdue_count'] > 0 ? 'bg-red-100 dark:bg-red-900/40' : 'bg-gray-100 dark:bg-gray-600' }} rounded-lg">
                            <x-icon name="exclamation-triangle" class="w-5 h-5 {{ $invoiceStats['overdue_count'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-400' }}" />
                        </div>
                        <div>
                            <p class="text-xs {{ $invoiceStats['overdue_count'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-500' }} font-medium">Jatuh Tempo</p>
                            <p class="text-lg font-bold {{ $invoiceStats['overdue_count'] > 0 ? 'text-red-700 dark:text-red-300' : 'text-gray-400' }}">
                                {{ $invoiceStats['overdue_count'] }} Invoice
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 3: Budget Composition Donut + Loan --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Budget Composition --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Komposisi Anggaran</h2>
            <div class="h-56 flex items-center justify-center">
                <canvas id="budgetDonut"></canvas>
            </div>
            <div class="mt-4 flex items-center justify-center gap-6 text-xs">
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-orange-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Terpakai</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Committed</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="w-3 h-3 rounded-full bg-green-500"></span>
                    <span class="text-gray-600 dark:text-gray-400">Sisa</span>
                </div>
            </div>
        </div>

        {{-- Loan Summary --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pinjaman Karyawan</h2>
                @if(auth()->user()->hasRole('finance'))
                    <x-ui.button href="{{ route('finance.loans.index') }}" variant="secondary" size="xs" icon="arrow-right">
                        Kelola
                    </x-ui.button>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                {{-- Active Loans --}}
                <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl p-4 border border-blue-100 dark:border-blue-800 text-center">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/40 rounded-lg w-fit mx-auto mb-2">
                        <x-icon name="user-group" class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                    </div>
                    <p class="text-3xl font-bold text-blue-700 dark:text-blue-300">{{ $loanStats['active'] }}</p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Pinjaman Aktif</p>
                </div>

                {{-- Total Outstanding --}}
                <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-100 dark:border-amber-800 text-center">
                    <div class="p-2 bg-amber-100 dark:bg-amber-900/40 rounded-lg w-fit mx-auto mb-2">
                        <x-icon name="banknotes" class="w-6 h-6 text-amber-600 dark:text-amber-400" />
                    </div>
                    <p class="text-xl font-bold text-amber-700 dark:text-amber-300">
                        {{ smartMoney($loanStats['total_outstanding']) }}
                    </p>
                    <p class="text-xs text-amber-600 dark:text-amber-400 mt-1">Total Outstanding</p>
                </div>
            </div>

            @if($loanStats['active'] == 0)
                <div class="text-center py-4">
                    <x-icon name="check-badge" class="w-10 h-10 text-green-400 mx-auto mb-2" />
                    <p class="text-sm text-gray-500">Tidak ada pinjaman aktif saat ini.</p>
                </div>
            @endif
        </div>
    </div>

    {{-- SECTION 4: Monthly Spending Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-700 shadow-sm mb-8">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Detail Pengeluaran Per Bulan</h2>

        <div class="overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-l-lg">Bulan</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah Invoice</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider rounded-r-lg">Total Pembayaran</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @php
                        $monthNames = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                        $grandTotal = 0;
                        $grandCount = 0;
                    @endphp

                    @forelse($monthlyData as $row)
                        @php
                            $grandTotal += $row->total;
                            $grandCount += $row->count;
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $monthNames[$row->month] ?? 'Bulan ' . $row->month }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                    {{ $row->count }} invoice
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-900 dark:text-white">
                                Rp {{ number_format($row->total, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500 italic">
                                Belum ada data pembayaran di tahun {{ $currentYear }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($monthlyData->count() > 0)
                    <tfoot class="bg-gray-50 dark:bg-gray-700/50 font-semibold">
                        <tr>
                            <td class="px-4 py-3 text-gray-900 dark:text-white rounded-l-lg">Total</td>
                            <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $grandCount }} invoice</td>
                            <td class="px-4 py-3 text-right text-gray-900 dark:text-white rounded-r-lg">
                                Rp {{ number_format($grandTotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- Charts JS --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Monthly spending chart data
            const months = @json($monthlyData->pluck('month'));
            const totals = @json($monthlyData->pluck('total'));

            const monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
            const labels = months.map(m => monthNames[m] || 'Bln ' + m);

            // Monthly Spending Bar Chart
            const monthlyCtx = document.getElementById('monthlyChart');
            if (monthlyCtx) {
                new Chart(monthlyCtx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pengeluaran',
                            data: totals,
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
                        animation: { duration: 2000, easing: 'easeOutQuart' },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        return 'Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw);
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.05)' },
                                ticks: {
                                    callback: function(val) {
                                        if (val >= 1000000000) return (val / 1000000000).toFixed(1) + 'M';
                                        if (val >= 1000000) return (val / 1000000).toFixed(0) + 'Jt';
                                        return val;
                                    }
                                }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            }

            // Budget Composition Donut
            const donutCtx = document.getElementById('budgetDonut');
            if (donutCtx) {
                const budgetData = @json($budgetSummary);
                new Chart(donutCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Terpakai', 'Committed', 'Sisa'],
                        datasets: [{
                            data: [budgetData.used, budgetData.committed, budgetData.remaining],
                            backgroundColor: ['#f97316', '#3b82f6', '#10b981'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        animation: { animateScale: true, animateRotate: true },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(ctx) {
                                        const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                        const pct = total > 0 ? ((ctx.raw / total) * 100).toFixed(1) : 0;
                                        return ctx.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(ctx.raw) + ' (' + pct + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            }
        });
    </script>

    <style>
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in-down {
            animation: fadeInDown 0.5s ease-out;
        }
    </style>
</x-app-layout>
