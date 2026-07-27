<x-app-layout>
    <x-slot name="title">Kelola Role & Permission</x-slot>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white font-display">Role & Permission</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Kelola role dan hak akses setiap role di sistem</p>
            </div>
            <x-ui.button href="{{ route('admin.roles.create') }}" icon="plus">Tambah Role</x-ui.button>
        </div>
    </x-slot>

    {{-- Role Cards --}}
    <div class="space-y-4">
        @foreach($roles as $role)
        @php
            $rolePerms = $role->permissions->pluck('name')->toArray();
            $isProtected = in_array($role->name, ['admin','staff','supervisor','hrd','direktur','finance','purchasing','direktur_operasional','hrd_coordinator','manager','direktur_hr','direktur_finance','direktur_it','direktur_sales','presiden_direktur']);
            // Group permissions by category
            $permGroups = [
                'Dokumen' => ['create-document','view-document','edit-document','delete-document','submit-document','print-document'],
                'Approval' => ['approve-document','reject-document'],
                'Keuangan' => ['manage-budget','create-po','create-invoice','print-po','print-invoice','manage-purchasing'],
                'Laporan' => ['view-reports','export-reports'],
                'HR & Absensi' => ['manage-attendance','view-attendance'],
                'Administrasi' => ['manage-users','manage-roles','manage-workflow','manage-templates','view-audit-log'],
            ];
        @endphp
        <div x-data="{ open: false }" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Role Header (Clickable) --}}
            <button @click="open = !open" class="w-full flex items-center justify-between p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors text-left">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-sm
                        {{ $role->name === 'admin' ? 'bg-red-600' :
                           ($role->name === 'presiden_direktur' ? 'bg-purple-600' :
                           (str_starts_with($role->name, 'direktur') ? 'bg-blue-600' :
                           ($role->name === 'finance' ? 'bg-emerald-600' :
                           ($role->name === 'hrd' ? 'bg-pink-600' :
                           ($role->name === 'manager' ? 'bg-amber-600' :
                           ($role->name === 'supervisor' ? 'bg-indigo-600' : 'bg-gray-500')))))) }}">
                        {{ strtoupper(substr(str_replace(['direktur_','presiden_'], '', $role->name), 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ role_label($role->name) }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $role->permissions->count() }} permission
                            · {{ $role->users_count ?? $role->users()->count() }} user
                            @if($isProtected)
                            · <span class="text-amber-600 dark:text-amber-400">Role default</span>
                            @endif
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.roles.edit', $role) }}" @click.stop
                        class="p-2 text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 rounded-lg transition" title="Edit">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    </a>
                    @if(!$isProtected)
                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" @click.stop onsubmit="confirmAction(event, 'Hapus role {{ $role->name }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="p-2 text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition" title="Hapus">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                    @endif
                    <svg :class="open ? 'rotate-180' : ''" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </button>

            {{-- Permission Detail (Expandable) --}}
            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                 class="border-t border-gray-200 dark:border-gray-700 p-4 bg-gray-50 dark:bg-gray-900/30">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($permGroups as $groupName => $groupPerms)
                    @php $hasAny = count(array_intersect($groupPerms, $rolePerms)) > 0; @endphp
                    <div class="p-3 rounded-lg {{ $hasAny ? 'bg-white dark:bg-gray-800' : 'bg-gray-100 dark:bg-gray-800/50 opacity-50' }}">
                        <h4 class="text-xs font-semibold uppercase text-gray-500 dark:text-gray-400 mb-2">{{ $groupName }}</h4>
                        <div class="space-y-1">
                            @foreach($groupPerms as $perm)
                            @php $hasPerm = in_array($perm, $rolePerms); @endphp
                            <div class="flex items-center gap-2 text-sm">
                                @if($hasPerm)
                                <svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span class="text-gray-700 dark:text-gray-300">{{ str_replace('-', ' ', $perm) }}</span>
                                @else
                                <svg class="w-4 h-4 text-gray-300 dark:text-gray-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span class="text-gray-400 dark:text-gray-600 line-through">{{ str_replace('-', ' ', $perm) }}</span>
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-end">
                    <a href="{{ route('admin.roles.edit', $role) }}" class="inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Permission
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $roles->links() }}
    </div>

    {{-- Info Box --}}
    <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
        <div class="flex gap-3">
            <svg class="w-5 h-5 text-blue-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-blue-800 dark:text-blue-300">
                <p class="font-medium mb-1">Cara Kerja Permission</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-400">
                    <li><strong>Role</strong> menentukan posisi user dalam organisasi (staff, supervisor, manager, dll)</li>
                    <li><strong>Permission</strong> menentukan apa yang boleh dilakukan role tersebut (buat dokumen, approve, cetak, dll)</li>
                    <li>Ubah permission di halaman <strong>Edit</strong> untuk mengontrol akses setiap role</li>
                    <li>Role bertanda <span class="text-amber-600 font-medium">default</span> tidak bisa dihapus karena dipakai oleh sistem</li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>