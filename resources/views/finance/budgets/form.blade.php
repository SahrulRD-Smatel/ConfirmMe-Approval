<x-app-layout>
    <x-slot name="title">{{ isset($budget) ? 'Edit Budget' : 'Tambah Budget' }}</x-slot>

    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('finance.budgets.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400">
                <x-icon name="arrow-left" class="w-5 h-5" />
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">
                {{ isset($budget) ? 'Edit Budget' : 'Tambah Budget' }}
            </h1>
        </div>
    </x-slot>

    <form action="{{ isset($budget) ? route('finance.budgets.update', $budget) : route('finance.budgets.store') }}"
        method="POST" class="max-w-2xl">
        @csrf
        @if(isset($budget)) @method('PUT') @endif

        <x-ui.card class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Budget <span
                            class="text-danger-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $budget->name ?? '') }}" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode <span
                            class="text-danger-500">*</span></label>
                    <input type="text" name="code" value="{{ old('code', $budget->code ?? '') }}" required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 uppercase">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Departemen <span
                            class="text-danger-500">*</span></label>
                    <input type="text" name="department" value="{{ old('department', $budget->department ?? '') }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tahun Fiskal <span
                            class="text-danger-500">*</span></label>
                    <input type="number" name="fiscal_year"
                        value="{{ old('fiscal_year', $budget->fiscal_year ?? date('Y')) }}" required min="2020"
                        max="2099"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Total Anggaran <span
                            class="text-danger-500">*</span></label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-500">Rp</span>
                        <input type="text" name="total_amount"
                            value="{{ old('total_amount', $budget->total_amount ?? '') }}"
                            class="currency-input block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500"
                            inputmode="numeric" required>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $budget->is_active ?? true) ? 'checked' : '' }}
                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Budget Aktif</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="button" variant="secondary"
                    href="{{ route('finance.budgets.index') }}">Batal</x-ui.button>
                <x-ui.button type="submit" icon="check">Simpan</x-ui.button>
            </div>
        </x-ui.card>
    </form>
</x-app-layout>