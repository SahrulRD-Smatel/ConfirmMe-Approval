@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-r-xl border-l-4 border-primary-400 bg-primary-50 py-2 ps-3 pe-4 text-start text-base font-semibold text-primary-700 transition dark:border-primary-600 dark:bg-primary-900/50 dark:text-primary-300 focus:border-primary-700 focus:bg-primary-100 focus:text-primary-800 dark:focus:border-primary-300 dark:focus:bg-primary-900 dark:focus:text-primary-200 transition duration-150 ease-in-out'
            : 'block w-full rounded-r-xl border-l-4 border-transparent py-2 ps-3 pe-4 text-start text-base font-medium text-gray-600 transition hover:border-primary-200 hover:bg-primary-50 hover:text-primary-700 dark:text-gray-400 dark:hover:border-primary-800 dark:hover:bg-gray-800 dark:hover:text-primary-300 focus:outline-none focus:text-gray-800 dark:focus:text-gray-200 focus:bg-gray-50 dark:focus:bg-gray-700 focus:border-gray-300 dark:focus:border-gray-600 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
