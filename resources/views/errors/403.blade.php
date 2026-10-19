<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - Akses Ditolak | ConfirmMe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Guard Monster Animation */
        .monster-guard { animation: guard-bounce 3s ease-in-out infinite; }
        .monster-guard .head { animation: head-shake 2.5s ease-in-out infinite; transform-origin: center bottom; }
        .monster-guard .eye-left, .monster-guard .eye-right { animation: blink 4s ease-in-out infinite; }
        .monster-guard .eye-right { animation-delay: 0.1s; }
        .monster-guard .hand-left { animation: wave-left 1.5s ease-in-out infinite; transform-origin: right center; }
        .monster-guard .hand-right { animation: wave-right 1.5s ease-in-out infinite 0.3s; transform-origin: left center; }
        .monster-guard .tail { animation: tail-wag 1s ease-in-out infinite; transform-origin: left center; }
        .monster-guard .shield { animation: shield-glow 2s ease-in-out infinite; }

        @keyframes guard-bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }
        @keyframes head-shake {
            0%, 40%, 100% { transform: rotate(0deg); }
            10% { transform: rotate(-8deg); }
            20% { transform: rotate(8deg); }
            30% { transform: rotate(-5deg); }
        }
        @keyframes blink {
            0%, 45%, 55%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.05); }
        }
        @keyframes wave-left {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(-15deg); }
        }
        @keyframes wave-right {
            0%, 100% { transform: rotate(0deg); }
            50% { transform: rotate(15deg); }
        }
        @keyframes tail-wag {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(15deg); }
            75% { transform: rotate(-15deg); }
        }
        @keyframes shield-glow {
            0%, 100% { filter: drop-shadow(0 0 4px rgba(220, 38, 38, 0.3)); }
            50% { filter: drop-shadow(0 0 12px rgba(220, 38, 38, 0.6)); }
        }
        .floating-x { animation: float-x 3s ease-in-out infinite; }
        .floating-x:nth-child(2) { animation-delay: 0.5s; }
        .floating-x:nth-child(3) { animation-delay: 1s; }
        @keyframes float-x {
            0%, 100% { transform: translateY(0) rotate(0deg); opacity: 0.6; }
            50% { transform: translateY(-15px) rotate(10deg); opacity: 1; }
        }
    </style>
</head>

