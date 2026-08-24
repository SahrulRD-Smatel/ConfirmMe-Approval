<x-app-layout>
    <x-slot name="title">{{ isset($budget) ? __('Edit Budget') : __('Tambah Budget') }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('finance.budgets.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">
                    {{ isset($budget) ? __('Edit Budget') : __('Tambah Budget') }}
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Tetapkan identitas, periode, dan batas anggaran dalam satu workspace.') }}</p>
            </div>
        </div>
    </x-slot>

    <form action="{{ isset($budget) ? route('finance.budgets.update', $budget) : route('finance.budgets.store') }}"
        method="POST" class="mx-auto max-w-6xl">
        @csrf
        @if(isset($budget)) @method('PUT') @endif

        <div class="grid grid-cols-1 items-start gap-5 xl:grid-cols-[minmax(0,1fr)_17rem]">
            <div class="min-w-0 space-y-5">
                <x-ui.card class="overflow-hidden rounded-2xl border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
                    <div class="border-b border-gray-200/80 bg-primary-50/50 px-5 py-4 dark:border-gray-800/80 dark:bg-primary-900/10 sm:px-6">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('01 / Identity') }}</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Identitas budget') }}</h2>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Gunakan nama dan kode yang mudah dikenali di laporan keuangan.') }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                        <div class="sm:col-span-2">
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.1em] text-gray-600 dark:text-gray-300">{{ __('Nama Budget') }} <span class="text-danger-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $budget->name ?? '') }}" required
                                class="block h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.1em] text-gray-600 dark:text-gray-300">{{ __('Kode') }} <span class="text-danger-500">*</span></label>
                            <input type="text" name="code" value="{{ old('code', $budget->code ?? '') }}" required
                                class="block h-11 w-full rounded-xl border-gray-300 font-mono text-sm uppercase shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.1em] text-gray-600 dark:text-gray-300">{{ __('Departemen') }} <span class="text-danger-500">*</span></label>
                            <input type="text" name="department" value="{{ old('department', $budget->department ?? '') }}" required
                                class="block h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </x-ui.card>

                <x-ui.card class="overflow-hidden rounded-2xl border-gray-200/80 p-0 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80">
                    <div class="border-b border-gray-200/80 px-5 py-4 dark:border-gray-800/80 sm:px-6">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('02 / Allocation') }}</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Periode dan alokasi') }}</h2>
                        <p class="mt-1 text-xs leading-5 text-gray-500 dark:text-gray-400">{{ __('Tentukan tahun fiskal dan total dana yang tersedia untuk departemen ini.') }}</p>
                    </div>
                    <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 sm:p-6">
                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.1em] text-gray-600 dark:text-gray-300">{{ __('Tahun Fiskal') }} <span class="text-danger-500">*</span></label>
                            <input type="number" name="fiscal_year"
                                value="{{ old('fiscal_year', $budget->fiscal_year ?? date('Y')) }}" required min="2020"
                                max="2099"
                                class="block h-11 w-full rounded-xl border-gray-300 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                        </div>

                        <div>
                            <label class="mb-1.5 block text-xs font-bold uppercase tracking-[0.1em] text-gray-600 dark:text-gray-300">{{ __('Total Anggaran') }} <span class="text-danger-500">*</span></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-sm font-semibold text-gray-400">Rp</span>
                                <input type="text" name="total_amount"
                                    value="{{ old('total_amount', $budget->total_amount ?? '') }}"
                                    class="currency-input block h-11 w-full rounded-xl border-gray-300 pl-10 pr-3 text-sm font-semibold shadow-sm focus:border-primary-500 focus:ring-primary-500/20 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                                    inputmode="numeric" required>
                            </div>
                        </div>

                        <div class="sm:col-span-2 flex items-center rounded-xl border border-gray-200/80 bg-gray-50/70 px-4 py-3 dark:border-gray-700/80 dark:bg-gray-800/50">
                            <label class="flex cursor-pointer items-center gap-3">
                                <input type="checkbox" name="is_active" value="1" {{ old('is_active', $budget->is_active ?? true) ? 'checked' : '' }}
                                    class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                                <span>
                                    <span class="block text-sm font-semibold text-gray-800 dark:text-gray-200">{{ __('Budget Aktif') }}</span>
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('Tersedia untuk dipakai dalam transaksi dan laporan.') }}</span>
                                </span>
                            </label>
                        </div>
                    </div>
                </x-ui.card>
            </div>

            <aside class="space-y-4 xl:sticky xl:top-6">
                <div class="overflow-hidden rounded-2xl border border-gray-200/80 bg-white shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900">
                    <div class="border-b border-gray-200/80 bg-gray-50/80 px-5 py-4 dark:border-gray-800/80 dark:bg-gray-800/50">
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Setup checklist') }}</p>
                        <h2 class="mt-1 text-sm font-bold text-gray-900 dark:text-white">{{ __('Sebelum disimpan') }}</h2>
                    </div>
                    <div class="space-y-1 p-3">
                        <div class="flex items-start gap-3 rounded-xl bg-primary-50/70 px-3 py-3 dark:bg-primary-900/10">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-primary-100 text-[10px] font-bold text-primary-700 dark:bg-primary-900/40 dark:text-primary-300">01</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Identity') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Nama, kode, departemen') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-blue-100 text-[10px] font-bold text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">02</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Allocation') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Tahun dan total anggaran') }}</p></div>
                        </div>
                        <div class="flex items-start gap-3 rounded-xl px-3 py-3">
                            <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-lg bg-success-100 text-[10px] font-bold text-success-700 dark:bg-success-900/40 dark:text-success-300">03</span>
                            <div><p class="text-xs font-semibold text-gray-800 dark:text-gray-200">{{ __('Activation') }}</p><p class="mt-0.5 text-[10px] leading-4 text-gray-500">{{ __('Status penggunaan budget') }}</p></div>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-primary-100 bg-primary-50/60 p-4 dark:border-primary-900/40 dark:bg-primary-900/10">
                    <div class="flex items-center gap-2 text-primary-800 dark:text-primary-300">
                        <x-icon name="information-circle" class="h-4 w-4" />
                        <p class="text-xs font-semibold">{{ __('Format nominal') }}</p>
                    </div>
                    <p class="mt-2 text-xs leading-5 text-primary-700/80 dark:text-primary-300/70">{{ __('Masukkan total anggaran dalam Rupiah. Field akan memformat nominal secara otomatis.') }}</p>
                </div>
            </aside>
        </div>

        <div class="sticky bottom-4 z-20 flex flex-wrap items-center justify-end gap-3 rounded-2xl border border-gray-200/80 bg-white/95 p-3 shadow-soft-lg backdrop-blur dark:border-gray-800/80 dark:bg-gray-900/95">
            <x-ui.button type="button" variant="secondary" href="{{ route('finance.budgets.index') }}">{{ __('Batal') }}</x-ui.button>
            <x-ui.button type="submit" icon="check">{{ __('Simpan') }}</x-ui.button>
        </div>
    </form>
</x-app-layout>