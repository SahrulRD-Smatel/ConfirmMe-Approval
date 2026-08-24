<x-app-layout>
    <x-slot name="title">{{ __('Kelola Pengguna') }}</x-slot>

    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Kelola Pengguna') }}</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    {{ __('Kelola identitas, role, departemen, dan akses akun dari satu ruang kerja yang terstruktur.') }}
                </p>
            </div>
            <x-ui.button href="{{ route('admin.users.create') }}" icon="plus">
                {{ __('Tambah Pengguna') }}
            </x-ui.button>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filters / command bar -->
        <section class="rounded-[1.75rem] border border-gray-200/80 bg-white/90 p-4 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/90 sm:p-5">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-[0.18em] text-gray-400">{{ __('Directory controls') }}</p>
                    <h2 class="mt-1 text-base font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('Cari dan filter akun') }}</h2>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500">{{ __('Gunakan filter untuk mempersempit daftar pengguna.') }}</p>
            </div>
            <form action="{{ route('admin.users.index') }}" method="GET" class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1fr)_13rem_auto]">
                <label class="relative block">
                    <span class="sr-only">{{ __('Cari nama atau email') }}</span>
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                        <x-icon name="magnifying-glass" class="h-5 w-5" />
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 pl-11 pr-4 text-sm text-gray-900 outline-none transition placeholder:text-gray-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white"
                        placeholder="{{ __('Cari nama atau email...') }}">
                </label>
                <label class="block">
                    <span class="sr-only">{{ __('Filter role') }}</span>
                    <select name="role"
                        class="block h-12 w-full rounded-xl border border-gray-200 bg-gray-50/80 px-4 text-sm text-gray-900 outline-none transition focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:bg-gray-800/80 dark:text-white">
                        <option value="">{{ __('Semua Role') }}</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                {{ role_label($role->name) }}</option>
                        @endforeach
                    </select>
                </label>
                <x-ui.button type="submit" variant="secondary" class="h-12 px-6">{{ __('Filter') }}</x-ui.button>
            </form>
        </section>

        <!-- User directory -->
        <section class="overflow-hidden rounded-[1.75rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/95">
            <div class="flex flex-col gap-4 border-b border-gray-200/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-gray-800/80">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                        <x-icon name="users" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-[0.16em] text-gray-400">{{ __('People') }}</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ __('User directory') }}</h2>
                    </div>
                </div>
                <p class="max-w-xs text-left text-xs leading-5 text-gray-400 sm:text-right">{{ __('Review account identity and access status before making changes.') }}</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[860px] w-full border-separate border-spacing-0">
                    <thead>
                        <tr class="bg-gray-50/80 dark:bg-gray-800/70">
                            <th class="border-b border-gray-200/80 px-6 py-4 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:border-gray-800/80 dark:text-gray-400">{{ __('Pengguna') }}</th>
                            <th class="border-b border-gray-200/80 px-6 py-4 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:border-gray-800/80 dark:text-gray-400">{{ __('Role') }}</th>
                            <th class="border-b border-gray-200/80 px-6 py-4 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:border-gray-800/80 dark:text-gray-400">{{ __('Departemen') }}</th>
                            <th class="border-b border-gray-200/80 px-6 py-4 text-left text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:border-gray-800/80 dark:text-gray-400">{{ __('Status') }}</th>
                            <th class="border-b border-gray-200/80 px-6 py-4 text-right text-[10px] font-bold uppercase tracking-[0.16em] text-gray-500 dark:border-gray-800/80 dark:text-gray-400">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr class="group transition-colors hover:bg-primary-50/40 dark:hover:bg-primary-900/10">
                                <td class="border-b border-gray-100 px-6 py-4 dark:border-gray-800/70">
                                    <div class="flex items-center gap-3">
                                        <div class="relative shrink-0">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}"
                                                class="h-11 w-11 rounded-2xl object-cover ring-1 ring-gray-200 dark:ring-gray-700">
                                            <span class="absolute -bottom-1 -right-1 h-3 w-3 rounded-full border-2 border-white bg-primary-500 dark:border-gray-900"></span>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-sm font-semibold text-gray-900 transition-colors group-hover:text-primary-700 dark:text-white dark:group-hover:text-primary-400">{{ $user->name }}</div>
                                            <div class="mt-1 truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="border-b border-gray-100 px-6 py-4 dark:border-gray-800/70">
                                    <div class="flex flex-wrap gap-1.5">
                                        @foreach($user->roles as $role)
                                            <x-ui.badge status="info">{{ role_label($role->name) }}</x-ui.badge>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="border-b border-gray-100 px-6 py-4 dark:border-gray-800/70">
                                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ $user->department ?? '-' }}</span>
                                </td>
                                <td class="border-b border-gray-100 px-6 py-4 dark:border-gray-800/70">
                                    <x-ui.badge :status="$user->is_active ? 'success' : 'danger'">
                                        {{ $user->is_active ? __('Aktif') : __('Nonaktif') }}
                                    </x-ui.badge>
                                </td>
                                <td class="border-b border-gray-100 px-6 py-4 dark:border-gray-800/70">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}"
                                            class="inline-flex h-9 items-center gap-2 rounded-xl border border-gray-200 px-3 text-xs font-semibold text-primary-700 transition hover:border-primary-200 hover:bg-primary-50 focus:outline-none focus:ring-4 focus:ring-primary-500/10 dark:border-gray-700 dark:text-primary-400 dark:hover:bg-primary-900/20">
                                            <x-icon name="pencil" class="h-4 w-4" />
                                            <span class="hidden lg:inline">{{ __('Edit') }}</span>
                                        </a>
                                        <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                class="inline-flex h-9 items-center gap-2 rounded-xl border border-transparent px-3 text-xs font-semibold transition focus:outline-none focus:ring-4 focus:ring-primary-500/10 {{ $user->is_active ? 'text-yellow-600' : 'text-green-600' }} {{ $user->is_active ? 'text-yellow-700 hover:border-yellow-200 hover:bg-yellow-50 dark:text-yellow-400 dark:hover:bg-yellow-900/20' : 'text-green-700 hover:border-green-200 hover:bg-green-50 dark:text-green-400 dark:hover:bg-green-900/20' }}">
                                                <x-icon name="{{ $user->is_active ? 'pause' : 'play' }}" class="h-4 w-4" />
                                                <span class="hidden lg:inline">{{ $user->is_active ? __('Nonaktifkan') : __('Aktifkan') }}</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-gray-100 text-gray-400 dark:bg-gray-800 dark:text-gray-500">
                                        <x-icon name="users" class="h-8 w-8" />
                                    </div>
                                    <p class="mt-5 text-sm font-semibold text-gray-700 dark:text-gray-200">{{ __('Tidak ada pengguna') }}</p>
                                    <p class="mx-auto mt-1 max-w-xs text-xs leading-5 text-gray-400">{{ __('Coba ubah kata kunci atau filter role untuk melihat hasil lainnya.') }}</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="border-t border-gray-200/80 px-5 py-4 dark:border-gray-800/80 sm:px-6">
                    {{ $users->links() }}
                </div>
            @endif
        </section>
    </div>
</x-app-layout>
