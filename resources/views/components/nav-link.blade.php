@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center px-1 pt-1 border-b-2 border-primary-400 dark:border-primary-600 text-sm font-semibold leading-5 text-primary-800 dark:text-primary-300 focus:outline-none focus:border-primary-700 transition duration-150 ease-in-out'
            : 'inline-flex items-center px-1 pt-1 border-b-2 border-transparent text-sm font-medium leading-5 text-gray-500 dark:text-gray-400 hover:border-primary-200 hover:text-primary-700 dark:hover:border-primary-800 dark:hover:text-primary-300 focus:outline-none focus:text-primary-700 dark:focus:text-primary-300 focus:border-primary-300 dark:focus:border-primary-700 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
