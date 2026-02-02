@props(['title' => null])

@php
    $resolvedTitle = $title ?? config('app.name');
    $storeNavLinks = [
        ['href' => '#categories', 'label' => __('Colecciones')],
        ['href' => '#featured', 'label' => __('Destacados')],
        ['href' => '#community', 'label' => __('Comunidad')],
        ['href' => route('shop.events.index'), 'label' => __('Eventos')],
        ['href' => route('shop.tables.index'), 'label' => __('Reservar mesa')],
    ];

    $buttonVariants = [
        'primary' => 'rounded-full border border-[#e6c45c] bg-[#fff5d4] px-4 py-2 font-semibold text-[#4a3400] transition hover:bg-[#ffe9a6]',
        'outline' => 'rounded-full border border-[#c6b3ff] px-4 py-2 font-semibold text-[#4b2d7f] transition hover:bg-[#f1eaff]',
        'success' => 'rounded-full border border-[#7ab44c] px-4 py-2 font-semibold text-[#2f5d11] transition hover:bg-[#f0ffe0]',
        'ghost' => 'rounded-full border border-transparent px-4 py-2 font-semibold text-[#4b2d7f] hover:bg-white/30 transition',
    ];

    $guestActions = [
        ['href' => route('shop.login'), 'label' => __('Iniciar sesión'), 'variant' => 'outline'],
        ['href' => route('shop.register'), 'label' => __('Crear cuenta'), 'variant' => 'success'],
    ];

    $authActions = [
        ['href' => route('shop.account'), 'label' => __('Mi cuenta'), 'variant' => 'outline'],
    ];
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $resolvedTitle])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#f9f5ff] via-[#daccff] to-[#2f1b54] text-[#1c1232] antialiased">
        <div class="flex min-h-screen flex-col bg-gradient-to-b from-transparent via-[#efe5ff]/80 to-[#2f1b54]">
            <header class="border-b border-[#e6c45c]/40 bg-gradient-to-r from-[#512f8c] via-[#b69ef7] to-[#f6f0ff] backdrop-blur text-[#201339]">
                <div class="mx-auto flex w-full max-w-6xl flex-wrap items-center gap-4 px-4 py-4 lg:flex-nowrap lg:gap-6 lg:px-8">
                    <x-app-logo href="{{ route('home') }}" class="shrink-0" theme="shop" />

                    <nav class="hidden flex-2 items-center gap-4 text-sm font-medium text-[#523286] md:flex">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="transition hover:text-[#b084f5]">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="flex flex-1 flex-wrap items-center justify-end gap-3 text-sm">

                        @guest('shop')
                            @foreach ($guestActions as $action)
                                <a href="{{ $action['href'] }}" class="{{ $buttonVariants[$action['variant']] ?? $buttonVariants['ghost'] }}">
                                    {{ $action['label'] }}
                                </a>
                            @endforeach
                        @else
                            @foreach ($authActions as $action)
                                <a href="{{ $action['href'] }}" class="{{ $buttonVariants[$action['variant']] ?? $buttonVariants['ghost'] }}">
                                    {{ $action['label'] }}
                                </a>
                            @endforeach
                            <form method="POST" action="{{ route('shop.logout') }}" class="flex">
                                @csrf
                                <button type="submit" class="{{ $buttonVariants['primary'] }}">
                                    {{ __('Cerrar sesión') }}
                                </button>
                            </form>
                        @endguest
                    </div>
                </div>
            </header>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="border-t border-[#c6b3ff] bg-gradient-to-r from-[#f7f0ff] to-[#dcd0ff] text-[#4b2d7f]">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-2 px-4 py-8 text-sm lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <p>&copy; {{ now()->year }} {{ config('app.name') }}.</p>
                    <div class="flex flex-wrap items-center gap-4">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="transition hover:text-[#b084f5]">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </footer>
        </div>

        <x-cookie-consent-banner />

        @fluxScripts
    </body>
</html>
