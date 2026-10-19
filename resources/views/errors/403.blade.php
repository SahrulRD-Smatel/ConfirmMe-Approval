<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>403 - {{ __('Akses Ditolak') }} | ConfirmMe</title>
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

            {{-- Illustration: shield with lock (line art, no mascot) --}}
            <div class="anim-fade-up mx-auto mb-8 h-32 w-32 sm:h-36 sm:w-36">
                <svg viewBox="0 0 120 120" class="h-full w-full" fill="none" aria-hidden="true">
                    <path d="M60 10 L98 25 V57 C98 84 79 101 60 110 C41 101 22 84 22 57 V25 Z"
                        fill="#fafafa" stroke="#b91c1c" stroke-width="3.5" stroke-linejoin="round"/>
                    <rect x="48" y="58" width="24" height="19" rx="4" fill="#b91c1c"/>
                    <path d="M53 58 V50 a7 7 0 0 1 14 0 V58" stroke="#b91c1c" stroke-width="4" stroke-linecap="round"/>
                    <circle cx="60" cy="66" r="2.4" fill="#ffffff"/>
                    <line x1="60" y1="68" x2="60" y2="72" stroke="#ffffff" stroke-width="2.4" stroke-linecap="round"/>
                </svg>
            </div>

            <p class="anim-fade-up text-xs font-bold uppercase tracking-[0.2em] text-primary-700 dark:text-primary-400" style="animation-delay:0.08s">
                {{ __('Kesalahan 403') }}
            </p>
            <h1 class="anim-fade-up mt-2 text-6xl font-black tracking-tight text-gray-900 dark:text-white sm:text-7xl" style="animation-delay:0.14s">
                403
            </h1>
            <h2 class="anim-fade-up mt-3 text-xl font-bold text-gray-800 dark:text-gray-100 sm:text-2xl" style="animation-delay:0.2s">
                {{ __('Akses Ditolak') }}
            </h2>
            <p class="anim-fade-up mx-auto mt-4 max-w-md text-sm leading-relaxed text-gray-500 dark:text-gray-400 sm:text-base" style="animation-delay:0.26s">
                {{ __('Anda tidak memiliki izin untuk mengakses halaman ini. Jika Anda merasa ini keliru, hubungi administrator sistem.') }}
            </p>

            <div class="anim-fade-up mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row" style="animation-delay:0.32s">
                <a href="{{ url()->previous() !== url()->current() ? url()->previous() : route('dashboard') }}"
                    class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 bg-white px-6 py-3 text-sm font-semibold text-gray-700 transition-all hover:-translate-y-0.5 hover:bg-gray-50 hover:shadow-md dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800 sm:w-auto">
                    <svg class="mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                    </svg>
                    {{ __('Kembali') }}
                </a>
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
