<x-app-layout>
    <x-slot name="title">Kelola Role & Permission</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Role & Permission</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Atur hak akses secara dinamis untuk mengontrol visibilitas menu dan fitur setiap divisi.</p>
            </div>
            <x-ui.button href="{{ route('admin.roles.create') }}" icon="plus" class="shadow-lg shadow-primary-500/30">Tambah Role</x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-6">
        @foreach($roles as $role)
        @php
            $rolePerms = $role->permissions->pluck('name')->toArray();
            $isProtected = in_array($role->name, ['admin','staff','supervisor','hrd','direktur','finance','purchasing','direktur_operasional','hrd_coordinator','manager','direktur_hr','direktur_finance','direktur_it','direktur_sales','presiden_direktur']);
            
            // Generate icon based on role name
            $icon = 'users';
            $bgColor = 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300';
            if($role->name === 'admin') { $icon = 'shield-check'; $bgColor = 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'; }
            elseif(str_starts_with($role->name, 'direktur') || $role->name === 'presiden_direktur') { $icon = 'building-office'; $bgColor = 'bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400'; }
            elseif($role->name === 'hrd' || $role->name === 'hrd_coordinator') { $icon = 'user-group'; $bgColor = 'bg-pink-100 text-pink-600 dark:bg-pink-900/30 dark:text-pink-400'; }
            elseif($role->name === 'finance' || $role->name === 'purchasing') { $icon = 'banknotes'; $bgColor = 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'; }
            elseif($role->name === 'manager' || $role->name === 'supervisor') { $icon = 'star'; $bgColor = 'bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400'; }
        @endphp

        <div x-data="{ open: false }" 
             class="group bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl border border-gray-200/50 dark:border-gray-700/50 overflow-hidden shadow-sm hover:shadow-md transition-all duration-300">
            
            {{-- Header (Clickable) --}}
            <button @click="open = !open" 
                    class="w-full flex items-center justify-between p-5 transition-colors focus:outline-none">
                <div class="flex items-center gap-5">
                    <div class="w-14 h-14 rounded-xl flex items-center justify-center {{ $bgColor }} transition-transform duration-300 group-hover:scale-110">
                        <x-icon name="{{ $icon }}" class="w-7 h-7" />
                    </div>
                    <div class="text-left">
                        <div class="flex items-center gap-3">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ role_label($role->name) }}</h3>
                            @if($isProtected)
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400">
                                    Sistem
                                </span>
                            @endif
                        </div>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-3">
                            <span class="flex items-center gap-1">
                                <x-icon name="check-circle" class="w-4 h-4 text-emerald-500" />
                                <b>{{ count($rolePerms) }}</b> Akses Terbuka
                            </span>
                            <span class="text-gray-300 dark:text-gray-600">•</span>
                            <span class="flex items-center gap-1">
                                <x-icon name="user" class="w-4 h-4" />
                                <b>{{ $role->users()->count() }}</b> Pengguna
                            </span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 pr-2">
                    <div class="flex items-center space-x-1 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                        <a href="{{ route('admin.roles.edit', $role) }}" @click.stop 
                           class="p-2.5 text-primary-600 bg-primary-50 hover:bg-primary-100 dark:bg-primary-900/20 dark:hover:bg-primary-900/40 rounded-xl transition-colors tooltip" data-tip="Edit Hak Akses">
                            <x-icon name="pencil" class="w-5 h-5" />
                        </a>
                        @if(!$isProtected)
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" @click.stop onsubmit="confirmAction(event, 'Hapus role {{ $role->name }} beserta hak aksesnya?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="p-2.5 text-danger-600 bg-danger-50 hover:bg-danger-100 dark:bg-danger-900/20 dark:hover:bg-danger-900/40 rounded-xl transition-colors tooltip" data-tip="Hapus Role">
                                <x-icon name="trash" class="w-5 h-5" />
                            </button>
                        </form>
                        @endif
                    </div>
                    <div class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-50 dark:bg-gray-700/50 text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors">
                        <x-icon name="chevron-down" class="w-5 h-5 transition-transform duration-300" x-bind:class="open ? 'rotate-180' : ''" />
                    </div>
                </div>
            </button>

            {{-- Body (Permissions Matrix) --}}
            <div x-show="open" 
                 x-collapse
                 class="border-t border-gray-100 dark:border-gray-700/50 bg-gray-50/50 dark:bg-gray-800/30">
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($permGroups as $groupName => $groupPerms)
                            @php 
                                $activeInGroup = array_intersect($groupPerms, $rolePerms);
                                $hasAny = count($activeInGroup) > 0;
                                $isAll = count($activeInGroup) === count($groupPerms);
                            @endphp
                            
                            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 shadow-sm relative overflow-hidden transition-colors {{ $hasAny ? 'ring-1 ring-primary-500/20' : 'opacity-75' }}">
                                @if($isAll)
                                    <div class="absolute top-0 right-0 w-16 h-16 overflow-hidden">
                                        <div class="absolute transform rotate-45 bg-emerald-500 text-white text-[9px] font-bold py-1 right-[-35px] top-[15px] w-[120px] text-center shadow-sm">
                                            FULL AKSES
                                        </div>
                                    </div>
                                @endif
                                
                                <div class="flex items-center gap-2 mb-4">
                                    <h4 class="font-bold text-gray-800 dark:text-gray-200 uppercase text-xs tracking-wider">{{ $groupName }}</h4>
                                    @if($hasAny && !$isAll)
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-400">
                                            {{ count($activeInGroup) }}/{{ count($groupPerms) }}
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="space-y-2.5">
                                    @foreach($groupPerms as $perm)
                                        @php $hasPerm = in_array($perm, $rolePerms); @endphp
                                        <div class="flex items-start gap-3 text-sm group/item">
                                            @if($hasPerm)
                                                <div class="mt-0.5 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center flex-shrink-0">
                                                    <x-icon name="check" class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                                </div>
                                                <span class="text-gray-900 dark:text-gray-100 font-medium">{{ str_replace('-', ' ', $perm) }}</span>
                                            @else
                                                <div class="mt-0.5 w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center flex-shrink-0">
                                                    <x-icon name="x-mark" class="w-3.5 h-3.5 text-gray-400" />
                                                </div>
                                                <span class="text-gray-400 dark:text-gray-500">{{ str_replace('-', ' ', $perm) }}</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                
                <div class="px-6 py-4 bg-gray-100/50 dark:bg-gray-800/80 border-t border-gray-100 dark:border-gray-700/50 flex justify-end">
                    <x-ui.button href="{{ route('admin.roles.edit', $role) }}" variant="secondary" icon="pencil" class="shadow-sm">
                        Ubah Konfigurasi Akses
                    </x-ui.button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</x-app-layout>