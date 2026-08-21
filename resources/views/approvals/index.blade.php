<x-app-layout>
    <x-slot name="title">Approval Queue</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Dokumen Menunggu Approval</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Berikut dokumen yang memerlukan approval dari Anda
                </p>
            </div>

            <div class="flex items-center gap-2">
                <x-ui.button href="{{ route('approvals.bulk') }}" variant="secondary" icon="squares-2x2">
                    Bulk Approval
                </x-ui.button>
            </div>
        </div>
    </x-slot>

    <!-- Filters -->
    <x-ui.card class="mb-6">
        <form action="{{ route('approvals.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4">
            <div class="w-full sm:w-48">
                <select name="priority"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Prioritas</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Tinggi</option>
                    <option value="normal" {{ request('priority') === 'normal' ? 'selected' : '' }}>Normal</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Rendah</option>
                </select>
            </div>

            <div class="w-full sm:w-48">
                <select name="approval_type"
                    class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-4 focus:ring-primary-500/20/10">
                    <option value="">Semua Jenis</option>
                    <option value="only" {{ request('approval_type') === 'only' ? 'selected' : '' }}>Approval Only
                    </option>
                    <option value="po" {{ request('approval_type') === 'po' ? 'selected' : '' }}>Approval to PO</option>
                    <option value="invoice" {{ request('approval_type') === 'invoice' ? 'selected' : '' }}>Approval to
                        Invoice</option>
                    <option value="bulk" {{ request('approval_type') === 'bulk' ? 'selected' : '' }}>Approval Bulk
                    </option>
                </select>
            </div>

            <x-ui.button type="submit" variant="secondary">
                Filter
            </x-ui.button>
        </form>
    </x-ui.card>

    <!-- Documents Grid -->
    @if($documents->count() > 0)
        <div class="grid grid-cols-1 gap-5 md:grid-cols-2 lg:grid-cols-3">
            @foreach($documents as $document)
                <x-ui.card :hover="true" class="flex min-h-full flex-col">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <x-ui.badge :status="$document->priority_color">
                            {{ $document->priority_label }}
                        </x-ui.badge>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $document->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 line-clamp-2">
                        {{ $document->title }}
                    </h3>

                    <p class="text-xs text-primary-600 dark:text-primary-400 mb-3">
                        {{ $document->document_number }}
                    </p>

                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 flex-1">
                        {{ Str::limit($document->description, 100) }}
                    </p>

                    <div class="flex items-center justify-between pt-4 border-t border-gray-200/80 dark:border-gray-700/80">
                        <div class="flex items-center gap-2">
                            <img src="{{ $document->user->avatar_url }}" alt="{{ $document->user->name }}"
                                class="w-6 h-6 rounded-full">
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $document->user->name }}</span>
                        </div>

                        <a href="{{ route('approvals.show', $document) }}"
                            class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400">
                            Review →
                        </a>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        @if($documents->hasPages())
            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        @endif
    @else
        <x-ui.card class="text-center py-12">
            <x-icon name="check-circle" class="w-16 h-16 text-success-500 mx-auto mb-4" />
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Semua Beres! 🎉</h3>
            <p class="text-gray-500 dark:text-gray-400">Tidak ada dokumen yang menunggu approval dari Anda.</p>
        </x-ui.card>
    @endif
</x-app-layout>