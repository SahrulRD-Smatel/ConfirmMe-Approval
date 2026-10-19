<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>500 - {{ __('Kesalahan Server') }} | ConfirmMe</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>

<body class="min-h-screen bg-white antialiased dark:bg-gray-950">
    <div class="flex min-h-screen items-center justify-center px-4 py-12 sm:px-6">
        <div class="w-full max-w-lg text-center">

            {{-- Illustration: server stack with alert (line art, no mascot) --}}
            <div class="anim-fade-up mx-auto mb-8 h-32 w-32 sm:h-36 sm:w-36">
                <svg viewBox="0 0 120 120" class="h-full w-full" fill="none" aria-hidden="true">
                    <rect x="24" y="14" width="62" height="24" rx="5" fill="#fafafa" stroke="#b91c1c" stroke-width="3.5"/>
                    <rect x="24" y="46" width="62" height="24" rx="5" fill="#fafafa" stroke="#b91c1c" stroke-width="3.5"/>
                    <rect x="24" y="78" width="62" height="24" rx="5" fill="#fafafa" stroke="#9ca3af" stroke-width="3.5"/>
                    <circle cx="35" cy="26" r="3.2" fill="#16a34a"/>
                    <circle cx="35" cy="58" r="3.2" fill="#16a34a"/>
                    <circle cx="35" cy="90" r="3.2" fill="#b91c1c"/>
                    <line x1="46" y1="26" x2="72" y2="26" stroke="#d1d5db" stroke-width="4" stroke-linecap="round"/>
                    <line x1="46" y1="58" x2="72" y2="58" stroke="#d1d5db" stroke-width="4" stroke-linecap="round"/>
                    <path d="M92 74 L106 98 H78 Z" fill="#fef2f2" stroke="#b91c1c" stroke-width="3.5" stroke-linejoin="round"/>
                    <line x1="92" y1="83" x2="92" y2="89" stroke="#b91c1c" stroke-width="3.5" stroke-linecap="round"/>
                    <circle cx="92" cy="93.5" r="2" fill="#b91c1c"/>
                </svg>
            </div>

            <p class="anim-fade-up text-xs font-bold uppercase tracking-[0.2em] text-primary-700 dark:text-primary-400" style="animation-delay:0.08s">
                {{ __('Kesalahan 500') }}
            </p>
            <h1 class="anim-fade-up mt-2 text-6xl font-black tracking-tight text-gray-900 dark:text-white sm:text-7xl" style="animation-delay:0.14s">
                500
            </h1>
            <h2 class="anim-fade-up mt-3 text-xl font-bold text-gray-800 dark:text-gray-100 sm:text-2xl" style="animation-delay:0.2s">
                {{ __('Terjadi Kesalahan Server') }}
            </h2>
            <p class="anim-fade-up mx-auto mt-4 max-w-md text-sm leading-relaxed text-gray-500 dark:text-gray-400 sm:text-base" style="animation-delay:0.26s">
                {{ __('Mohon maaf, terjadi gangguan pada server kami. Tim teknis telah diberi tahu. Silakan coba lagi beberapa saat.') }}
            </p>

            <div class="anim-fade-up mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row" style="animation-delay:0.32s">
                <button type="button" onclick="window.location.reload()"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition-all hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 sm:w-auto">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    {{ __('Coba Lagi') }}
                </button>
                <a href="{{ route('dashboard') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-primary-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all hover:-translate-y-0.5 hover:bg-primary-800 hover:shadow-md sm:w-auto">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    {{ __('Ke Dashboard') }}
                </a>
            </div>

            <p class="anim-fade-up mt-10 text-xs text-gray-400 dark:text-gray-500" style="animation-delay:0.4s">
                ConfirmMe &middot; PT. Info Solusindo Data Utama
            </p>
        </div>
    </div>
</body>

</html>
