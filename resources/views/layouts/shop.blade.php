@props(['title' => null])

@php
    $resolvedTitle = $title ?? config('app.name');
    $storeNavLinks = [
        ['href' => route('shop.events.index'), 'label' => __('Eventos')],
        ['href' => route('shop.tables.index'), 'label' => __('Reservar mesa')],
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
    <body class="min-h-screen bg-white text-[#8a2ab6] antialiased">
        <div class="flex min-h-screen flex-col">

            <header class="bg-[#8a2ab6] border-b border-white/10">
                <div class="mx-auto flex w-full max-w-6xl items-center gap-6 px-4 py-2 lg:px-8">
                    <x-app-logo href="{{ route('home') }}" class="shrink-0" theme="shop" />

                    <nav class="hidden flex-1 items-center gap-6 text-sm font-medium md:flex">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="text-white/70 transition hover:text-white">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>

                    <div class="hidden items-center gap-3 text-sm md:flex">
                        @guest('shop')
                            <a href="{{ route('shop.login') }}" class="font-medium text-white/80 transition hover:text-white">
                                {{ __('Iniciar sesión') }}
                            </a>
                            <a href="{{ route('shop.register') }}" class="rounded-full bg-[#f5a520] px-4 py-2 font-semibold text-[#8a2ab6] transition hover:bg-[#ffd978]">
                                {{ __('Crear cuenta') }}
                            </a>
                        @else
                            <a href="{{ route('shop.account') }}" class="font-medium text-white/80 transition hover:text-white">
                                {{ __('Mi cuenta') }}
                            </a>
                            <form method="POST" action="{{ route('shop.logout') }}">
                                @csrf
                                <button type="submit" class="rounded-full border border-white/25 px-4 py-2 font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">
                                    {{ __('Salir') }}
                                </button>
                            </form>
                        @endguest
                    </div>

                    <button
                        type="button"
                        id="shop-mobile-menu-button"
                        class="ml-auto p-2 text-white/80 transition hover:text-white md:hidden"
                        aria-expanded="false"
                        aria-controls="shop-mobile-menu"
                        aria-label="{{ __('Abrir menú') }}"
                    >
                        <span id="shop-mobile-menu-icon-open" class="text-xl leading-none">☰</span>
                        <span id="shop-mobile-menu-icon-close" class="hidden text-xl leading-none">✕</span>
                    </button>
                </div>

                <div id="shop-mobile-menu" class="hidden border-t border-white/10 bg-[#162060] md:hidden">
                    <div class="mx-auto flex w-full max-w-6xl flex-col gap-1 px-4 py-3 text-sm">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="rounded-lg px-3 py-2.5 text-white/80 transition hover:bg-white/10 hover:text-white">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                        <div class="mt-2 flex flex-col gap-2 border-t border-white/10 pt-3">
                            @guest('shop')
                                <a href="{{ route('shop.login') }}" class="rounded-lg px-3 py-2.5 text-white/80 transition hover:bg-white/10 hover:text-white">
                                    {{ __('Iniciar sesión') }}
                                </a>
                                <a href="{{ route('shop.register') }}" class="rounded-full bg-[#f5a520] px-4 py-2.5 text-center font-semibold text-[#8a2ab6] transition hover:bg-[#ffd978]">
                                    {{ __('Crear cuenta') }}
                                </a>
                            @else
                                <a href="{{ route('shop.account') }}" class="rounded-lg px-3 py-2.5 text-white/80 transition hover:bg-white/10 hover:text-white">
                                    {{ __('Mi cuenta') }}
                                </a>
                                <form method="POST" action="{{ route('shop.logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full rounded-full border border-white/25 px-4 py-2.5 font-semibold text-white/80 transition hover:bg-white/10 hover:text-white">
                                        {{ __('Cerrar sesión') }}
                                    </button>
                                </form>
                            @endguest
                        </div>
                    </div>
                </div>
            </header>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const button = document.getElementById('shop-mobile-menu-button');
                    const menu = document.getElementById('shop-mobile-menu');
                    const openIcon = document.getElementById('shop-mobile-menu-icon-open');
                    const closeIcon = document.getElementById('shop-mobile-menu-icon-close');
                    if (!button || !menu) return;
                    button.addEventListener('click', function () {
                        const isOpen = menu.classList.toggle('hidden');
                        button.setAttribute('aria-expanded', String(!isOpen));
                        openIcon.classList.toggle('hidden');
                        closeIcon.classList.toggle('hidden');
                    });
                });
            </script>

            <main class="flex-1">
                {{ $slot }}
            </main>

            <footer class="bg-[#8a2ab6] text-white">
                <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-4 text-sm lg:flex-row lg:items-center lg:justify-between lg:px-8">
                    <p class="text-white/50">&copy; {{ now()->year }} {{ config('app.name') }}.</p>
                    <nav class="flex flex-wrap items-center gap-5">
                        @foreach ($storeNavLinks as $link)
                            <a href="{{ $link['href'] }}" class="text-white/70 transition hover:text-white">
                                {{ $link['label'] }}
                            </a>
                        @endforeach
                    </nav>
                    @if ($instagramLink || $tiktokLink)
                        <div class="flex items-center gap-2">
                            @if ($instagramLink)
                                <a href="{{ $instagramLink }}" target="_blank" rel="noopener" class="rounded-full border border-white/20 p-2 transition hover:border-white/40 hover:bg-white/10" aria-label="Instagram">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-white/70" aria-hidden="true">
                                        <path d="M12 7.3A4.7 4.7 0 1 0 16.7 12 4.71 4.71 0 0 0 12 7.3Zm0 7.8A3.1 3.1 0 1 1 15.1 12 3.11 3.11 0 0 1 12 15.1Zm6-8.44a1.11 1.11 0 1 1-1.11-1.11 1.11 1.11 0 0 1 1.11 1.11ZM21.8 7a6.43 6.43 0 0 0-1.7-3.6A6.43 6.43 0 0 0 16.5 1.7c-1.43-.09-1.87-.12-4.5-.12s-3.07 0-4.5.12A6.43 6.43 0 0 0 3.9 3.4 6.43 6.43 0 0 0 2.2 7c-.09 1.43-.12 1.87-.12 4.5s0 3.07.12 4.5a6.43 6.43 0 0 0 1.7 3.6 6.43 6.43 0 0 0 3.6 1.7c1.43.09 1.87.12 4.5.12s3.07 0 4.5-.12a6.43 6.43 0 0 0 3.6-1.7 6.43 6.43 0 0 0 1.7-3.6c.09-1.43.12-1.87.12-4.5s0-3.07-.12-4.5ZM20 15.7a4.88 4.88 0 0 1-1.2 2.9 4.88 4.88 0 0 1-2.9 1.2c-1.14.05-1.48.07-4.1.07s-3 0-4.1-.07A4.88 4.88 0 0 1 4.8 18.6 4.88 4.88 0 0 1 3.6 15.7c-.05-1.14-.07-1.48-.07-4.1s0-3 .07-4.1a4.88 4.88 0 0 1 1.2-2.9 4.88 4.88 0 0 1 2.9-1.2c1.14-.05 1.48-.07 4.1-.07s3 0 4.1.07a4.88 4.88 0 0 1 2.9 1.2 4.88 4.88 0 0 1 1.2 2.9c.05 1.14.07 1.48.07 4.1s0 3-.07 4.1Z" />
                                    </svg>
                                </a>
                            @endif
                            @if ($tiktokLink)
                                <a href="{{ $tiktokLink }}" target="_blank" rel="noopener" class="rounded-full border border-white/20 p-2 transition hover:border-white/40 hover:bg-white/10" aria-label="TikTok">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-5 w-5 fill-current text-white/70" aria-hidden="true">
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
