@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1.5 text-sm font-medium text-danger-600 dark:text-danger-400']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
