@props([
    'value' => '',
    'limit' => null,
    'class' => 'text-sm text-neutral-700 dark:text-neutral-200',
])

@php
    $display = $value;
    if ($limit && is_string($value)) {
        $display = \Illuminate\Support\Str::limit($value, $limit);
    }
@endphp

<span class="{{ $class }}">{{ $display }}</span>
