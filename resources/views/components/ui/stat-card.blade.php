@props([
    'title',
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'primary',
    'href' => null,
    'valueSize' => 'text-3xl sm:text-4xl font-bold font-display tracking-tight',
])

@php
$colors = [
    'primary' => 'bg-primary-100 text-primary-600 dark:bg-primary-900/30 dark:text-primary-400',
    'success' => 'bg-success-100 text-success-600 dark:bg-success-900/30 dark:text-success-400',
    'danger' => 'bg-danger-100 text-danger-600 dark:bg-danger-900/30 dark:text-danger-400',
    'warning' => 'bg-warning-100 text-warning-600 dark:bg-warning-900/30 dark:text-warning-400',
    'gray' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
];

$iconBg = $colors[$color] ?? $colors['primary'];

// Detect if value looks like a currency (starts with Rp)
$isCurrency = is_string($value) && str_starts_with(trim($value), 'Rp');
@endphp

<x-ui.card :hover="$href ? true : false" {{ $attributes }}>
    @if($href)
        <a href="{{ $href }}" class="block">
    @endif
    
    <div class="flex items-center justify-between mb-4">
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide truncate pr-4">{{ $title }}</p>
        
        @if($icon)
            <div class="flex-shrink-0 p-2.5 rounded-xl {{ $iconBg }} bg-opacity-70">
                <x-icon :name="$icon" class="w-5 h-5" />
            </div>
        @endif
    </div>

    <div class="flex-1 min-w-0">
        @if($isCurrency)
            {{-- Currency value with hover tooltip --}}
            <div class="relative group" x-data="{ showTooltip: false }">
                <p class="{{ $valueSize }} text-gray-900 dark:text-white truncate cursor-default"
                   @mouseenter="showTooltip = true"
                   @mouseleave="showTooltip = false">
                   {{ $value }}
                </p>
                
                {{-- Tooltip with full value --}}
                <div x-show="showTooltip"
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-1"
                     class="absolute z-50 bottom-full left-0 mb-2 px-3 py-2 bg-gray-900 dark:bg-gray-700 text-white text-sm font-semibold rounded-lg shadow-lg whitespace-nowrap pointer-events-none"
                     style="display: none;">
                    {{ $value }}
                    {{-- Arrow --}}
                    <div class="absolute top-full left-4 w-0 h-0 border-l-[6px] border-r-[6px] border-t-[6px] border-l-transparent border-r-transparent border-t-gray-900 dark:border-t-gray-700"></div>
                </div>
            </div>
        @else
            <p class="{{ $valueSize }} text-gray-900 dark:text-white truncate">{{ $value }}</p>
        @endif
        
        @if($trend)
            <div class="mt-2 flex items-center text-sm font-medium">
                @if($trendUp)
                    <div class="flex items-center text-success-600 dark:text-success-400 bg-success-50 dark:bg-success-900/30 px-1.5 py-0.5 rounded-md mr-2">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                        </svg>
                        <span>{{ $trend }}</span>
                    </div>
                @else
                    <div class="flex items-center text-danger-600 dark:text-danger-400 bg-danger-50 dark:bg-danger-900/30 px-1.5 py-0.5 rounded-md mr-2">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0v-8m0 8l-8-8-4 4-6-6"/>
                        </svg>
                        <span>{{ $trend }}</span>
                    </div>
                @endif
                <span class="text-gray-400 dark:text-gray-500">dari bulan lalu</span>
            </div>
        @endif
    </div>
    
    @if(trim($slot) !== '')
        <div class="mt-4">
            {{ $slot }}
        </div>
    @endif
    
    @if($href)
        </a>
    @endif
</x-ui.card>
