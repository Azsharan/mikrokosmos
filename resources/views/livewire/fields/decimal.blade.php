@props([
    'value' => null,
    'default' => '',
    'class' => 'text-sm text-neutral-700 dark:text-neutral-200',
    'prefix' => '',
    'suffix' => '',
    'decimals' => 2,
    'decimal_separator' => '.',
    'thousand_separator' => ',',
])

@php
    $display = $value !== null
        ? number_format((float) $value, $decimals, $decimal_separator, $thousand_separator)
        : $default;
@endphp

<span class="{{ $class }}">{{ $prefix }}{{ $display }}{{ $suffix }}</span>
