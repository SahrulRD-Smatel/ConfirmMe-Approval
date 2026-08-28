@props(['document'])
            <!-- Activity Log -->
            <x-ui.card>
                <h2 class="mb-5 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Riwayat Aktivitas') }}</h2>

                <div class="space-y-3 max-h-80 overflow-y-auto">
                    @forelse($document->approvalLogs->take(10) as $log)
                        <div class="flex items-start gap-3 text-sm">
                            <div
                                class="flex-shrink-0 w-8 h-8 rounded-full bg-{{ $log->action_color }}-100 dark:bg-{{ $log->action_color }}-900/30 flex items-center justify-center">
                                <x-icon :name="$log->action_icon"
                                    class="w-4 h-4 text-{{ $log->action_color }}-600 dark:text-{{ $log->action_color }}-400" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-gray-900 dark:text-white">
                                    <span class="font-medium">{{ $log->user->name ?? __('System') }}</span>
                                    {{ $log->action_label }}
                                </p>
                                <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500 text-center py-4">{{ __('Belum ada aktivitas') }}</p>
                    @endforelse
                </div>
            </x-ui.card>
