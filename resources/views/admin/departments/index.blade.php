<x-app-layout>
    <x-slot name="title">{{ __('Manajemen Departemen') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Manajemen Departemen') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Kelola departemen perusahaan. Perubahan nama otomatis update ke semua user terkait.') }}</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-green-200/80 bg-green-50/90 px-4 py-3.5 text-sm text-green-700 shadow-sm dark:border-green-800/60 dark:bg-green-900/20 dark:text-green-400">
                <x-icon name="check-circle" class="mt-0.5 h-5 w-5 shrink-0 text-green-500" />
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if(session('error'))
            <div class="flex items-start gap-3 rounded-2xl border border-red-200/80 bg-red-50/90 px-4 py-3.5 text-sm text-red-700 shadow-sm dark:border-red-800/60 dark:bg-red-900/20 dark:text-red-400">
                <x-icon name="exclamation-circle" class="mt-0.5 h-5 w-5 shrink-0 text-red-500" />
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-[minmax(18rem,0.35fr)_minmax(0,1fr)]">
            {{-- Add Department Form --}}
            <aside class="lg:sticky lg:top-6">
                <div class="overflow-hidden rounded-[1.75rem] border border-primary-200/80 bg-primary-50/60 shadow-soft-lg shadow-primary-900/[0.025] dark:border-primary-800/60 dark:bg-primary-900/10">
                    <div class="border-b border-primary-200/70 px-5 py-5 dark:border-primary-800/60">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-2xl bg-primary-600 text-white shadow-soft">
                                <x-icon name="plus-circle" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-primary-600 dark:text-primary-400">{{ __('Create record') }}</p>
                                <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Tambah Departemen') }}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <form action="{{ route('admin.departments.store') }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Nama Departemen') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="{{ __('contoh: Marketing') }}"
                                    class="h-11 w-full rounded-xl border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @error('name')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Kode') }} <span class="text-red-500">*</span></label>
                                <input type="text" name="code" value="{{ old('code') }}" required placeholder="{{ __('contoh: MKT') }}" maxlength="20"
                                    class="h-11 w-full rounded-xl border border-gray-200 bg-white px-3.5 text-sm uppercase text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                @error('code')<p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Direktur Role') }}</label>
                                <select name="director_role"
                                    class="h-11 w-full rounded-xl border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    <option value="">— {{ __('Tidak ada') }} —</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role }}" {{ old('director_role') === $role ? 'selected' : '' }}>
                                            {{ role_label($role) }}
                                        </option>
                                    @endforeach
                                </select>
                                <p class="mt-1.5 text-xs leading-5 text-gray-400">{{ __('Role yang menjadi direktur/kepala departemen ini') }}</p>
                            </div>
                            <div>
                                <label class="mb-2 block text-xs font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">{{ __('Urutan') }}</label>
                                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0"
                                    class="h-11 w-full rounded-xl border border-gray-200 bg-white px-3.5 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                            </div>
                            <button type="submit"
                                class="flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-primary-600 px-4 text-sm font-semibold text-white shadow-soft shadow-primary-900/10 transition hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                <x-icon name="plus" class="h-4 w-4" />
                                {{ __('Tambah Departemen') }}
                            </button>
                        </form>
                    </div>
                </div>
            </aside>

            {{-- Department List --}}
            <section class="overflow-hidden rounded-[1.75rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/95">
                <div class="flex flex-col gap-3 border-b border-gray-200/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-gray-800/80">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gray-100 text-primary-600 dark:bg-gray-800 dark:text-primary-400">
                            <x-icon name="building-office" class="h-5 w-5" />
                        </div>
                        <div>
                            <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Organization directory') }}</p>
                            <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Daftar Departemen') }}</h2>
                        </div>
                    </div>
                    <span class="inline-flex w-fit items-center rounded-full bg-gray-100 px-3 py-1.5 text-xs font-semibold text-gray-500 dark:bg-gray-800 dark:text-gray-400">{{ $departments->count() }} {{ __('departemen') }}</span>
                </div>

                <div class="divide-y divide-gray-200/80 dark:divide-gray-800/80">
                    @forelse($departments as $dept)
                        <article class="group px-5 py-4 transition-colors hover:bg-primary-50/30 dark:hover:bg-primary-900/10 sm:px-6" x-data="{ editing: false }">
                            {{-- View Mode --}}
                            <div x-show="!editing" class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex min-w-0 items-center gap-3">
                                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-700 dark:bg-primary-900/30 dark:text-primary-400">
                                        <span class="text-xs font-bold tracking-wide">{{ $dept->code }}</span>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <p class="truncate text-sm font-semibold text-gray-900 dark:text-white">{{ $dept->name }}</p>
                                            @if(!$dept->is_active)
                                                <span class="rounded-full bg-red-50 px-2 py-0.5 text-[9px] font-bold uppercase tracking-[0.12em] text-red-600 dark:bg-red-900/20 dark:text-red-400">{{ __('Nonaktif') }}</span>
                                            @endif
                                        </div>
                                        <div class="mt-1 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-gray-400">
                                            @if($dept->director_role)
                                                <span>{{ __('Direktur') }}: <span class="font-semibold text-primary-600 dark:text-primary-400">{{ role_label($dept->director_role) }}</span></span>
                                            @else
                                                <span class="italic">{{ __('Tanpa direktur') }}</span>
                                            @endif
                                            <span class="text-gray-300 dark:text-gray-700">•</span>
                                            <span>{{ \App\Models\User::where('department', $dept->name)->count() }} {{ __('user') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex shrink-0 items-center gap-2 sm:opacity-70 sm:transition-opacity sm:group-hover:opacity-100">
                                    <button @click="editing = true"
                                        class="inline-flex h-9 items-center gap-2 rounded-xl border border-gray-200 px-3 text-xs font-semibold text-gray-600 transition hover:border-primary-200 hover:bg-primary-50 hover:text-primary-600 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-primary-900/20">
                                        <x-icon name="pencil-square" class="h-4 w-4" />
                                        <span>{{ __('Edit') }}</span>
                                    </button>
                                    @if(\App\Models\User::where('department', $dept->name)->count() === 0)
                                        <form action="{{ route('admin.departments.destroy', $dept) }}" method="POST"
                                            onsubmit="confirmAction(event, '{{ __('Hapus departemen') }} {{ $dept->name }}?')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-red-200/80 text-red-500 transition hover:bg-red-50 dark:border-red-900/50 dark:hover:bg-red-900/20">
                                                <x-icon name="trash" class="h-4 w-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>

                            {{-- Edit Mode --}}
                            <form x-show="editing" x-cloak action="{{ route('admin.departments.update', $dept) }}" method="POST"
                                class="rounded-2xl border border-primary-200/80 bg-primary-50/40 p-4 dark:border-primary-800/60 dark:bg-primary-900/10">
                                @csrf @method('PUT')
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
                                    <div>
                                        <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">{{ __('Nama') }}</label>
                                        <input type="text" name="name" value="{{ $dept->name }}" required
                                            class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">{{ __('Kode') }}</label>
                                        <input type="text" name="code" value="{{ $dept->code }}" required maxlength="20"
                                            class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm uppercase text-gray-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">{{ __('Direktur Role') }}</label>
                                        <select name="director_role"
                                            class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                            <option value="">— {{ __('Tidak ada') }} —</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role }}" {{ $dept->director_role === $role ? 'selected' : '' }}>{{ role_label($role) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="mb-1.5 block text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500">{{ __('Urutan') }}</label>
                                        <input type="number" name="sort_order" value="{{ $dept->sort_order }}" min="0"
                                            class="h-10 w-full rounded-xl border border-gray-200 bg-white px-3 text-sm text-gray-900 outline-none focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white">
                                    </div>
                                </div>
                                <div class="mt-4 flex flex-col gap-3 border-t border-primary-200/70 pt-3 sm:flex-row sm:items-center dark:border-primary-800/60">
                                    <label class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }}
                                            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500/20">
                                        {{ __('Aktif') }}
                                    </label>
                                    <div class="flex items-center justify-end gap-2 sm:ml-auto">
                                        <button type="button" @click="editing = false"
                                            class="rounded-xl px-3 py-2 text-sm font-semibold text-gray-600 transition hover:bg-white dark:text-gray-300 dark:hover:bg-gray-800">
                                            {{ __('Batal') }}
                                        </button>
                                        <button type="submit"
                                            class="rounded-xl bg-primary-600 px-4 py-2 text-sm font-semibold text-white shadow-soft transition hover:bg-primary-700 focus:outline-none focus:ring-4 focus:ring-primary-500/20">
                                            {{ __('Simpan') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </article>
                    @empty
                        <div class="px-6 py-16 text-center">
                            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-3xl bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                                <x-icon name="building-office" class="h-8 w-8" />
                            </div>
                            <p class="mt-4 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Belum ada departemen') }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ __('Gunakan panel di sebelah kiri untuk membuat departemen pertama.') }}</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-app-layout>