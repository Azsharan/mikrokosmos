@props([
    'value' => null,
    'format' => 'Y-m-d H:i',
    'default' => '',
    'class' => 'text-sm text-neutral-700 dark:text-neutral-200',
])

@php
    if ($value instanceof \DateTimeInterface) {
        $display = $value->format($format);
    } elseif ($value) {
        $display = date($format, strtotime($value));
    } else {
        $display = $default;
    }
@endphp

<span class="{{ $class }}">{{ $display }}</span>
