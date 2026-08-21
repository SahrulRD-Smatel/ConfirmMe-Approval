@props([
    'padding' => true,
    'hover' => false,
])


@php
    $classes = 'relative overflow-hidden rounded-3xl border border-gray-200/80 bg-white shadow-soft ring-1 ring-black/5 dark:border-gray-800/80 dark:bg-gray-900 dark:shadow-none dark:ring-white/10';
    $classes .= $padding ? ' p-6' : '';
    $classes .= $hover ? ' transition-all duration-300 hover:-translate-y-0.5 hover:shadow-soft-lg cursor-pointer' : '';
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
