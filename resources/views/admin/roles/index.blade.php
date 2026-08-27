<x-app-layout>
    <x-slot name="title">{{ __('Kelola Role & Permission') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Role & Permission') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ __('Atur hak akses secara dinamis untuk mengontrol visibilitas menu dan fitur setiap divisi.') }}</p>
            </div>
            <x-ui.button href="{{ route('admin.roles.create') }}" icon="plus" class="shadow-soft shadow-primary-500/20">{{ __('Tambah Role') }}</x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-5">
        <!-- <section class="rounded-[1.75rem] border border-gray-200/80 bg-white/90 p-5 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/90 sm:p-6">
            <div class="flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-3">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                        <x-icon name="shield-check" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Role directory</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">Access profiles</h2>
                        <p class="mt-1 max-w-2xl text-xs leading-5 text-gray-500 dark:text-gray-400">Buka hanya role yang ingin ditinjau. Daftar permission di dalam setiap grup memiliki scroll internal agar halaman tetap ringkas.</p>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-2 text-[10px] font-bold uppercase tracking-[0.12em] text-gray-500 dark:text-gray-400">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-2.5 py-1.5 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400"><span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>Aktif</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-2.5 py-1.5 text-gray-500 dark:bg-gray-800 dark:text-gray-400"><span class="h-1.5 w-1.5 rounded-full bg-gray-400"></span>Tidak aktif</span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-primary-50 px-2.5 py-1.5 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400"><span class="h-1.5 w-1.5 rounded-full bg-primary-500"></span>Sistem</span>
                </div>
            </div>
        </section> -->

        @foreach($roles as $role)
        @php
            $rolePerms = $role->permissions->pluck('name')->toArray();
            $isProtected = in_array($role->name, ['admin','staff','supervisor','hrd','direktur','finance','purchasing','direktur_operasional','hrd_coordinator','manager','direktur_hr','direktur_finance','direktur_it','direktur_sales','presiden_direktur']);
            
            // Generate icon based on role name
            $icon = 'users';
            $bgColor = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
            if($role->name === 'admin') { $icon = 'shield-check'; $bgColor = 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'; }
            elseif(str_starts_with($role->name, 'direktur') || $role->name === 'presiden_direktur') { $icon = 'building-office-2'; $bgColor = 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'; }
            elseif($role->name === 'hrd' || $role->name === 'hrd_coordinator') { $icon = 'user-group'; $bgColor = 'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400'; }
            elseif($role->name === 'finance' || $role->name === 'purchasing') { $icon = 'banknotes'; $bgColor = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'; }
            elseif($role->name === 'manager' || $role->name === 'supervisor') { $icon = 'star'; $bgColor = 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'; }
        @endphp

        <section x-data="{ open: false }"
             class="group overflow-hidden rounded-[1.5rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.02] transition-all duration-300 dark:border-gray-800/80 dark:bg-gray-900/95">
            {{-- Header (Clickable) --}}
            <div @click="open = !open"
                    class="flex w-full items-center justify-between gap-4 p-4 text-left transition-colors hover:bg-gray-50/80 focus:outline-none focus:ring-4 focus:ring-inset focus:ring-primary-500/10 sm:p-5 cursor-pointer">
                <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $bgColor }} transition-transform duration-300 group-hover:scale-105 sm:h-12 sm:w-12">
                        <x-icon name="{{ $icon }}" class="h-6 w-6" />
                    </div>
                    <div class="min-w-0">
                        <div class="flex flex-wrap items-center gap-2 sm:gap-3">
                            <h3 class="truncate text-base font-bold text-gray-900 dark:text-white sm:text-lg">{{ role_label($role->name) }}</h3>
                            @if($isProtected)
                                <span title="{{ __('Role bawaan sistem yang tidak dapat dihapus') }}" data-tip="{{ __('Role bawaan sistem yang tidak dapat dihapus') }}" class="cursor-help rounded-full bg-primary-100 px-2 py-0.5 text-[9px] font-bold uppercase tracking-[0.12em] text-primary-700 dark:bg-primary-900/50 dark:text-primary-400">{{ __('Permanen') }}</span>
                            @endif
                        </div>
                        <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center gap-1.5"><x-icon name="check-circle" class="h-3.5 w-3.5 text-emerald-500" /><b>{{ count($rolePerms) }}</b> {{ __('Akses') }}</span>
                            <span class="hidden text-gray-300 dark:text-gray-600 sm:inline">•</span>
                            <span class="inline-flex items-center gap-1.5"><x-icon name="user" class="h-3.5 w-3.5" /><b>{{ $role->users()->count() }}</b> {{ __('Pengguna') }}</span>
                        </div>
                    </div>
                </div>

                <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                    <div class="flex items-center gap-1 opacity-100 transition-opacity duration-300 sm:opacity-0 sm:group-hover:opacity-100">
                        <a href="{{ route('admin.roles.edit', $role) }}" @click.stop
                           class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-primary-50 text-primary-600 transition-colors hover:bg-primary-100 dark:bg-primary-900/20 dark:hover:bg-primary-900/40" data-tip="{{ __('Edit Hak Akses') }}" aria-label="{{ __('Edit Hak Akses') }}">
                            <x-icon name="pencil" class="h-4 w-4" />
                        </a>
                        @if(!$isProtected)
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" @click.stop onsubmit="confirmAction(event, '{{ __('Hapus role') }} {{ $role->name }} {{ __('beserta hak aksesnya?') }}')">
                            @csrf @method('DELETE')
                            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-danger-50 text-danger-600 transition-colors hover:bg-danger-100 dark:bg-danger-900/20 dark:hover:bg-danger-900/40" data-tip="{{ __('Hapus Role') }}" aria-label="{{ __('Hapus Role') }}">
                                <x-icon name="trash" class="h-4 w-4" />
                            </button>
                        </form>
                        @endif
                    </div>
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gray-100 text-gray-400 transition-colors group-hover:text-gray-600 dark:bg-gray-800 dark:group-hover:text-gray-300">
                        <x-icon name="chevron-down" class="h-4 w-4 transition-transform duration-300" x-bind:class="open ? 'rotate-180' : ''" />
                    </span>
                </div>
            </div>

            {{-- Body (Permissions Matrix) --}}
            <div x-show="open"
                 x-collapse
                 class="border-t border-gray-200/80 bg-gray-50/50 dark:border-gray-800/80 dark:bg-gray-950/30">
                <div class="p-4 sm:p-5">
                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-4">
                        @foreach($permGroups as $groupName => $groupPerms)
                            @php
                                $groupPermNames = array_column($groupPerms, 'name');
                                $activeInGroup = array_intersect($groupPermNames, $rolePerms);
                                $hasAny = count($activeInGroup) > 0;
                                $isAll = count($activeInGroup) === count($groupPerms);
                            @endphp

                            <div class="flex max-h-72 min-h-0 flex-col overflow-hidden rounded-2xl border border-gray-200/80 bg-white p-3.5 shadow-sm transition-colors dark:border-gray-800/80 dark:bg-gray-900 {{ $hasAny ? 'ring-1 ring-primary-500/20' : 'opacity-75' }}">
                                @if($isAll)
                                    <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden">
                                        <div class="absolute transform rotate-45 bg-emerald-500 text-white text-[9px] font-bold py-1 right-[-35px] top-[15px] w-[120px] text-center shadow-soft">
                                            {{ __('FULL AKSES') }}
                                        </div>
                                    </div>
                                @endif

                                <div class="mb-3 flex shrink-0 items-center gap-2 border-b border-gray-100 pb-3 dark:border-gray-800">
                                    <h4 class="min-w-0 truncate font-bold text-[10px] uppercase tracking-[0.14em] text-gray-700 dark:text-gray-200">{{ $groupName }}</h4>
                                    @if($hasAny && !$isAll)
                                        <span class="shrink-0 rounded-full bg-primary-100 px-2 py-0.5 text-[9px] font-semibold text-primary-700 dark:bg-primary-900/50 dark:text-primary-400">{{ count($activeInGroup) }}/{{ count($groupPerms) }}</span>
                                    @endif
                                </div>

                                <div class="min-h-0 flex-1 overflow-y-auto pr-1">
                                    <div class="grid grid-cols-1 gap-1.5">
                                        @foreach($groupPerms as $permData)
                                            @php 
                                                $permName = $permData['name'];
                                                $hasPerm = in_array($permName, $rolePerms); 
                                            @endphp
                                            @php
                                                $badgeLabel = $permData['type'];
                                                $badgeClass = $hasPerm
                                                    ? ($badgeLabel == 'FITUR' ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-400' : 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400')
                                                    : 'bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500';
                                            @endphp
                                            @if($hasPerm)
                                                <div class="group/item flex min-w-0 items-start gap-2 rounded-lg px-1.5 py-1 text-xs transition-colors hover:bg-emerald-50/70 dark:hover:bg-emerald-900/10">
                                                    <div class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-900/30">
                                                        <x-icon name="check" class="h-3 w-3 text-emerald-600 dark:text-emerald-400" />
                                                    </div>
                                                    <span class="min-w-0 flex flex-wrap items-center gap-1.5 font-medium leading-5 text-gray-900 dark:text-gray-100">
                                                        <span class="break-words">{{ $permData['label'] }}</span>
                                                        <span class="shrink-0 rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wide {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                                    </span>
                                                </div>
                                            @else
                                                <div class="group/item flex min-w-0 items-start gap-2 rounded-lg px-1.5 py-1 text-xs transition-colors opacity-60">
                                                    <div class="mt-0.5 flex h-4 w-4 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-gray-800">
                                                        <x-icon name="x-mark" class="h-3 w-3 text-gray-400 dark:text-gray-500" />
                                                    </div>
                                                    <span class="min-w-0 flex items-center gap-1.5 text-gray-400 dark:text-gray-500 line-through decoration-gray-300 dark:decoration-gray-600">
                                                        <span class="break-words truncate">{{ $permData['label'] }}</span>
                                                        <span class="shrink-0 rounded px-1.5 py-0.5 text-[9px] font-bold tracking-wide {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                                    </span>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-200/80 bg-white/60 px-4 py-3 dark:border-gray-800/80 dark:bg-gray-900/60 sm:px-5">
                    <x-ui.button href="{{ route('admin.roles.edit', $role) }}" variant="secondary" icon="pencil" class="shadow-sm">
                        {{ __('Ubah Konfigurasi Akses') }}
                    </x-ui.button>
                </div>
            </div>
        </section>
        @endforeach
    </div>
</x-app-layout>