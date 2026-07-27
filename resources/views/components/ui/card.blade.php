@props([
    'padding' => true,
    'hover' => false,
])


@php
    $classes = 'bg-white dark:bg-gray-800 rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] dark:shadow-none ring-1 ring-gray-900/5 dark:ring-white/10 overflow-hidden relative';
    $classes .= $padding ? ' p-6' : '';
    $classes .= $hover ? ' transition-all duration-300 hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] hover:-translate-y-1 cursor-pointer' : '';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
