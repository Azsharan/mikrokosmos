@props([
    'sidebar' => false,
    'theme' => 'default',
    'name' => null,
])



@php
    $nameClasses = match($theme) {
        'shop' => 'text-2xl font-extrabold tracking-tight text-black',
        default => 'text-lg font-semibold text-white-900',
    };
    $logoWrapperClasses = match($theme) {
        'shop' => 'flex aspect-square size-14 items-center justify-center rounded-full bg-black text-white',
        default => 'flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground',
    };
    $logoIconClasses = match($theme) {
        'shop' => 'size-6 fill-current text-white',
        default => 'size-5 fill-current text-white',
    };
    $appName = $name ?? config('app.name', 'Laravel Starter Kit');

    static $logoImageSourceCache;
    static $logoImageInitialized;

    if (! $logoImageInitialized) {
        $logoPath = storage_path('app/logo-noBG.png');
        $logoImageSourceCache = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
        $logoImageInitialized = true;
    }

    $logoImageSource = $logoImageSourceCache;
    $logoImageClasses = match (true) {
        $theme === 'shop' && $sidebar => 'h-14 w-14 object-contain',
        $theme === 'shop' => 'h-16 w-16 object-contain',
        $sidebar => 'h-10 w-10 object-contain',
        default => 'h-12 w-12 object-contain',
    };
@endphp

@php
    $baseClasses = $sidebar
        ? 'flex items-center gap-3 rounded-lg px-2 py-1'
        : 'flex items-center gap-3';
    $classes = \Illuminate\Support\Arr::toCssClasses([
        $baseClasses,
        $attributes->get('class'),
    ]);
    $href = $attributes->get('href', '/');
@endphp

<a href="{{ $href }}" {{ $attributes->except('href', 'class')->merge(['class' => $classes]) }}>
    @if ($logoImageSource)
        <img src="{{ $logoImageSource }}" alt="{{ $appName }}" class="{{ $logoImageClasses }}">
    @else
        <div class="{{ $logoWrapperClasses }}">
            <x-app-logo-icon class="{{ $logoIconClasses }}" />
        </div>
    @endif
    <div class="{{ $nameClasses }}">{{ $appName }}</div>
</a>
