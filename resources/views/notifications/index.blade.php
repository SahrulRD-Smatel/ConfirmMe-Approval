<x-app-layout>
    <x-slot name="title">Notifikasi</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Notifikasi</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Semua notifikasi dan pemberitahuan Anda
                </p>
            </div>

            @if($notifications->where('read_at', null)->count() > 0)
                <form action="{{ route('notifications.read-all') }}" method="POST">
                    @csrf
                    <x-ui.button type="submit" variant="secondary" size="sm">
                        Tandai Semua Dibaca
                    </x-ui.button>
                </form>
            @endif
        </div>
    </x-slot>

    <x-ui.card :padding="false">
        <div class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
            @forelse($notifications as $notification)
                <div
                    class="p-5 {{ $notification->read_at ? 'bg-white dark:bg-gray-900' : 'bg-primary-50 dark:bg-primary-900/10' }} hover:bg-primary-50/60 dark:hover:bg-gray-800/80 transition-colors">
                    <div class="flex items-start gap-4">
                        <div
                            class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            <x-icon name="bell" class="w-5 h-5 text-primary-600 dark:text-primary-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-white">
                                    {{ $notification->data['title'] ?? 'Notifikasi' }}
                                </p>
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                {{ $notification->data['message'] ?? '' }}
                            </p>
                            @if(isset($notification->data['action_url']))
                                <a href="{{ $notification->data['action_url'] }}"
                                    class="mt-2 inline-flex items-center text-sm text-primary-600 dark:text-primary-400 hover:underline">
                                    Lihat Detail →
                                </a>
                            @endif
                        </div>
                        @if(!$notification->read_at)
                            <form action="{{ route('notifications.read', $notification) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
                                    title="Tandai Dibaca">
                                    <x-icon name="check" class="w-5 h-5" />
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-12 text-center">
                    <x-icon name="bell-slash" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">Tidak ada notifikasi</p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">
                {{ $notifications->links() }}
            </div>
        @endif
    </x-ui.card>
</x-app-layout>