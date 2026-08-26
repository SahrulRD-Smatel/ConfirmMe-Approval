<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ __('Pinjaman Saya') }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Riwayat pinjaman karyawan Anda') }}</p>
        </div>
    </x-slot>


            {{-- Stats Cards --}}
            <div class="mb-7 grid grid-cols-2 gap-4 md:grid-cols-4">
                <x-ui.stat-card title="{{ __('Pinjaman Aktif') }}" :value="$stats['active_loans']" icon="currency-dollar" color="primary" />
                <x-ui.stat-card title="{{ __('Total Pinjaman') }}" :value="'Rp ' . number_format($stats['total_borrowed'], 0, ',', '.')" icon="banknotes" />
                <x-ui.stat-card title="{{ __('Sisa Hutang') }}" :value="'Rp ' . number_format($stats['total_outstanding'], 0, ',', '.')" icon="arrow-trending-down" color="warning" />
                <x-ui.stat-card title="{{ __('Lunas') }}" :value="$stats['paid_off']" icon="check-circle" color="success" />
            </div>

            {{-- Loans List --}}
            <div class="space-y-4">
                @forelse($loans as $loan)
                    <div class="rounded-3xl border border-gray-200/80 bg-white p-6 shadow-soft dark:border-gray-700/80 dark:bg-gray-900">
                        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                            <div class="flex-1">
                                <div class="flex items-center gap-3 mb-2">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $loan->loan_number }}
                                    </h3>
                                    <x-ui.badge :type="$loan->status_color">{{ $loan->status_label }}</x-ui.badge>
                                </div>
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <p class="text-gray-500">{{ __('Jumlah Pinjaman') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $loan->formatted_loan_amount }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('Cicilan/Bulan') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $loan->formatted_monthly_installment }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('Sisa Hutang') }}</p>
                                        <p class="font-medium text-primary-600">{{ $loan->formatted_outstanding }}</p>
                                    </div>
                                    <div>
                                        <p class="text-gray-500">{{ __('Pembayaran') }}</p>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            {{ $loan->repayments->count() }}/{{ $loan->installment_months }}</p>
                                    </div>
                                </div>

                                {{-- Progress Bar --}}
                                <div class="mt-4">
                                    <div class="flex justify-between text-xs text-gray-500 mb-1">
                                        <span>Progress: {{ number_format($loan->getProgressPercentage(), 0) }}%</span>
                                        <span>{{ $loan->formatted_total_paid }} / {{ $loan->formatted_loan_amount }}</span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full"
                                            style="width: {{ $loan->getProgressPercentage() }}%"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="flex-shrink-0">
                                <a href="{{ route('my-loans.show', $loan) }}"
                                    class="inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700 text-sm">
                                    {{ __('Lihat Detail') }}
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Belum ada pinjaman') }}</h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            {{ __('Anda belum memiliki riwayat pinjaman karyawan.') }}
                        </p>
                        <a href="{{ route('documents.create') }}"
                            class="mt-4 inline-flex items-center px-4 py-2 bg-primary-600 text-white rounded-xl hover:bg-primary-700">
                            {{ __('Ajukan Pinjaman Baru') }}
                        </a>
                    </div>
                @endforelse
            </div>

            @if($loans->hasPages())
                <div class="mt-6">
                    {{ $loans->links() }}
                </div>
            @endif

</x-app-layout>