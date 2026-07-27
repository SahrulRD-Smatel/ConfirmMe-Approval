<x-app-layout>
    <x-slot name="title">Template Dokumen</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Template Dokumen</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola template pengajuan dokumen</p>
            </div>
            <x-ui.button href="{{ route('admin.templates.create') }}" icon="plus">Buat Template</x-ui.button>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($templates as $template)
            <x-ui.card>
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                        <p class="text-xs text-gray-500">{{ $template->code }}</p>
                    </div>
                    <x-ui.badge :status="$template->is_active ? 'success' : 'danger'">
                        {{ $template->is_active ? 'Aktif' : 'Nonaktif' }}
                    </x-ui.badge>
                </div>

                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ Str::limit($template->description, 80) }}</p>

                <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                    <span>Tipe: {{ $template->approval_type_label }}</span>
                </div>

                <div class="flex items-center gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <x-ui.button href="{{ route('admin.templates.edit', $template) }}" variant="secondary"
                        size="sm">Edit</x-ui.button>
                    <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                        onsubmit="confirmAction(event, 'Hapus template?')">
                        @csrf @method('DELETE')
                        <x-ui.button type="submit" variant="danger" size="sm">Hapus</x-ui.button>
                    </form>
                </div>
            </x-ui.card>
        @empty
            <div class="col-span-full">
                <x-ui.card class="text-center py-12">
                    <x-icon name="document-text" class="w-12 h-12 text-gray-300 mx-auto mb-4" />
                    <p class="text-gray-500">Belum ada template</p>
                    <x-ui.button href="{{ route('admin.templates.create') }}" class="mt-4">Buat Template
                        Pertama</x-ui.button>
                </x-ui.card>
            </div>
        @endforelse
    </div>
</x-app-layout>