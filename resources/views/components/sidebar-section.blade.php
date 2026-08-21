@props(['title', 'collapsed' => false])

<div class="mt-6">
    <h3 class="mb-2 px-3 text-[10px] font-bold uppercase tracking-[0.16em] text-primary-300/80 dark:text-gray-500"
        x-show="!sidebarCollapsed">
        {{ $title }}
    </h3>
    <div class="space-y-1.5">
        {{ $slot }}
    </div>
</div>