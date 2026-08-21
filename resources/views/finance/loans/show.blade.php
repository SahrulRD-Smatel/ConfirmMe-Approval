<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('finance.loans.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $loan->loan_number }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Detail Pinjaman Karyawan</p>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-xl">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Loan Summary --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                        <div class="flex items-start justify-between mb-6">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Informasi Pinjaman</h3>
                                <p class="text-sm text-gray-500">{{ $loan->created_at->format('d F Y') }}</p>
                            </div>
                            <x-ui.badge :type="$loan->status_color" size="lg">{{ $loan->status_label }}</x-ui.badge>
                        </div>

                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Jumlah Pinjaman</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $loan->formatted_loan_amount }}
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Cicilan
                                    1-{{ $loan->installment_months - 1 }}</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $loan->formatted_monthly_installment }}
                                </p>
                                @if($loan->last_installment && $loan->last_installment != $loan->monthly_installment)
                                    <p class="text-xs text-gray-500 mt-1">Terakhir: {{ $loan->formatted_last_installment }}
                                    </p>
                                @endif
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Tenor</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white">
                                    {{ $loan->installment_months }} Bulan
                                </p>
                            </div>
                            <div class="p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                                <p class="text-xs text-gray-500 dark:text-gray-400 uppercase">Sisa Hutang</p>
                                <p class="text-lg font-bold text-primary-600">{{ $loan->formatted_outstanding }}</p>
                            </div>
                        </div>

                        {{-- Progress Bar --}}
                        <div class="mt-6">
                            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400 mb-2">
                                <span>Progress Pembayaran</span>
                                <span>{{ number_format($loan->getProgressPercentage(), 1) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-300"
                                    style="width: {{ $loan->getProgressPercentage() }}%"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>Dibayar: {{ $loan->formatted_total_paid }}</span>
                                <span>Total: {{ $loan->formatted_loan_amount }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Repayment History --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                        <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Riwayat Pembayaran</h3>

                        @if($loan->repayments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Cicilan</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Tanggal</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Jumlah</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Metode</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Dicatat Oleh</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                                        @foreach($loan->repayments as $repayment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $repayment->installment_number }}/{{ $loan->installment_months }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $repayment->payment_date->format('d M Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm font-medium text-green-600">
                                                    {{ $repayment->formatted_amount }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $repayment->payment_method_label }}
                                                </td>
                                                <td class="px-4 py-3 text-sm text-gray-500">
                                                    {{ $repayment->recordedBy->name ?? '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-center text-gray-500 dark:text-gray-400 py-8">
                                Belum ada pembayaran cicilan
                            </p>
                        @endif
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Employee Info --}}
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                        <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Info Karyawan</h3>
                        <div class="flex items-center gap-4 mb-4">
                            <img class="h-12 w-12 rounded-full" src="{{ $loan->employee->avatar_url }}" alt="">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $loan->employee->name }}</p>
                                <p class="text-sm text-gray-500">{{ $loan->employee->department }}</p>
                            </div>
                        </div>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-500">Employee ID:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $loan->employee->employee_id ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Bank:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $loan->bank_name ?: $loan->employee->bank_name ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">No. Rekening:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $loan->bank_account_number ?: $loan->employee->bank_account_number ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-500">Nama Rekening:</span>
                                <span
                                    class="text-gray-900 dark:text-white">{{ $loan->bank_account_name ?: $loan->employee->bank_account_name ?? '-' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Disbursement Form --}}
                    @if($loan->status === 'pending_disbursement')
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6"
                            x-data="{ showConfirmModal: false }">
                            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Cairkan Pinjaman</h3>
                            <form id="disbursementForm" action="{{ route('finance.loans.disburse', $loan) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Bukti Transfer (Opsional)
                                        </label>
                                        <input type="file" name="disbursement_proof" accept=".pdf,.jpg,.jpeg,.png"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                            Catatan (Opsional)
                                        </label>
                                        <textarea name="notes" rows="2"
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="Catatan pencairan..."></textarea>
                                    </div>
                                    <button type="button" @click="showConfirmModal = true"
                                        class="w-full py-2 px-4 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium">
                                        Cairkan Pinjaman
                                    </button>
                                </div>
                            </form>

                            {{-- Confirmation Modal --}}
                            <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto"
                                aria-modal="true">
                                <div
                                    class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
                                    {{-- Backdrop --}}
                                    <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                                        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
                                        x-transition:leave-end="opacity-0"
                                        class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/80 transition-opacity"
                                        @click="showConfirmModal = false"></div>

                                    {{-- Modal Content --}}
                                    <div x-show="showConfirmModal" x-transition:enter="ease-out duration-300"
                                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave="ease-in duration-200"
                                        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                        class="relative bg-white dark:bg-gray-800 rounded-xl shadow-xl transform transition-all sm:max-w-lg sm:w-full mx-auto overflow-hidden">

                                        {{-- Header --}}
                                        <div
                                            class="bg-yellow-50 dark:bg-yellow-900/20 px-6 py-4 border-b border-yellow-100 dark:border-yellow-800">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="flex-shrink-0 w-10 h-10 rounded-full bg-yellow-100 dark:bg-yellow-800 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                    </svg>
                                                </div>
                                                <h3 class="text-lg font-bold text-yellow-800 dark:text-yellow-200">
                                                    Konfirmasi Pencairan Dana
                                                </h3>
                                            </div>
                                        </div>

                                        {{-- Body --}}
                                        <div class="px-6 py-5">
                                            <p class="text-gray-600 dark:text-gray-300 mb-4">
                                                Anda akan mencairkan pinjaman dengan detail berikut:
                                            </p>

                                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4 space-y-3">
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Pemohon:</span>
                                                    <span
                                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $loan->employee->name ?? '-' }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Jumlah
                                                        Pinjaman:</span>
                                                    <span
                                                        class="text-lg font-bold text-green-600 dark:text-green-400">{{ $loan->formatted_loan_amount }}</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Tenor:</span>
                                                    <span
                                                        class="text-sm font-medium text-gray-900 dark:text-white">{{ $loan->installment_months }}
                                                        bulan</span>
                                                </div>
                                                <div class="flex justify-between">
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">Bank
                                                        Tujuan:</span>
                                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                                        @if($loan->bank_name || $loan->employee->bank_name)
                                                            {{ $loan->bank_name ?: $loan->employee->bank_name ?? '-' }} -
                                                            {{ $loan->bank_account_number ?: $loan->employee->bank_account_number ?? '-' }}
                                                        @else
                                                            <span class="text-yellow-600">Belum diisi</span>
                                                        @endif
                                                    </span>
                                                </div>
                                            </div>

                                            <div
                                                class="mt-4 p-3 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                                                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                                                    <strong>Perhatian:</strong> Pastikan dana telah ditransfer ke rekening
                                                    karyawan sebelum mengkonfirmasi pencairan ini.
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Footer --}}
                                        <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4 flex justify-end gap-3">
                                            <button type="button" @click="showConfirmModal = false"
                                                class="px-4 py-2 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-primary-50/60 dark:hover:bg-primary-50/600 font-medium">
                                                Batal
                                            </button>
                                            <button type="button"
                                                @click="document.getElementById('disbursementForm').submit()"
                                                class="px-4 py-2 bg-green-600 text-white rounded-xl hover:bg-green-700 font-medium flex items-center gap-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                                Ya, Cairkan Dana
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Repayment Form --}}
                    @if(in_array($loan->status, ['disbursed', 'repaying']))
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Catat Pembayaran</h3>
                            <form action="{{ route('finance.loans.repayment', $loan) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="space-y-4">
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah
                                            Pembayaran * (Cicilan ke-{{ $loan->getNextInstallmentNumber() }})</label>
                                        <div class="relative">
                                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                                            <input type="text" name="amount" required
                                                value="{{ (int) $loan->getInstallmentAmount($loan->getNextInstallmentNumber()) }}"
                                                class="currency-input w-full pl-10 pr-3 py-2 rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                                inputmode="numeric">
                                        </div>
                                        @if($loan->getNextInstallmentNumber() == $loan->installment_months && $loan->last_installment != $loan->monthly_installment)
                                            <p class="text-xs text-yellow-600 mt-1">* Cicilan terakhir disesuaikan untuk
                                                pembulatan</p>
                                        @endif
                                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal
                                            Pembayaran *</label>
                                        <input type="date" name="payment_date" required value="{{ date('Y-m-d') }}"
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Metode
                                            Pembayaran *</label>
                                        <select name="payment_method" required
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                            <option value="salary_deduction">Potong Gaji</option>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="cash">Tunai</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bukti
                                            Bayar (Opsional)</label>
                                        <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png"
                                            class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100">
                                    </div>
                                    <div>
                                        <label
                                            class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan</label>
                                        <textarea name="notes" rows="2"
                                            class="w-full rounded-xl border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                                            placeholder="Catatan pembayaran..."></textarea>
                                    </div>
                                    <button type="submit"
                                        class="w-full py-2 px-4 bg-primary-600 text-white rounded-xl hover:bg-primary-700 font-medium">
                                        Catat Pembayaran
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif

                    {{-- Disbursement Info --}}
                    @if($loan->disbursement_date)
                        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-soft p-6">
                            <h3 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Info Pencairan</h3>
                            <div class="space-y-2 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Tanggal Cair:</span>
                                    <span
                                        class="text-gray-900 dark:text-white">{{ $loan->disbursement_date->format('d M Y') }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Dicairkan Oleh:</span>
                                    <span class="text-gray-900 dark:text-white">{{ $loan->disbursedBy->name ?? '-' }}</span>
                                </div>
                                @if($loan->disbursement_proof)
                                    <div class="pt-2">
                                        <a href="{{ asset('storage/' . $loan->disbursement_proof) }}" target="_blank"
                                            class="text-primary-600 hover:underline text-sm">
                                            Lihat Bukti Transfer →
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>