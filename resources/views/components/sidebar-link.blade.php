@props(['active' => false, 'href', 'icon' => null])

@php
    $classes = $active
        ? 'group flex items-center gap-3 rounded-xl bg-primary-700 px-3 py-2.5 text-sm font-semibold text-white shadow-sm shadow-primary-950/10'
        : 'group flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm text-primary-100 transition-all duration-200 hover:bg-primary-700 hover:text-white dark:text-gray-300 dark:hover:bg-gray-700';
@endphp

<a href="{{ $href }}" {{ $active ? 'data-active=true' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <x-icon :name="$icon" class="w-4 h-4 flex-shrink-0" />
    @endif
    <span x-show="!sidebarCollapsed" x-cloak class="flex flex-1 items-center justify-between min-w-0">
        {{ $slot }}
    </span>
</a>