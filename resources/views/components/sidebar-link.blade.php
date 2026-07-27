@props(['active' => false, 'href', 'icon' => null])

@php
    $classes = $active
        ? 'flex items-center gap-3 px-3 py-2 text-sm text-white bg-primary-700 dark:bg-gray-700 rounded-lg font-medium'
        : 'flex items-center gap-3 px-3 py-2 text-sm text-primary-100 hover:text-white hover:bg-primary-700 dark:text-gray-300 dark:hover:bg-gray-700 rounded-lg transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="w-4 h-4 flex-shrink-0" />
    @endif
    <span x-show="!sidebarCollapsed" x-cloak class="truncate">{{ $slot }}</span>
</a>