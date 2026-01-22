@props(['title' => null])

@php($pageTitle = $title ?? config('app.name'))

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head', ['title' => $pageTitle])
    </head>
    <body class="min-h-screen bg-gradient-to-b from-[#f9f5ff] via-[#daccff] to-[#2f1b54] text-[#1c1232] antialiased">
        <div class="flex min-h-screen flex-col bg-gradient-to-b from-transparent via-[#efe5ff]/80 to-[#2f1b54]">
            <header class="border-b border-[#e6c45c]/40 bg-gradient-to-r from-[#512f8c] via-[#b69ef7] to-[#f6f0ff] backdrop-blur text-[#201339]">
                <div class="mx-auto flex w-full max-w-6xl items-center justify-between gap-6 px-4 py-4 lg:px-8">
                    <x-app-logo href="{{ route('home') }}" class="shrink-0" theme="shop" />

                    <nav class="hidden items-center gap-6 text-sm font-medium text-[#523286] md:flex">
                        <a href="#categories" class="transition hover:text-[#b084f5]">{{ __('Colecciones') }}</a>
                        <a href="#featured" class="transition hover:text-[#b084f5]">{{ __('Destacados') }}</a>
                        <a href="#community" class="transition hover:text-[#b084f5]">{{ __('Comunidad') }}</a>
                    </nav>

                    <div class="flex flex-1 flex-wrap items-center justify-end gap-3 text-sm">
                        <a href="#featured" class="rounded-full border border-[#e6c45c] bg-[#fff5d4] px-4 py-2 font-semibold text-[#4a3400] transition hover:bg-[#ffe9a6]">
                            {{ __('Explorar productos') }}
                        </a>

                        @guest('shop')
                            <a href="{{ route('shop.login') }}" class="rounded-full border border-[#c6b3ff] px-4 py-2 font-semibold text-[#4b2d7f] transition hover:bg-[#f1eaff]">
                                {{ __('Iniciar sesión') }}
                            </a>
                            <a href="{{ route('shop.register') }}" class="rounded-full bg-[#7ab44c] px-4 py-2 font-semibold text-white transition hover:bg-[#6aa23d]">
                                {{ __('Crear cuenta') }}
                            </a>
                        @else
                            <a href="{{ route('shop.account') }}" class="rounded-full border border-[#c6b3ff] px-4 py-2 font-semibold text-[#4b2d7f] transition hover:bg-[#f1eaff]">
                                {{ __('Mi cuenta') }}
                            </a>
                            <form method="POST" action="{{ route('shop.logout') }}" class="flex">
                                @csrf
                                <button type="submit" class="rounded-full border border-[#e6c45c] bg-[#ffe9a6] px-4 py-2 font-semibold text-[#4a3400] transition hover:bg-[#ffd96e]">
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
                        <a href="#categories" class="transition hover:text-[#b084f5]">{{ __('Colecciones') }}</a>
                        <a href="#featured" class="transition hover:text-[#b084f5]">{{ __('Destacados') }}</a>
                        <a href="#community" class="transition hover:text-[#b084f5]">{{ __('Comunidad') }}</a>
                    </div>
                </div>
            </footer>
        </div>

        <x-cookie-consent-banner />

        @fluxScripts
    </body>
</html>
