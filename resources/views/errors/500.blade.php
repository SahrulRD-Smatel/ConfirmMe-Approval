<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - Kesalahan Server | ConfirmMe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Panicking Monster */
        .monster-panic { animation: panic-shake 0.5s ease-in-out infinite; }
        .monster-panic .body { animation: body-tremble 0.3s ease-in-out infinite; }
        .monster-panic .eye-left, .monster-panic .eye-right { animation: panic-eyes 2s ease-in-out infinite; }
        .monster-panic .mouth { animation: mouth-open 1.5s ease-in-out infinite; }
        .monster-panic .arm-left { animation: flail-left 0.6s ease-in-out infinite; transform-origin: right top; }
        .monster-panic .arm-right { animation: flail-right 0.6s ease-in-out infinite 0.15s; transform-origin: left top; }
        .monster-panic .sweat-1, .monster-panic .sweat-2, .monster-panic .sweat-3 { animation: sweat-drop 1.5s ease-in infinite; }
        .monster-panic .sweat-2 { animation-delay: 0.4s; }
        .monster-panic .sweat-3 { animation-delay: 0.8s; }
        .monster-panic .spark { animation: spark-flash 0.8s ease-in-out infinite; }
        .monster-panic .spark:nth-child(2) { animation-delay: 0.2s; }
        .monster-panic .spark:nth-child(3) { animation-delay: 0.4s; }

        @keyframes panic-shake {
            0%, 100% { transform: translateX(0) rotate(0deg); }
            25% { transform: translateX(-3px) rotate(-1deg); }
            75% { transform: translateX(3px) rotate(1deg); }
        }
        @keyframes body-tremble {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(0.98); }
        }
        @keyframes panic-eyes {
            0%, 30%, 100% { transform: scale(1); }
            5% { transform: scale(1.3); }
            10% { transform: scale(0.9); }
            15% { transform: scale(1.2); }
        }
        @keyframes mouth-open {
            0%, 100% { transform: scaleY(1); }
            50% { transform: scaleY(1.3); }
        }
        @keyframes flail-left {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(-30deg); }
            75% { transform: rotate(15deg); }
        }
        @keyframes flail-right {
            0%, 100% { transform: rotate(0deg); }
            25% { transform: rotate(30deg); }
            75% { transform: rotate(-15deg); }
        }
        @keyframes sweat-drop {
            0% { transform: translateY(0); opacity: 1; }
            100% { transform: translateY(25px); opacity: 0; }
        }
        @keyframes spark-flash {
            0%, 100% { opacity: 0; transform: scale(0.5); }
            50% { opacity: 1; transform: scale(1.2); }
        }

        /* Glitch text */
        .glitch {
            position: relative;
        }
        .glitch::before, .glitch::after {
            content: '500';
            position: absolute;
            left: 0;
            right: 0;
            top: 0;
            overflow: hidden;
            clip-path: inset(0 0 0 0);
        }
        .glitch::before {
            animation: glitch-top 2s ease-in-out infinite alternate-reverse;
            color: #f97316;
            left: 2px;
        }
        .glitch::after {
            animation: glitch-bottom 2s ease-in-out infinite alternate;
            color: #ef4444;
            left: -2px;
        }
        @keyframes glitch-top {
            0%, 90%, 100% { clip-path: inset(0 0 80% 0); }
            93% { clip-path: inset(10% 0 60% 0); transform: translateX(3px); }
            96% { clip-path: inset(40% 0 20% 0); transform: translateX(-3px); }
        }
        @keyframes glitch-bottom {
            0%, 90%, 100% { clip-path: inset(80% 0 0 0); }
            93% { clip-path: inset(50% 0 10% 0); transform: translateX(-3px); }
            96% { clip-path: inset(20% 0 40% 0); transform: translateX(3px); }
        }

        /* Warning stripes */
        .warning-stripe {
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 10px,
                rgba(251, 191, 36, 0.1) 10px,
                rgba(251, 191, 36, 0.1) 20px
            );
        }
    </style>
</head>

