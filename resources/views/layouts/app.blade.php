<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Dashboard' }} - {{ config('app.name', 'ConfirmMe') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700|plus-jakarta-sans:500,600,700,800&display=swap"
        rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Page loading indicator -->
    <style>
        .page-loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 0;
            height: 3px;
            background: linear-gradient(90deg, #dc2626, #f97316, #dc2626);
            z-index: 9999;
            transition: width 0.3s ease;
            box-shadow: 0 0 8px rgba(220, 38, 38, 0.5);
        }
        .page-loader.loading {
            width: 85%;
            transition: width 8s cubic-bezier(0.1, 0.5, 0.3, 1);
        }
        .page-loader.done {
            width: 100%;
            opacity: 0;
            transition: width 0.2s ease, opacity 0.3s ease 0.2s;
        }
    </style>

    <!-- Currency input formatter -->
    <script src="{{ asset('js/currency-input.js') }}" defer></script>

    <!-- Global form loading spinner (prevents double-submit) -->
    <script src="{{ asset('js/form-loading.js') }}" defer></script>
</head>

<body class="h-full font-sans antialiased bg-gray-50 text-gray-800 dark:bg-gray-950 dark:text-gray-100"
    x-data="{ sidebarOpen: false, sidebarCollapsed: false }">

    {{-- Animated Floating Bubbles Background --}}
    <div class="bubbles-container" aria-hidden="true">
        <div class="bubble bubble--maroon"
            style="width: 80px; height: 80px; left: 5%; animation-duration: 22s; animation-delay: 0s;"></div>
        <div class="bubble bubble--rose"
            style="width: 120px; height: 120px; left: 15%; animation-duration: 28s; animation-delay: 2s;"></div>
        <div class="bubble bubble--gray"
            style="width: 60px; height: 60px; left: 25%; animation-duration: 18s; animation-delay: 4s;"></div>
        <div class="bubble bubble--maroon"
            style="width: 150px; height: 150px; left: 35%; animation-duration: 32s; animation-delay: 1s;"></div>
        <div class="bubble bubble--rose"
            style="width: 40px; height: 40px; left: 45%; animation-duration: 20s; animation-delay: 6s;"></div>
        <div class="bubble bubble--gray"
            style="width: 100px; height: 100px; left: 55%; animation-duration: 25s; animation-delay: 3s;"></div>
        <div class="bubble bubble--maroon"
            style="width: 70px; height: 70px; left: 65%; animation-duration: 19s; animation-delay: 5s;"></div>
        <div class="bubble bubble--rose"
            style="width: 200px; height: 200px; left: 75%; animation-duration: 35s; animation-delay: 0.5s;"></div>
        <div class="bubble bubble--gray"
            style="width: 50px; height: 50px; left: 85%; animation-duration: 16s; animation-delay: 7s;"></div>
        <div class="bubble bubble--maroon"
            style="width: 90px; height: 90px; left: 10%; animation-duration: 24s; animation-delay: 8s;"></div>
        <div class="bubble bubble--rose"
            style="width: 65px; height: 65px; left: 50%; animation-duration: 21s; animation-delay: 10s;"></div>
        <div class="bubble bubble--gray"
            style="width: 110px; height: 110px; left: 90%; animation-duration: 30s; animation-delay: 3.5s;"></div>
    </div>

    <div class="min-h-screen flex relative z-10 bg-gray-50/80 dark:bg-gray-950/80">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 z-40 bg-gray-900/80 lg:hidden"
            @click="sidebarOpen = false">
        </div>

        <!-- Sidebar -->
        @include('layouts.sidebar')

        <!-- Main Content Area -->
        <div class="flex-1 flex min-w-0 flex-col bg-transparent transition-all duration-300" :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
            <!-- Top Navigation -->
            @include('layouts.topbar')

            <!-- Page Header -->
            @isset($header)
                <header class="border-b border-gray-200/80 bg-white/90 backdrop-blur-xl dark:border-gray-800/80 dark:bg-gray-900/90">
                    <div class="mx-auto max-w-[1600px] px-4 py-4 sm:px-6 sm:py-5 xl:px-10">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto bg-transparent">
                <div class="py-6 sm:py-8 lg:py-10">
                    <div class="mx-auto w-full max-w-[1600px] px-4 sm:px-6 xl:px-10">
                        <!-- Flash Messages -->
                        @if (session('success'))
                            <div class="mb-5 rounded-2xl border border-success-200/80 bg-success-50/90 p-4 shadow-sm shadow-success-900/5 animate-fade-in dark:border-success-800/60 dark:bg-success-900/20">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-success-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm font-medium text-success-800 dark:text-success-200">
                                        {{ session('success') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-5 rounded-2xl border border-danger-200/80 bg-danger-50/90 p-4 shadow-sm shadow-danger-900/5 animate-fade-in dark:border-danger-800/60 dark:bg-danger-900/20">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z"
                                            clip-rule="evenodd" />
                                    </svg>
                                    <p class="ml-3 text-sm font-medium text-danger-800 dark:text-danger-200">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{ $slot }}
                    </div>
                </div>
            </main>

            <!-- Footer -->
            <footer class="border-t border-gray-200/80 bg-white/60 py-5 backdrop-blur-sm dark:border-gray-800/80 dark:bg-gray-900/60">
                <div class="mx-auto w-full max-w-[1600px] px-4 sm:px-6 xl:px-10">
                    <p class="text-center text-sm text-gray-500 dark:text-gray-400">
                        &copy; {{ date('Y') }} PT. Info Solusindo Data Utama. ConfirmMe v1.0
                    </p>
                </div>
            </footer>
        </div>
    </div>

    @stack('scripts')

    <!-- Global navigation loading indicator -->
    <script>
        (function() {
            // Create loader element
            const loader = document.createElement('div');
            loader.className = 'page-loader';
            loader.id = 'page-loader';
            document.body.prepend(loader);

            // Intercept all navigation clicks
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a[href]');
                if (!link) return;

                const href = link.getAttribute('href');
                // Skip: external links, anchors, javascript:, # links, new tabs
                if (!href || href.startsWith('#') || href.startsWith('javascript:') ||
                    link.target === '_blank' || link.hasAttribute('download') ||
                    href.startsWith('http') && !href.startsWith(window.location.origin)) {
                    return;
                }

                // Show loading bar
                loader.classList.remove('done');
                loader.style.width = '0';
                // Force reflow
                loader.offsetHeight;
                loader.classList.add('loading');
            });

            // Handle form submissions
            document.addEventListener('submit', function(e) {
                loader.classList.remove('done');
                loader.style.width = '0';
                loader.offsetHeight;
                loader.classList.add('loading');
            });

            // Complete on page fully loaded
            window.addEventListener('load', function() {
                loader.classList.remove('loading');
                loader.classList.add('done');
            });
        })();
    </script>
</body>

</html>