<x-app-layout>
    <x-slot name="title">Hasil Pencarian</x-slot>

    <x-slot name="header">
        <div>
            <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Hasil Pencarian</h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if(request('q'))
                    Menampilkan hasil untuk "{{ request('q') }}"
                @else
                    Cari dokumen berdasarkan nomor, judul, atau deskripsi
                @endif
            </p>
        </div>
    </x-slot>

    <!-- Search Form -->
    <x-ui.card class="mb-6">
        <form action="{{ route('search') }}" method="GET" class="flex flex-col gap-3 sm:flex-row">
            <div class="flex-1">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <x-icon name="eye" class="h-5 w-5 text-gray-400" />
                    </div>
                    <input type="text" name="q" value="{{ request('q') }}"
                        class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10"
                        placeholder="Ketik kata kunci pencarian..." autofocus>
                </div>
            </div>
            <x-ui.button type="submit">
                Cari
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Results -->
    @if(request('q'))
        <x-ui.card :padding="false">
            <div class="border-b border-gray-200/80 p-5 dark:border-gray-700/80">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Ditemukan <strong>{{ $documents->total() }}</strong> dokumen
                </p>
            </div>

            <div class="divide-y divide-gray-200/80 dark:divide-gray-700/80">
                @forelse($documents as $document)
                    <a href="{{ route('documents.show', $document) }}"
                        class="block p-5 transition-colors hover:bg-primary-50/60 dark:hover:bg-gray-800/80/80">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                                        {{ $document->document_number }}
                                    </span>
                                    <x-ui.badge :status="$document->status_color" size="sm">
                                        {{ $document->status_label }}
                                    </x-ui.badge>
                                </div>
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                                    {{ $document->title }}
                                </h3>
                                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                    {{ Str::limit($document->description, 150) }}
                                </p>
                                <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                                    <span>{{ $document->user->name }}</span>
                                    <span>{{ $document->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                            <x-icon name="chevron-right" class="w-5 h-5 text-gray-400 flex-shrink-0" />
                        </div>
                    </a>
                @empty
                    <div class="p-14 text-center">
                        <x-icon name="eye" class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                        <p class="text-gray-500 dark:text-gray-400">Tidak ada dokumen yang ditemukan</p>
                    </div>
                @endforelse
            </div>

            @if($documents->hasPages())
                <div class="px-6 py-4 border-t border-gray-200/80 dark:border-gray-700/80">
                    {{ $documents->links() }}
                </div>
            @endif
        </x-ui.card>
    @endif
</x-app-layout>