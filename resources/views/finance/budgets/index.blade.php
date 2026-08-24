<x-app-layout>
    <x-slot name="title">{{ __('Kelola Anggaran') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Kelola Anggaran') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Manajemen budget departemen') }}</p>
            </div>
            <x-ui.button href="{{ route('finance.budgets.create') }}" icon="plus">{{ __('Tambah Budget') }}</x-ui.button>
        </div>
    </x-slot>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <x-ui.stat-card title="{{ __('Total Budget') }}" :value="'Rp ' . number_format($totalBudget, 0, ',', '.')"
            icon="banknotes" />
        <x-ui.stat-card title="{{ __('Terpakai') }}" :value="'Rp ' . number_format($usedBudget, 0, ',', '.')"
            icon="arrow-trending-down" status="warning" />
        <x-ui.stat-card title="{{ __('Tersedia') }}" :value="'Rp ' . number_format($availableBudget, 0, ',', '.')"
            icon="arrow-trending-up" status="success" />
        <x-ui.stat-card title="{{ __('Budget Aktif') }}" :value="$activeBudgets" icon="check-circle" status="info" />
    </div>

    <!-- Budgets Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-[760px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Nama Budget') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Departemen') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Terpakai') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Progress') }}</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    @forelse($budgets as $budget)
                        <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $budget->name }}</div>
                                <div class="text-xs text-gray-500">{{ $budget->code }} | {{ $budget->fiscal_year }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">{{ $budget->department }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white font-medium">
                                Rp {{ number_format($budget->total_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 dark:text-gray-300">
                                Rp {{ number_format($budget->used_amount, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div class="h-2 rounded-full {{ $budget->usage_percentage > 80 ? 'bg-danger-500' : ($budget->usage_percentage > 50 ? 'bg-yellow-500' : 'bg-success-500') }}"
                                        style="width: {{ min($budget->usage_percentage, 100) }}%"></div>
                                </div>
                                <span
                                    class="text-xs text-gray-500">{{ number_format($budget->usage_percentage, 1) }}%</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-wrap items-center justify-end gap-2">
                                    <a href="{{ route('finance.budgets.edit', $budget) }}"
                                        class="text-primary-600 hover:text-primary-800">
                                        <x-icon name="pencil" class="w-5 h-5" />
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">{{ __('Belum ada budget') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($budgets->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">{{ $budgets->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>