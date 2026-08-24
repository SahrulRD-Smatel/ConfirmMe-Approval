<x-app-layout>
    <x-slot name="title">{{ __('Audit Log') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Audit Log') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Log aktivitas sistem') }}</p>
            </div>
            <x-ui.button href="{{ route('admin.audit-logs.export') }}" variant="secondary" icon="arrow-down-tray">
                {{ __('Export PDF') }}
            </x-ui.button>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('admin.audit-logs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                    placeholder="{{ __('Cari aktivitas...') }}">
            </div>
            <div class="w-full sm:w-48">
                <select name="event"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">{{ __('Semua Event') }}</option>
                    <option value="created" {{ request('event') === 'created' ? 'selected' : '' }}>{{ __('Created') }}</option>
                    <option value="updated" {{ request('event') === 'updated' ? 'selected' : '' }}>{{ __('Updated') }}</option>
                    <option value="deleted" {{ request('event') === 'deleted' ? 'selected' : '' }}>{{ __('Deleted') }}</option>
                </select>
            </div>
            <x-ui.button type="submit" variant="secondary">{{ __('Filter') }}</x-ui.button>
        </form>
    </x-ui.card>

    <!-- Logs Table -->
    <x-ui.card :padding="false">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full divide-y divide-gray-200/80 dark:divide-gray-700/80">
                <thead class="bg-gray-50/80 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Waktu') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('User') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Event') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('Model') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('IP Address') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200/80 dark:divide-gray-700/80">
                    @forelse($logs as $log)
                                    <tr class="hover:bg-primary-50/60 dark:hover:bg-gray-800/80">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $log->user->name ?? 'System' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <x-ui.badge :status="match ($log->event) {
                            'created' => 'success',
                            'updated' => 'info',
                            'deleted' => 'danger',
                            default => 'secondary'
                        }">{{ __(ucfirst($log->event)) }}</x-ui.badge>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-gray-300">
                                            {{ class_basename($log->auditable_type) }} #{{ $log->auditable_id }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $log->ip_address ?? '-' }}
                                        </td>
                                    </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">{{ __('Tidak ada log') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">{{ $logs->links() }}</div>
        @endif
    </x-ui.card>
</x-app-layout>