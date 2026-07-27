@props(['title', 'collapsed' => false])

<div class="mt-4">
    <h3 class="px-3 mb-2 text-xs font-semibold text-primary-300 dark:text-gray-500 uppercase tracking-wider"
        x-show="!sidebarCollapsed">
        {{ $title }}
    </h3>
    <div class="space-y-1">
        {{ $slot }}
    </div>
</div>