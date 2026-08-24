<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800 dark:text-gray-200" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Language Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-1.5">
                                <x-icon name="globe-alt" class="w-4 h-4" />
                                <span>{{ strtoupper(app()->getLocale()) }}</span>
                            </div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('lang.switch', 'id')">
                            <div class="flex items-center justify-between">
                                <span>🇮🇩 Indonesia</span>
                                @if(app()->getLocale() === 'id')
                                    <x-icon name="check" class="w-4 h-4 text-green-500" />
                                @endif
                            </div>
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('lang.switch', 'en')">
                            <div class="flex items-center justify-between">
                                <span>🇺🇸 English</span>
                                @if(app()->getLocale() === 'en')
                                    <x-icon name="check" class="w-4 h-4 text-green-500" />
                                @endif
                            </div>
                        </x-dropdown-link>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-2">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center gap-2">
                                <span>{{ Auth::user()->name }}</span>
                                @php
                                    $roles = Auth::user()->getRoleNames();

                                    // Define Role Weights (Higher = Higher Priority)
                                    $roleWeights = [
                                        'presiden_direktur' => 100,
                                        'direktur_hr' => 90,
                                        'direktur_finance' => 90,
                                        'direktur_it' => 90,
                                        'direktur_sales' => 90,
                                        'direktur_operasional' => 90,
                                        'manager' => 80,
                                        'supervisor' => 70,
                                        'hrd' => 60,
                                        'hrd_coordinator' => 55,
                                        'purchasing' => 52,
                                        'finance' => 50,
                                        'admin' => 40,
                                        'staff' => 10,
                                        'user' => 0,
                                    ];

                                    // Find highest weighted role
                                    $navRole = 'user';
                                    $maxWeight = -1;

                                    foreach ($roles as $role) {
                                        $weight = $roleWeights[$role] ?? 0;
                                        if ($weight > $maxWeight) {
                                            $maxWeight = $weight;
                                            $navRole = $role;
                                        }
                                    }

                                    $navRoleBadge = match ($navRole) {
                                        'admin' => 'bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-300',
                                        'presiden_direktur' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
                                        'direktur_hr', 'direktur_finance', 'direktur_it', 'direktur_sales', 'direktur_operasional' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
                                        'manager' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
                                        'supervisor' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900 dark:text-cyan-300',
                                        'hrd' => 'bg-pink-100 text-pink-700 dark:bg-pink-900 dark:text-pink-300',
                                        'hrd_coordinator' => 'bg-rose-100 text-rose-700 dark:bg-rose-900 dark:text-rose-300',
                                        'purchasing' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
                                        'finance' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
                                        default => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    };

                                    $navRoleLabel = match ($navRole) {
                                        'presiden_direktur' => 'Presdir',
                                        'direktur_hr' => 'Dir. HR',
                                        'direktur_finance' => 'Dir. Finance',
                                        'direktur_it' => 'Dir. IT',
                                        'direktur_sales' => 'Dir. Sales',
                                        'direktur_operasional' => 'Dir. Operasional',
                                        'manager' => 'Manager ' . (Auth::user()->department ?? ''),
                                        'supervisor' => 'SPV ' . (Auth::user()->department ?? ''),
                                        'hrd' => 'Staff HRD',
                                        'hrd_coordinator' => 'HRD Coordinator',
                                        'purchasing' => 'Purchasing',
                                        'finance' => 'Staff Finance',
                                        default => ucfirst($navRole),
                                    };
                                @endphp
                                <span
                                    class="hidden sm:inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $navRoleBadge }}">
                                    {{ $navRoleLabel }}
                                </span>
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500 hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900 focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                            stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>