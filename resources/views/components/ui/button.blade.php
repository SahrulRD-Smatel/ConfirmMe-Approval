@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';

$variants = [
    'primary' => 'bg-primary-800 text-white hover:bg-primary-900 focus:ring-primary-500 shadow-sm hover:shadow-md',
    'secondary' => 'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600',
    'success' => 'bg-success-600 text-white hover:bg-success-700 focus:ring-success-500 shadow-sm hover:shadow-md',
    'danger' => 'bg-danger-600 text-white hover:bg-danger-700 focus:ring-danger-500 shadow-sm hover:shadow-md',
    'warning' => 'bg-warning-500 text-white hover:bg-warning-600 focus:ring-warning-500 shadow-sm hover:shadow-md',
    'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-500 dark:text-gray-300 dark:hover:bg-gray-800',
    'outline' => 'border-2 border-primary-800 text-primary-800 hover:bg-primary-50 focus:ring-primary-500 dark:text-primary-400 dark:border-primary-400',
];

$sizes = [
    'xs' => 'px-2.5 py-1.5 text-xs gap-1',
    'sm' => 'px-3 py-2 text-sm gap-1.5',
    'md' => 'px-4 py-2.5 text-sm gap-2',
    'lg' => 'px-5 py-3 text-base gap-2',
    'xl' => 'px-6 py-3.5 text-base gap-2.5',
];

$classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon && $iconPosition === 'left')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
        {{ $slot }}
        @if($icon && $iconPosition === 'right')
            <x-icon :name="$icon" class="w-4 h-4" />
        @endif
    </button>
@endif
