<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-display text-2xl font-semibold tracking-tight text-gray-900 dark:text-white">Fitur & Modul Sistem</h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Aktifkan atau nonaktifkan fitur utama pada sistem. Perubahan akan langsung berdampak pada seluruh pengguna.</p>
            </div>
            <div class="rounded-xl border border-warning-200/80 bg-warning-50 px-4 py-2 text-xs leading-5 text-warning-800 shadow-sm dark:border-warning-800/60 dark:bg-warning-900/10 dark:text-warning-300">
                <div class="flex items-center gap-2 font-semibold"><x-icon name="exclamation-triangle" class="h-4 w-4" /> Perubahan bersifat global</div>
                <p class="mt-1 text-warning-700/80 dark:text-warning-400/80">Pastikan modul siap digunakan sebelum diaktifkan.</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-5">
        @if(session('success'))
            <div class="flex items-start gap-3 rounded-2xl border border-success-200/80 bg-success-50/90 px-4 py-3.5 text-sm text-success-700 shadow-sm dark:border-success-800/60 dark:bg-success-900/20 dark:text-success-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        
        @if(session('error'))
            <div class="flex items-start gap-3 rounded-2xl border border-danger-200/80 bg-danger-50/90 px-4 py-3.5 text-sm text-danger-700 shadow-sm dark:border-danger-800/60 dark:bg-danger-900/20 dark:text-danger-300">
                <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <section class="overflow-hidden rounded-[1.75rem] border border-gray-200/80 bg-white/95 shadow-soft-lg shadow-gray-900/[0.025] dark:border-gray-800/80 dark:bg-gray-900/95">
            <div class="flex flex-col gap-4 border-b border-gray-200/80 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6 dark:border-gray-800/80">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-primary-50 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400">
                        <x-icon name="adjustments-horizontal" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-bold uppercase tracking-[0.18em] text-gray-400">Feature registry</p>
                        <h2 class="mt-1 text-lg font-semibold tracking-tight text-gray-900 dark:text-white">System modules</h2>
                    </div>
                </div>
                <p class="max-w-sm text-xs leading-5 text-gray-400 sm:text-right">Setiap modul berdiri sendiri. Gunakan toggle untuk mengontrol ketersediaannya tanpa membuka halaman konfigurasi lain.</p>
            </div>

            <!-- Features Grid -->
            <div class="grid grid-cols-1 gap-3 p-4 sm:p-5 lg:grid-cols-2">
                @foreach($features as $feature)
                    <article class="group flex min-h-[150px] flex-col overflow-hidden rounded-2xl border border-gray-200/80 bg-gray-50/60 p-4 transition-all duration-300 hover:border-primary-200 hover:bg-white hover:shadow-soft dark:border-gray-800/80 dark:bg-gray-800/40 dark:hover:border-primary-800/70 dark:hover:bg-gray-900">
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-start gap-3">
                                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $feature->is_active ? 'bg-primary-100 text-primary-600 dark:bg-primary-900/50 dark:text-primary-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }} transition-colors duration-300">
                                    @if(str_contains($feature->key, '2fa'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                        </svg>
                                    @elseif(str_contains($feature->key, 'finance'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    @elseif(str_contains($feature->key, 'hris'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    @elseif(str_contains($feature->key, 'loan'))
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"></path>
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543-.94-3.31.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="truncate text-sm font-bold text-gray-900 dark:text-white">{{ $feature->name }}</h3>
                                    <span class="mt-2 inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-[0.12em] {{ $feature->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }} rounded-full px-2.5 py-1">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $feature->is_active ? 'bg-green-500' : 'bg-gray-500' }}"></span>
                                        {{ $feature->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </div>
                            </div>

                            <!-- Toggle Switch -->
                            <form action="{{ route('admin.features.toggle', $feature->key) }}" method="POST" class="shrink-0">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="relative inline-flex h-7 w-12 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-primary-600/20 focus:ring-offset-2 {{ $feature->is_active ? 'bg-primary-600' : 'bg-gray-200 dark:bg-gray-600' }}"
                                    role="switch"
                                    aria-checked="{{ $feature->is_active ? 'true' : 'false' }}">
                                    <span class="sr-only">Toggle feature</span>
                                    <span aria-hidden="true"
                                        class="pointer-events-none inline-block h-6 w-6 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $feature->is_active ? 'translate-x-5' : 'translate-x-0' }}">
                                    </span>
                                </button>
                            </form>
                        </div>
                        <p class="mt-4 line-clamp-2 min-h-[2.5rem] text-xs leading-5 text-gray-500 dark:text-gray-400">{{ $feature->description }}</p>
                        <div class="mt-auto flex items-center justify-between border-t border-gray-200/70 pt-3 text-[10px] font-bold uppercase tracking-[0.14em] text-gray-400 dark:border-gray-700/70 dark:text-gray-500">
                            <span>System feature</span>
                            <span class="inline-flex items-center gap-1.5"><span class="h-1.5 w-1.5 rounded-full {{ $feature->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>{{ $feature->is_active ? 'Ready' : 'Paused' }}</span>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-app-layout>