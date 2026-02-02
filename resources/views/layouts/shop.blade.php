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

    $instagramCacheKey = 'site_settings_active';
    $siteSettings = cache()->remember($instagramCacheKey, now()->addMinutes(15), function () {
        return \App\Models\SiteSetting::query()->latest()->first();
    });
    $defaultInstagram = 'https://www.instagram.com/mikrokosmos_ourense?igsh=cndhMWNod215ODdh';
    $instagramLink = ($siteSettings && $siteSettings->instagram_enabled)
        ? ($siteSettings->instagram_url ?: $defaultInstagram)
        : null;
    $tiktokLink = ($siteSettings && $siteSettings->tiktok_enabled)
        ? ($siteSettings->tiktok_url ?: 'https://www.tiktok.com/@mikrokosmos_ourense')
        : null;
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
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-8 text-sm lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <p>&copy; {{ now()->year }} {{ config('app.name') }}.</p>
                    <div class="flex flex-wrap items-center gap-4">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="transition hover:text-[#b084f5]">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                    @if ($instagramLink || $tiktokLink)
                        <div class="flex items-center text-sm gap-2 text-[#4b2d7f]">
                            @if ($instagramLink)
                                <a href="{{ $instagramLink }}" class="flex items-center rounded-full border border-[#c6b3ff] px-2 py-2 transition hover:bg-[#f1eaff]" target="_blank" rel="noopener">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-[#c13584]" aria-hidden="true">
                                        <path d="M12 7.3A4.7 4.7 0 1 0 16.7 12 4.71 4.71 0 0 0 12 7.3Zm0 7.8A3.1 3.1 0 1 1 15.1 12 3.11 3.11 0 0 1 12 15.1Zm6-8.44a1.11 1.11 0 1 1-1.11-1.11 1.11 1.11 0 0 1 1.11 1.11ZM21.8 7a6.43 6.43 0 0 0-1.7-3.6A6.43 6.43 0 0 0 16.5 1.7c-1.43-.09-1.87-.12-4.5-.12s-3.07 0-4.5.12A6.43 6.43 0 0 0 3.9 3.4 6.43 6.43 0 0 0 2.2 7c-.09 1.43-.12 1.87-.12 4.5s0 3.07.12 4.5a6.43 6.43 0 0 0 1.7 3.6 6.43 6.43 0 0 0 3.6 1.7c1.43.09 1.87.12 4.5.12s3.07 0 4.5-.12a6.43 6.43 0 0 0 3.6-1.7 6.43 6.43 0 0 0 1.7-3.6c.09-1.43.12-1.87.12-4.5s0-3.07-.12-4.5ZM20 15.7a4.88 4.88 0 0 1-1.2 2.9 4.88 4.88 0 0 1-2.9 1.2c-1.14.05-1.48.07-4.1.07s-3 0-4.1-.07A4.88 4.88 0 0 1 4.8 18.6 4.88 4.88 0 0 1 3.6 15.7c-.05-1.14-.07-1.48-.07-4.1s0-3 .07-4.1a4.88 4.88 0 0 1 1.2-2.9 4.88 4.88 0 0 1 2.9-1.2c1.14-.05 1.48-.07 4.1-.07s3 0 4.1.07a4.88 4.88 0 0 1 2.9 1.2 4.88 4.88 0 0 1 1.2 2.9c.05 1.14.07 1.48.07 4.1s0 3-.07 4.1Z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($tiktokLink)
                                <a href="{{ $tiktokLink }}" class="flex items-center rounded-full border border-[#c6b3ff] px-2 py-2 transition hover:bg-[#f1eaff]" target="_blank" rel="noopener">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-[#c13584]" aria-hidden="true">
                                        <path d="M21 7.4c-1.9 0-3.8-.6-5.3-1.8v7.9c0 3.9-3.2 7.1-7.1 7.1S1.5 17.4 1.5 13.5s3.2-7.1 7.1-7.1c.4 0 .9 0 1.3.1v3.3c-.4-.1-.8-.2-1.3-.2-2.1 0-3.9 1.7-3.9 3.9s1.7 3.9 3.9 3.9 3.9-1.7 3.9-3.9V1.5h3.2c.2 1.9 1.3 3.6 3 4.6 1 .6 2.2.9 3.4.9v3.4z"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </footer>
        </div>

        <x-cookie-consent-banner />

        @fluxScripts
    </body>
</html>
