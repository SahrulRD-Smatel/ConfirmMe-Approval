@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'inline-flex items-center rounded-xl border border-success-200/80 bg-success-50/80 px-3 py-2 text-sm font-medium text-success-700 dark:border-success-800/60 dark:bg-success-900/20 dark:text-success-300']) }}>
        {{ $status }}
    </div>
@endif