<body class="bg-gradient-to-br from-orange-50 via-white to-red-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 antialiased min-h-screen">
    <div class="min-h-screen flex items-center justify-center px-4 py-12 relative overflow-hidden warning-stripe">

        {{-- Decorative --}}
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-20 -right-20 w-72 h-72 bg-orange-200/30 dark:bg-orange-900/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-red-200/30 dark:bg-red-900/10 rounded-full blur-3xl"></div>
        </div>

        <div class="max-w-lg w-full text-center relative z-10">
            {{-- Animated Panicking Monster --}}
            <div class="monster-panic mx-auto mb-8 w-56 h-56 sm:w-64 sm:h-64">
                <svg viewBox="0 0 200 200" class="w-full h-full">
                    {{-- Shadow --}}
                    <ellipse cx="100" cy="186" rx="45" ry="7" fill="rgba(0,0,0,0.07)">
                        <animate attributeName="rx" values="45;40;45" dur="0.5s" repeatCount="indefinite"/>
                    </ellipse>

                    {{-- Sparks around --}}
                    <g class="spark">
                        <polygon points="42,70 45,65 48,70 45,72" fill="#f97316"/>
                    </g>
                    <g class="spark">
                        <polygon points="155,55 158,50 161,55 158,57" fill="#ef4444"/>
                    </g>
                    <g class="spark">
                        <polygon points="60,45 63,40 66,45 63,47" fill="#fbbf24"/>
                    </g>

                    {{-- Sweat drops --}}
                    <g class="sweat-1">
                        <ellipse cx="63" cy="75" rx="2.5" ry="4" fill="#60a5fa"/>
                    </g>
                    <g class="sweat-2">
                        <ellipse cx="140" cy="80" rx="2" ry="3.5" fill="#60a5fa"/>
                    </g>
                    <g class="sweat-3">
                        <ellipse cx="72" cy="60" rx="2" ry="3" fill="#93c5fd"/>
                    </g>

                    {{-- Left arm flailing --}}
                    <g class="arm-left">
                        <path d="M62 115 Q45 100 38 108 Q32 115 38 118 Q42 120 48 115" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                    </g>

                    {{-- Right arm flailing --}}
                    <g class="arm-right">
                        <path d="M138 115 Q155 100 162 108 Q168 115 162 118 Q158 120 152 115" fill="#f97316" stroke="#ea580c" stroke-width="1"/>
                    </g>

                    {{-- Body --}}
                    <g class="body">
                        <ellipse cx="100" cy="140" rx="42" ry="38" fill="#f97316"/>
                        <ellipse cx="100" cy="142" rx="36" ry="30" fill="#fb923c"/>

                        {{-- Belly --}}
                        <ellipse cx="100" cy="148" rx="22" ry="16" fill="#fed7aa"/>

                        {{-- Belly crack (error symbol) --}}
                        <path d="M94 140 L98 148 L94 148 L100 158" fill="none" stroke="#c2410c" stroke-width="2" stroke-linecap="round"/>
                    </g>

                    {{-- Feet --}}
                    <ellipse cx="82" cy="176" rx="13" ry="6" fill="#ea580c"/>
                    <ellipse cx="118" cy="176" rx="13" ry="6" fill="#ea580c"/>

                    {{-- Head --}}
                    <g>
                        {{-- Head circle --}}
                        <circle cx="100" cy="88" r="33" fill="#f97316"/>
                        <circle cx="100" cy="90" r="28" fill="#fb923c"/>

                        {{-- Hair/spikes (standing up from shock) --}}
                        <path d="M80 60 L78 42 L85 55" fill="#ea580c"/>
                        <path d="M95 55 L93 35 L100 50" fill="#ea580c"/>
                        <path d="M110 55 L112 35 L105 50" fill="#ea580c"/>
                        <path d="M120 60 L125 42 L118 55" fill="#ea580c"/>

                        {{-- Eyes (wide open, panicking) --}}
                        <g class="eye-left">
                            <circle cx="88" cy="84" r="11" fill="white"/>
                            <circle cx="88" cy="85" r="7" fill="#1e1e1e"/>
                            <circle cx="86" cy="82" r="3" fill="white"/>
                            {{-- Spiral pupil for dizziness --}}
                            <circle cx="88" cy="85" r="3" fill="none" stroke="white" stroke-width="0.8" opacity="0.5">
                                <animateTransform attributeName="transform" type="rotate" values="0 88 85;360 88 85" dur="3s" repeatCount="indefinite"/>
                            </circle>
                        </g>
                        <g class="eye-right">
                            <circle cx="112" cy="84" r="11" fill="white"/>
                            <circle cx="112" cy="85" r="7" fill="#1e1e1e"/>
                            <circle cx="110" cy="82" r="3" fill="white"/>
                            <circle cx="112" cy="85" r="3" fill="none" stroke="white" stroke-width="0.8" opacity="0.5">
                                <animateTransform attributeName="transform" type="rotate" values="0 112 85;360 112 85" dur="3s" repeatCount="indefinite"/>
                            </circle>
                        </g>

                        {{-- Mouth (open screaming) --}}
                        <g class="mouth">
                            <ellipse cx="100" cy="106" rx="10" ry="8" fill="#7c2d12"/>
                            <ellipse cx="100" cy="104" rx="7" ry="4" fill="#451a03"/>
                            {{-- Tongue --}}
                            <ellipse cx="100" cy="110" rx="5" ry="3" fill="#f87171"/>
                        </g>
                    </g>

                    {{-- Smoke/steam particles --}}
                    <circle cx="145" cy="70" r="3" fill="#d4d4d8" opacity="0.4">
                        <animate attributeName="cy" values="70;50;30" dur="2s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.4;0.2;0" dur="2s" repeatCount="indefinite"/>
                        <animate attributeName="r" values="3;5;2" dur="2s" repeatCount="indefinite"/>
                    </circle>
                    <circle cx="55" cy="65" r="2.5" fill="#d4d4d8" opacity="0.3">
                        <animate attributeName="cy" values="65;45;25" dur="2.5s" repeatCount="indefinite"/>
                        <animate attributeName="opacity" values="0.3;0.15;0" dur="2.5s" repeatCount="indefinite"/>
                        <animate attributeName="r" values="2.5;4;1.5" dur="2.5s" repeatCount="indefinite"/>
                    </circle>
                </svg>
            </div>

            {{-- Glitch Error Code --}}
            <h1 class="glitch text-7xl sm:text-8xl font-black bg-gradient-to-r from-orange-600 to-red-600 bg-clip-text text-transparent mb-3">
                500
            </h1>
            <h2 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Kesalahan Server!</h2>

            <p class="text-gray-500 dark:text-gray-400 mb-8 max-w-md mx-auto leading-relaxed">
                Monster server kami sedang panik! Terjadi kesalahan internal. Tim teknis sudah diberitahu. Silakan coba lagi dalam beberapa saat.
            </p>

            {{-- Actions --}}
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : '/' }}"
                    class="inline-flex items-center px-6 py-3 bg-white dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-gray-600 transition-all shadow-sm border border-gray-200 dark:border-gray-600 hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    Kembali
                </a>
                <a href="/"
                    class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-xl font-semibold hover:from-orange-700 hover:to-red-700 transition-all shadow-lg shadow-orange-500/25 hover:shadow-orange-500/40 hover:scale-105 active:scale-95">
                    <svg class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182"/>
                    </svg>
                    Coba Lagi
                </a>
            </div>
        </div>
    </div>
</body>

</html>