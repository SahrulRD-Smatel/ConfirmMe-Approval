<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-xl border border-transparent bg-primary-700 px-4 py-2.5 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition-all duration-200 hover:bg-primary-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-primary-500/20 focus:ring-offset-2 active:bg-primary-900 disabled:cursor-not-allowed disabled:opacity-50 dark:focus:ring-offset-gray-900']) }}>
    {{ $slot }}
</button>
