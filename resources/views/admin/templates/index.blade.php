<x-app-layout>
    <x-slot name="title">{{ __('Template Dokumen') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Template Dokumen') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Kelola template pengajuan dokumen dari satu katalog yang ringkas dan mudah dipindai.') }}</p>
            </div>
            <x-ui.button href="{{ route('admin.templates.create') }}" icon="plus">{{ __('Buat Template') }}</x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-5">
        <div class="flex flex-col gap-3 rounded-2xl border border-gray-200/80 bg-white/80 px-4 py-3 shadow-sm dark:border-gray-800/80 dark:bg-gray-900/80 sm:flex-row sm:items-center sm:justify-between sm:px-5">
            <div class="flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 dark:bg-primary-900/30">
                    <x-icon name="document-text" class="h-4.5 w-4.5 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Katalog template pengajuan') }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('Setiap template menentukan jalur approval yang tersedia.') }}</p>
                </div>
            </div>
            <span class="w-fit rounded-full bg-gray-100 px-3 py-1.5 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ __('Template registry') }}</span>
        </div>

        <div class="grid grid-cols-1 items-stretch gap-4 md:grid-cols-2 xl:grid-cols-3">
            @forelse($templates as $template)
                <x-ui.card class="group relative flex h-full flex-col overflow-hidden rounded-2xl border-gray-200/80 p-0 shadow-soft transition duration-200 hover:-translate-y-0.5 hover:shadow-soft-lg dark:border-gray-800/80">
                    <div class="h-1 bg-gradient-to-r from-primary-500 via-primary-400 to-primary-200 dark:from-primary-700 dark:via-primary-600 dark:to-primary-900"></div>
                    <div class="flex flex-1 flex-col p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gray-100 text-gray-500 transition group-hover:bg-primary-50 group-hover:text-primary-600 dark:bg-gray-800 dark:text-gray-400 dark:group-hover:bg-primary-900/30 dark:group-hover:text-primary-400">
                                    <x-icon name="document-text" class="h-5 w-5" />
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $template->name }}</h3>
                                    <p class="mt-1 truncate font-mono text-[10px] uppercase tracking-[0.12em] text-gray-400">{{ $template->code }}</p>
                                </div>
                            </div>
                            <x-ui.badge :status="$template->is_active ? 'success' : 'danger'">
                                {{ $template->is_active ? __('Aktif') : __('Nonaktif') }}
                            </x-ui.badge>
                        </div>

                        <p class="mt-4 min-h-[2.75rem] line-clamp-2 text-xs leading-5 text-gray-600 dark:text-gray-400">{{ Str::limit($template->description, 80) }}</p>

                        <div class="mt-4 flex items-center justify-between gap-3 rounded-xl border border-gray-100 bg-gray-50/80 px-3 py-2.5 dark:border-gray-800 dark:bg-gray-800/50">
                            <span class="text-[10px] font-bold uppercase tracking-[0.12em] text-gray-400">{{ __('Approval flow') }}</span>
                            <span class="max-w-[55%] truncate text-right text-xs font-semibold text-gray-700 dark:text-gray-300">{{ __($template->approval_type_label) }}</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-2 border-t border-gray-200/80 bg-gray-50/60 px-5 py-3 dark:border-gray-800/80 dark:bg-gray-800/30">
                        <span class="text-[10px] font-medium uppercase tracking-[0.12em] text-gray-400">{{ __('Template action') }}</span>
                        <div class="flex items-center gap-2">
                            <x-ui.button href="{{ route('admin.templates.edit', $template) }}" variant="secondary" size="sm">{{ __('Edit') }}</x-ui.button>
                            <form action="{{ route('admin.templates.destroy', $template) }}" method="POST"
                                onsubmit="confirmAction(event, '{{ __('Hapus template?') }}')">
                                @csrf @method('DELETE')
                                <x-ui.button type="submit" variant="danger" size="sm">{{ __('Hapus') }}</x-ui.button>
                            </form>
                        </div>
                    </div>
                </x-ui.card>
            @empty
                <div class="col-span-full">
                    <x-ui.card class="rounded-2xl border-dashed py-14 text-center shadow-soft dark:border-gray-700">
                        <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-primary-50 dark:bg-primary-900/30">
                            <x-icon name="document-text" class="h-7 w-7 text-primary-400" />
                        </div>
                        <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ __('Belum ada template') }}</p>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ __('Mulai dengan membuat template pengajuan pertama.') }}</p>
                        <x-ui.button href="{{ route('admin.templates.create') }}" class="mt-4">{{ __('Buat Template Pertama') }}</x-ui.button>
                    </x-ui.card>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>