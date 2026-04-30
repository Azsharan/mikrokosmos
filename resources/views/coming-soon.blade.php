<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => __('Próximamente')])
    </head>
    @php
        $appName = config('app.name', 'Mikrokosmos');
        $logoPath = storage_path('app/logo-noBG.png');
        $logoImageSource = file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
    @endphp
    <body class="min-h-screen bg-gradient-to-br from-[#140b25] via-[#2b1950] to-[#1a1031] text-white antialiased">
        <main class="mx-auto flex min-h-screen w-full max-w-6xl flex-col items-center justify-center px-6 text-center">
            <a href="{{ route('home') }}" class="mb-6 inline-flex flex-col items-center">
                @if ($logoImageSource)
                    <img src="{{ $logoImageSource }}" alt="{{ $appName }}" class="h-60 w-60 object-contain sm:h-56 sm:w-56">
                @else
                    <div class="flex size-44 items-center justify-center rounded-full bg-black text-white sm:size-56">
                        <x-app-logo-icon class="size-20 fill-current text-white sm:size-28" />
                    </div>
                @endif
                {{-- <span class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">{{ $appName }}</span> --}}
            </a>

            <h1 class="text-5xl font-black tracking-[0.2em] text-[#e6c45c] sm:text-7xl">
                {{ __('PRÓXIMAMENTE') }}
            </h1>
            <p class="mt-6 max-w-2xl text-sm text-[#e8dcff]/85 sm:text-base">
                {{ __('Estamos trabajando para traerte una mejor experiencia. Vuelve pronto.') }}
            </p>
        </main>
    </body>
</html>
