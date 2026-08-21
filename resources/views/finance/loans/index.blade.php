<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Pinjaman Karyawan</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Kelola pencairan dan cicilan pinjaman karyawan</p>
            </div>
            <div>
                <a href="{{ route('finance.loans.receivables') }}" target="_blank"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Laporan Piutang
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-6">
                <x-ui.stat-card title="Menunggu Pencairan" :value="$stats['pending_disbursement']" icon="clock" color="warning" />
                <x-ui.stat-card title="Pinjaman Aktif" :value="$stats['active']" icon="currency-dollar" color="primary" />
                <x-ui.stat-card title="Lunas" :value="$stats['paid_off']" icon="check-circle" color="success" />
                <x-ui.stat-card title="Total Kas Keluar" :value="'Rp ' . number_format($stats['total_disbursed'], 0, ',', '.')" icon="banknotes" />
                <x-ui.stat-card title="Total Outstanding" :value="'Rp ' . number_format($stats['total_outstanding'], 0, ',', '.')" icon="arrow-trending-up" color="danger" />
            </div>

            {{-- Filters --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft mb-6 p-4">
                <form action="{{ route('finance.loans.index') }}" method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nomor pinjaman atau nama..."
                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                    <select name="status" class="rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Semua Status</option>
                        <option value="pending_disbursement" {{ request('status') == 'pending_disbursement' ? 'selected' : '' }}>Menunggu Pencairan</option>
                        <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Dicairkan
                        </option>
                        <option value="repaying" {{ request('status') == 'repaying' ? 'selected' : '' }}>Dalam Cicilan
                        </option>
                        <option value="paid_off" {{ request('status') == 'paid_off' ? 'selected' : '' }}>Lunas</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status']))
                        <a href="{{ route('finance.loans.index') }}"
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:underline">
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            {{-- Loans Table --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-[900px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    No. Pinjaman</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Karyawan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Jumlah</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Cicilan/Bulan</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Outstanding</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                            @forelse($loans as $loan)
                                <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $loan->loan_number }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $loan->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img class="h-8 w-8 rounded-full" src="{{ $loan->employee->avatar_url }}"
                                                alt="">
                                            <div class="ml-3">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $loan->employee->name }}
                                                </div>
                                                <div class="text-xs text-gray-500">{{ $loan->employee->department }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $loan->formatted_loan_amount }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loan->formatted_monthly_installment }} × {{ $loan->installment_months }} bln
                                    </td>
                                    <td
                                        class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $loan->formatted_outstanding }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-ui.badge :type="$loan->status_color">{{ $loan->status_label }}</x-ui.badge>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <a href="{{ route('finance.loans.show', $loan) }}"
                                            class="text-primary-600 hover:text-primary-800 dark:text-primary-400">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        <p class="mt-2">Belum ada data pinjaman</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($loans->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">
                        {{ $loans->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>