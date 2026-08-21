@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'iconPosition' => 'left',
    'type' => 'button',
    'href' => null,
])

@php
$baseClasses = 'inline-flex items-center justify-center rounded-xl font-semibold transition-all duration-200 focus:outline-none focus:ring-4 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50';

$variants = [
    'primary' => 'bg-primary-700 text-white shadow-sm hover:bg-primary-800 hover:shadow-md focus:ring-primary-500/20',
    'secondary' => 'border border-gray-200 bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-500/20 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700',
    'success' => 'bg-success-600 text-white shadow-sm hover:bg-success-700 hover:shadow-md focus:ring-success-500/20',
    'danger' => 'bg-danger-600 text-white shadow-sm hover:bg-danger-700 hover:shadow-md focus:ring-danger-500/20',
    'warning' => 'bg-warning-500 text-white shadow-sm hover:bg-warning-600 hover:shadow-md focus:ring-warning-500/20',
    'ghost' => 'bg-transparent text-gray-600 hover:bg-gray-100 focus:ring-gray-500/20 dark:text-gray-300 dark:hover:bg-gray-800',
    'outline' => 'border border-primary-700 text-primary-700 hover:bg-primary-50 focus:ring-primary-500/20 dark:border-primary-400 dark:text-primary-300',
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
