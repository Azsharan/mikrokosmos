@props([
    'checked' => false,
    'label' => '',
    'class' => 'inline-flex items-center gap-2 text-sm text-neutral-700 dark:text-neutral-200',
])

<label class="{{ $class }}">
    <input type="checkbox" class="h-4 w-4 rounded border-neutral-300 text-primary-600 focus:ring-primary-500" @checked($checked) disabled>
    @if ($label)
        <span>{{ $label }}</span>
    @endif
</label>
