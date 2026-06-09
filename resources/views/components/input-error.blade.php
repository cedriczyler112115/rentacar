@props(['messages'])

@php
    $flatMessages = collect((array) $messages)
        ->flatten()
        ->filter(fn ($m) => is_string($m) || is_numeric($m))
        ->map(fn ($m) => (string) $m)
        ->values()
        ->all();
@endphp

@if (count($flatMessages) > 0)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ($flatMessages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
