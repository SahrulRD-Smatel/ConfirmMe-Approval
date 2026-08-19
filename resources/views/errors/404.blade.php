<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>404 - Halaman Tidak Ditemukan | ConfirmMe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Robot Walking Animation */
        .robot { animation: robot-walk 4s ease-in-out infinite; }
        .robot .head { animation: head-look 5s ease-in-out infinite; transform-origin: center bottom; }
        .robot .eye-left, .robot .eye-right { animation: robot-blink 3.5s ease-in-out infinite; }
        .robot .eye-right { animation-delay: 0.15s; }
        .robot .antenna { animation: antenna-bounce 2s ease-in-out infinite; transform-origin: center bottom; }
        .robot .leg-left { animation: leg-walk-left 1s ease-in-out infinite; transform-origin: top center; }
        .robot .leg-right { animation: leg-walk-right 1s ease-in-out infinite; transform-origin: top center; }
        .robot .arm-left { animation: arm-swing-left 1s ease-in-out infinite; transform-origin: top center; }
        .robot .arm-right { animation: arm-swing-right 1s ease-in-out infinite; transform-origin: top center; }
        .robot .question-mark { animation: question-float 2s ease-in-out infinite; }

        @keyframes robot-walk {
            0%, 100% { transform: translateX(-20px); }
            50% { transform: translateX(20px); }
        }
        @keyframes head-look {
            0%, 30%, 100% { transform: rotate(0deg); }
            10% { transform: rotate(-12deg); }
            20% { transform: rotate(12deg); }
            40%, 60% { transform: rotate(0deg); }
            70% { transform: rotate(8deg); }
            80% { transform: rotate(-8deg); }
        }
        @keyframes robot-blink {
            0%, 42%, 48%, 100% { transform: scaleY(1); }
            45% { transform: scaleY(0.05); }
        }
        @keyframes antenna-bounce {
            0%, 100% { transform: rotate(-5deg); }
            50% { transform: rotate(5deg); }
        }
        @keyframes leg-walk-left {
            0%, 100% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
        }
        @keyframes leg-walk-right {
            0%, 100% { transform: rotate(10deg); }
            50% { transform: rotate(-10deg); }
        }
        @keyframes arm-swing-left {
            0%, 100% { transform: rotate(10deg); }
            50% { transform: rotate(-10deg); }
        }
        @keyframes arm-swing-right {
            0%, 100% { transform: rotate(-10deg); }
            50% { transform: rotate(10deg); }
        }
        @keyframes question-float {
            0%, 100% { transform: translateY(0) scale(1); opacity: 0.8; }
            50% { transform: translateY(-10px) scale(1.1); opacity: 1; }
        }

        /* Ground dots */
        .ground-dot { animation: dot-appear 2s ease-in-out infinite; }
        .ground-dot:nth-child(2) { animation-delay: 0.3s; }
        .ground-dot:nth-child(3) { animation-delay: 0.6s; }
        .ground-dot:nth-child(4) { animation-delay: 0.9s; }

        @keyframes dot-appear {
            0%, 100% { opacity: 0.2; transform: scale(0.8); }
            50% { opacity: 0.7; transform: scale(1); }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 antialiased min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden">

        {{-- Decorative --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-blue-200/30 dark:bg-blue-900/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-indigo-200/30 dark:bg-indigo-900/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-lg w-full text-center relative z-10">
            {{-- Animated Robot SVG --}}
            <div class="robot mx-auto mb-8 w-56 h-56 sm:w-64 sm:h-64">
                <svg viewBox="0 0 200 200" class="w-full h-full">
                    {{-- Shadow --}}
                    <ellipse cx="100" cy="188" rx="40" ry="6" fill="rgba(0,0,0,0.06)">
                        <animate attributeName="rx" values="40;35;40" dur="4s" repeatCount="indefinite"/>
                    </ellipse>

                    {{-- Question marks --}}
                    <g class="question-mark">
                        <text x="145" y="45" font-size="22" font-weight="bold" fill="#60a5fa" opacity="0.8">?</text>
                    </g>
                    <g class="question-mark" style="animation-delay: 0.7s;">
                        <text x="52" y="55" font-size="16" font-weight="bold" fill="#818cf8" opacity="0.6">?</text>
                    </g>
                    <g class="question-mark" style="animation-delay: 1.4s;">
                        <text x="160" y="70" font-size="14" font-weight="bold" fill="#93c5fd" opacity="0.5">?</text>
                    </g>

                    {{-- Left leg --}}
                    <g class="leg-left">
                        <rect x="80" y="152" width="12" height="24" rx="4" fill="#6366f1"/>
                        <rect x="76" y="172" width="18" height="8" rx="3" fill="#4f46e5"/>
                    </g>

                    {{-- Right leg --}}
                    <g class="leg-right">
                        <rect x="108" y="152" width="12" height="24" rx="4" fill="#6366f1"/>
                        <rect x="106" y="172" width="18" height="8" rx="3" fill="#4f46e5"/>
                    </g>

                    {{-- Left arm --}}
                    <g class="arm-left">
                        <rect x="55" y="108" width="10" height="30" rx="5" fill="#818cf8"/>
                        <circle cx="60" cy="140" r="6" fill="#6366f1"/>
                    </g>

                    {{-- Right arm --}}
                    <g class="arm-right">
                        <rect x="135" y="108" width="10" height="30" rx="5" fill="#818cf8"/>
                        <circle cx="140" cy="140" r="6" fill="#6366f1"/>
                    </g>

                    {{-- Body --}}
                    <rect x="68" y="100" width="64" height="55" rx="12" fill="#818cf8"/>
                    <rect x="73" y="105" width="54" height="45" rx="8" fill="#a5b4fc"/>

                    {{-- Body screen --}}
                    <rect x="82" y="112" width="36" height="24" rx="4" fill="#312e81"/>
                    <text x="100" y="128" text-anchor="middle" font-size="10" font-family="monospace" fill="#67e8f9">404</text>

                    {{-- Body buttons --}}
                    <circle cx="88" cy="145" r="3" fill="#f87171"/>
                    <circle cx="100" cy="145" r="3" fill="#fbbf24"/>
                    <circle cx="112" cy="145" r="3" fill="#34d399"/>

                    {{-- Head --}}
                    <g class="head">
                        {{-- Antenna --}}
                        <g class="antenna">
                            <line x1="100" y1="55" x2="100" y2="42" stroke="#6366f1" stroke-width="3"/>
                            <circle cx="100" cy="38" r="5" fill="#f87171">
                                <animate attributeName="opacity" values="1;0.4;1" dur="1.5s" repeatCount="indefinite"/>
                            </circle>
                        </g>

                        {{-- Head shape --}}
                        <rect x="72" y="55" width="56" height="48" rx="14" fill="#818cf8"/>
                        <rect x="76" y="59" width="48" height="40" rx="10" fill="#a5b4fc"/>

                        {{-- Eyes --}}
                        <g class="eye-left">
                            <circle cx="90" cy="76" r="9" fill="white"/>
                            <circle cx="92" cy="77" r="5" fill="#1e1e1e"/>
                            <circle cx="94" cy="75" r="2" fill="white"/>
                        </g>
                        <g class="eye-right">
                            <circle cx="110" cy="76" r="9" fill="white"/>
                            <circle cx="108" cy="77" r="5" fill="#1e1e1e"/>
                            <circle cx="110" cy="75" r="2" fill="white"/>
                        </g>

                        {{-- Confused mouth --}}
                        <path d="M90 92 Q95 88 100 92 Q105 96 110 90" fill="none" stroke="#4338ca" stroke-width="2" stroke-linecap="round"/>

                        {{-- Ears/bolts --}}
                        <circle cx="72" cy="78" r="4" fill="#6366f1" stroke="#4f46e5" stroke-width="1"/>
                        <circle cx="128" cy="78" r="4" fill="#6366f1" stroke="#4f46e5" stroke-width="1"/>
                    </g>

                    {{-- Ground dots (footprints) --}}
                    <circle cx="40" cy="185" r="2" fill="#c7d2fe" class="ground-dot"/>
                    <circle cx="55" cy="187" r="1.5" fill="#c7d2fe" class="ground-dot"/>
                    <circle cx="145" cy="187" r="1.5" fill="#c7d2fe" class="ground-dot"/>
                    <circle cx="160" cy="185" r="2" fill="#c7d2fe" class="ground-dot"/>
                </svg>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl sm:text-8xl font-black bg-gradient-to-r from-blue-600 to-indigo-600 bg-clip-text text-transparent mb-3">
                404
            </h1>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Halaman Tidak Ditemukan</h2>

            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                Robot kami sudah mencari ke mana-mana, tapi halaman yang Kamu cari tidak bisa ditemukan. Mungkin sudah dipindahkan atau dihapus.
            </p>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-all shadow-sm border border-gray-200 dark:border-gray-600 hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Kembali
                </a>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl font-semibold hover:from-blue-700 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 hover:scale-105 active:scale-95">
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