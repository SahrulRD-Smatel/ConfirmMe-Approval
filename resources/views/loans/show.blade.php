<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('my-loans.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $loan->loan_number }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Detail Pinjaman</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Loan Summary Card --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6 mb-6">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Pinjaman</h3>
                        <p class="text-sm text-gray-500">Diajukan {{ $loan->created_at->format('d F Y') }}</p>
                    </div>
                    <x-ui.badge :type="$loan->status_color" size="lg">{{ $loan->status_label }}</x-ui.badge>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Jumlah Pinjaman</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $loan->formatted_loan_amount }}
                        </p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Cicilan/Bulan</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white">
                            {{ $loan->formatted_monthly_installment }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Sudah Dibayar</p>
                        <p class="text-lg font-bold text-green-600">{{ $loan->formatted_total_paid }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-lg text-center">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Sisa Hutang</p>
                        <p class="text-lg font-bold text-primary-600">{{ $loan->formatted_outstanding }}</p>
                    </div>
                </div>

                {{-- Progress --}}
                <div class="mb-6">
                    <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                        <span>Progress Pembayaran</span>
                        <span class="font-medium">{{ number_format($loan->getProgressPercentage(), 1) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                        <div class="bg-gradient-to-r from-green-400 to-green-600 h-4 rounded-full transition-all duration-300"
                            style="width: {{ $loan->getProgressPercentage() }}%"></div>
                    </div>
                </div>

                @if($loan->disbursement_date)
                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <p class="text-sm text-green-800 dark:text-green-300">
                            <span class="font-medium">✓ Pinjaman dicairkan</span> pada
                            {{ $loan->disbursement_date->format('d F Y') }}
                            @if($loan->disbursedBy)
                                oleh {{ $loan->disbursedBy->name }}
                            @endif
                        </p>
                    </div>
                @elseif($loan->status === 'pending_disbursement')
                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                        <p class="text-sm text-yellow-800 dark:text-yellow-300">
                            <span class="font-medium">⏳ Menunggu pencairan</span> - Tim Finance akan memproses pinjaman
                            Anda.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Repayment History --}}
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Riwayat Pembayaran</h3>

                @if($loan->repayments->count() > 0)
                    <div class="space-y-3">
                        @foreach($loan->repayments as $repayment)
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center">
                                        <span
                                            class="text-green-600 font-bold text-sm">{{ $repayment->installment_number }}</span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">
                                            Cicilan ke-{{ $repayment->installment_number }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ $repayment->payment_date->format('d M Y') }} •
                                            {{ $repayment->payment_method_label }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="font-semibold text-green-600">{{ $repayment->formatted_amount }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if(!$loan->isPaidOff())
                        <div class="mt-4 p-4 bg-primary-50 dark:bg-primary-900/20 rounded-lg">
                            <p class="text-sm text-primary-800 dark:text-primary-300">
                                <span class="font-medium">Sisa cicilan:</span>
                                {{ $loan->installment_months - $loan->repayments->count() }} kali pembayaran lagi
                            </p>
                        </div>
                    @endif
                @else
                    <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        <p>Belum ada pembayaran cicilan</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>