<body class="min-h-screen bg-gray-50 antialiased dark:bg-gray-950">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

        {{-- Decorative background --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute top-20 left-10 floating-x text-red-200 dark:text-red-900/30 text-6xl font-black select-none">&times;</div>
            <div class="absolute top-40 right-20 floating-x text-red-200 dark:text-red-900/30 text-4xl font-black select-none">&times;</div>
            <div class="absolute bottom-32 left-1/4 floating-x text-red-200 dark:text-red-900/30 text-5xl font-black select-none">&times;</div>
            <div class="absolute -top-20 -right-20 w-64 h-64 bg-red-200/30 dark:bg-red-900/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-orange-200/30 dark:bg-orange-900/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-xl text-center">
            {{-- Animated Guard Monster SVG --}}
            <div class="monster-guard mx-auto mb-8 w-56 h-56 sm:w-64 sm:h-64">
                <svg viewBox="0 0 200 200" class="w-full h-full">
                    {{-- Shadow --}}
                    <ellipse cx="100" cy="185" rx="50" ry="8" fill="rgba(0,0,0,0.08)">
                        <animate attributeName="rx" values="50;45;50" dur="3s" repeatCount="indefinite"/>
                    </ellipse>

                    {{-- Tail --}}
                    <g class="tail">
                        <path d="M55 130 Q30 120 25 100 Q22 90 30 88" fill="none" stroke="#dc2626" stroke-width="5" stroke-linecap="round"/>
                    </g>

                    {{-- Body --}}
                    <ellipse cx="100" cy="135" rx="45" ry="40" fill="#dc2626"/>
                    <ellipse cx="100" cy="140" rx="38" ry="30" fill="#ef4444"/>

                    {{-- Belly --}}
                    <ellipse cx="100" cy="145" rx="25" ry="18" fill="#fca5a5"/>

                    {{-- Left arm --}}
                    <g class="hand-left">
                        <path d="M60 125 Q45 120 40 130 Q37 137 42 138" fill="#dc2626" stroke="#b91c1c" stroke-width="1"/>
                    </g>

                    {{-- Right arm holding shield --}}
                    <g class="hand-right">
                        <path d="M140 125 Q155 120 158 130 Q160 137 155 138" fill="#dc2626" stroke="#b91c1c" stroke-width="1"/>
                        {{-- Shield --}}
                        <g class="shield">
                            <path d="M158 115 L172 120 L172 138 L165 148 L158 138 Z" fill="#fbbf24" stroke="#f59e0b" stroke-width="1.5"/>
                            <path d="M161 122 L169 125 L169 136 L165 142 L161 136 Z" fill="#fde68a"/>
                            <text x="165" y="133" text-anchor="middle" font-size="10" font-weight="bold" fill="#b45309">!</text>
                        </g>
                    </g>

                    {{-- Head --}}
                    <g class="head">
                        {{-- Head shape --}}
                        <circle cx="100" cy="90" r="35" fill="#dc2626"/>
                        <circle cx="100" cy="92" r="30" fill="#ef4444"/>

                        {{-- Horns --}}
                        <path d="M78 65 Q75 45 70 40" fill="none" stroke="#b91c1c" stroke-width="4" stroke-linecap="round"/>
                        <path d="M122 65 Q125 45 130 40" fill="none" stroke="#b91c1c" stroke-width="4" stroke-linecap="round"/>

                        {{-- Eyes --}}
                        <g class="eye-left">
                            <ellipse cx="87" cy="88" rx="9" ry="10" fill="white"/>
                            <circle cx="89" cy="89" r="5" fill="#1e1e1e"/>
                            <circle cx="91" cy="87" r="2" fill="white"/>
                        </g>
                        <g class="eye-right">
                            <ellipse cx="113" cy="88" rx="9" ry="10" fill="white"/>
                            <circle cx="111" cy="89" r="5" fill="#1e1e1e"/>
                            <circle cx="113" cy="87" r="2" fill="white"/>
                        </g>

                        {{-- Angry eyebrows --}}
                        <line x1="78" y1="76" x2="92" y2="80" stroke="#7f1d1d" stroke-width="3" stroke-linecap="round"/>
                        <line x1="108" y1="80" x2="122" y2="76" stroke="#7f1d1d" stroke-width="3" stroke-linecap="round"/>

                        {{-- Mouth --}}
                        <path d="M88 104 Q100 96 112 104" fill="none" stroke="#7f1d1d" stroke-width="2.5" stroke-linecap="round"/>

                        {{-- Teeth --}}
                        <rect x="95" y="99" width="4" height="5" rx="1" fill="white"/>
                        <rect x="101" y="99" width="4" height="5" rx="1" fill="white"/>
                    </g>

                    {{-- Feet --}}
                    <ellipse cx="82" cy="174" rx="14" ry="6" fill="#b91c1c"/>
                    <ellipse cx="118" cy="174" rx="14" ry="6" fill="#b91c1c"/>
                </svg>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl sm:text-8xl font-black text-red-600 mb-3">
                403
            </h1>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Akses Ditolak!</h2>

            {{-- Message --}}
            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                {{ $exception->getMessage() ?: 'Monster penjaga berkata: "Kamu tidak punya izin untuk lewat di sini!" Pastikan Anda memiliki role yang sesuai.' }}
            </p>

            {{-- Debug Info --}}
            @if(config('app.debug'))
                <div class="mb-8 p-4 bg-amber-50/80 dark:bg-amber-900/20 backdrop-blur border border-amber-200 dark:border-amber-800/50 rounded-xl text-left text-sm">
                    <h3 class="font-semibold text-amber-800 dark:text-amber-200 mb-2 flex items-center gap-2">
                        <span class="flex h-2 w-2 rounded-full bg-amber-500 animate-pulse"></span>
                        Debug Info
                    </h3>
                    @auth
                        <div class="space-y-1 text-amber-700 dark:text-amber-300 font-mono text-xs">
                            <p><span class="font-semibold">User:</span> {{ auth()->user()->name }} (ID: {{ auth()->user()->id }})</p>
                            <p><span class="font-semibold">Email:</span> {{ auth()->user()->email }}</p>
                            <p><span class="font-semibold">Roles:</span> {{ auth()->user()->getRoleNames()->implode(', ') ?: 'Tidak ada role' }}</p>
                            <p><span class="font-semibold">URL:</span> {{ request()->fullUrl() }}</p>
                        </div>
                    @else
                        <p class="text-amber-700 dark:text-amber-300">User belum login.</p>
                    @endauth
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                    class="inline-flex items-center rounded-xl border border-gray-200 bg-white px-6 py-3 font-semibold text-gray-700 shadow-sm transition-all hover:-translate-y-0.5 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center rounded-xl bg-primary-700 px-6 py-3 font-semibold text-white shadow-sm shadow-primary-900/20 transition-all hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    Dashboard
                </a>
            </div>
        </div>
    </div>
</body>

</html>