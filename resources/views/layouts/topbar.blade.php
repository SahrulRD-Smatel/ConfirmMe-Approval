<!-- Top Navigation Bar -->
<header class="sticky top-0 z-30 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
    
    <!-- Mobile menu button -->
    <button @click="sidebarOpen = true" type="button" class="lg:hidden -m-2.5 p-2.5 text-gray-700 dark:text-gray-300">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
        </svg>
    </button>

    <!-- Separator -->
    <div class="h-6 w-px bg-gray-200 dark:bg-gray-700 lg:hidden"></div>

    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
        <!-- Search Bar -->
        <form class="relative flex flex-1" action="{{ route('search') }}" method="GET">
            <label for="search-field" class="sr-only">Search</label>
            <svg class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
            </svg>
            <input id="search-field" 
                   name="q" 
                   type="search" 
                   class="block h-full w-full border-0 py-0 pl-8 pr-0 text-gray-900 dark:text-white bg-transparent placeholder:text-gray-400 focus:ring-0 sm:text-sm" 
                   placeholder="Cari dokumen, nomor, atau judul...">
        </form>

        <div class="flex items-center gap-x-4 lg:gap-x-6">
            <!-- Notification Bell -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="relative -m-2.5 p-2.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    <span class="sr-only">View notifications</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                    </svg>
                    @php 
                        $unreadCount = $sidebarBadges['unreadNotifCount'] ?? 0;
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute top-0 right-0 flex h-5 w-5 items-center justify-center rounded-full bg-danger-500 text-xs font-bold text-white animate-pulse-slow">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </button>

                <!-- Notification Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="transform opacity-0 scale-95"
                     x-transition:enter-end="transform opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="transform opacity-100 scale-100"
                     x-transition:leave-end="transform opacity-0 scale-95"
                     class="absolute right-0 mt-2 w-80 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200 dark:ring-gray-700 focus:outline-none"
                     style="display: none;">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifikasi</h3>
                    </div>
                    <div class="max-h-96 overflow-y-auto">
                        @forelse(auth()->user()->notifications->take(5) as $notification)
                            <a href="{{ route('notifications.read', $notification->id) }}" 
                               class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 {{ $notification->read_at ? '' : 'bg-primary-50 dark:bg-primary-900/20' }}">
                                @php
                                    $notifType = $notification->data['type'] ?? 'default';
                                    $iconColors = [
                                        'submitted' => 'bg-blue-100 text-blue-600 dark:bg-blue-800 dark:text-blue-400',
                                        'approved' => 'bg-green-100 text-green-600 dark:bg-green-800 dark:text-green-400',
                                        'rejected' => 'bg-red-100 text-red-600 dark:bg-red-800 dark:text-red-400',
                                        'next_step' => 'bg-amber-100 text-amber-600 dark:bg-amber-800 dark:text-amber-400',
                                        'completed' => 'bg-emerald-100 text-emerald-600 dark:bg-emerald-800 dark:text-emerald-400',
                                    ];
                                    $iconColor = $iconColors[$notifType] ?? 'bg-primary-100 text-primary-600 dark:bg-primary-800 dark:text-primary-400';
                                @endphp
                                <div class="flex-shrink-0 w-8 h-8 rounded-full {{ $iconColor }} flex items-center justify-center">
                                    @if($notifType === 'approved' || $notifType === 'completed')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    @elseif($notifType === 'rejected')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    @elseif($notifType === 'next_step' || $notifType === 'submitted')
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                                        </svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 dark:text-gray-300">{{ $notification->data['title'] ?? 'Notifikasi' }}</p>
                                    <p class="text-sm text-gray-900 dark:text-white line-clamp-2">{{ $notification->data['message'] ?? 'Notifikasi baru' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                </div>
                                @unless($notification->read_at)
                                    <span class="flex-shrink-0 w-2 h-2 rounded-full bg-primary-500 mt-2"></span>
                                @endunless
                            </a>
                        @empty
                            <div class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada notifikasi
                            </div>
                        @endforelse
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                        <a href="{{ route('notifications.index') }}" class="text-sm font-medium text-primary-600 hover:text-primary-800 dark:text-primary-400">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>
            </div>

            <!-- Separator -->
            <div class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200 dark:lg:bg-gray-700"></div>

            <!-- User Dropdown -->
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" type="button" class="flex items-center gap-x-3 -m-1.5 p-1.5">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="h-8 w-8 rounded-full bg-gray-50 object-cover">
                    <span class="hidden lg:flex lg:items-center">
                        <span class="text-sm font-semibold leading-6 text-gray-900 dark:text-white">{{ auth()->user()->name }}</span>
                        <svg class="ml-2 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </button>

                <!-- User Menu Dropdown -->
                <div x-show="open" 
                     @click.away="open = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 origin-top-right rounded-lg bg-white dark:bg-gray-800 shadow-lg ring-1 ring-gray-200 dark:ring-gray-700"
                     style="display: none;">
                    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        @php
                            $currentRole = auth()->user()->getRoleNames()->first() ?? 'user';
                            $roleLabelsMap = [
                                'admin' => 'Administrator',
                                'presiden_direktur' => 'Presiden Direktur',
                                'direktur_hr' => 'Direktur HR',
                                'direktur_finance' => 'Direktur Finance',
                                'direktur_it' => 'Direktur IT',
                                'direktur_sales' => 'Direktur Sales',
                                'direktur_operasional' => 'Direktur Operasional',
                                'manager' => 'Manager',
                                'supervisor' => 'Supervisor',
                                'hrd' => 'HRD',
                                'hrd_coordinator' => 'HRD Coordinator',
                                'purchasing' => 'Purchasing',
                                'finance' => 'Finance',
                                'staff' => 'Staff',
                            ];
                            $roleColorsMap = [
                                'admin' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
                                'presiden_direktur' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
                                'direktur_hr' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                                'direktur_finance' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                'direktur_it' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
                                'direktur_sales' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                                'direktur_operasional' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
                                'manager' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
                                'supervisor' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-200',
                                'hrd' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
                                'hrd_coordinator' => 'bg-rose-100 text-rose-800 dark:bg-rose-900 dark:text-rose-200',
                                'purchasing' => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-200',
                                'finance' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200',
                                'staff' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200',
                            ];
                            $displayRole = $roleLabelsMap[$currentRole] ?? ucfirst(str_replace('_', ' ', $currentRole));
                            $roleColor = $roleColorsMap[$currentRole] ?? 'bg-primary-100 text-primary-800 dark:bg-primary-900 dark:text-primary-200';
                        @endphp
                        <span class="inline-flex items-center mt-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $roleColor }}">
                            {{ $displayRole }}
                        </span>
                    </div>
                    <div class="py-1">
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Profil Saya
                        </a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-2 w-full px-4 py-2 text-sm text-danger-600 dark:text-danger-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                                Keluar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>
