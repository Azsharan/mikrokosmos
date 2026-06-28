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
        $siteSettings = \App\Models\SiteSetting::query()->latest()->first();
        $instagramLink = ($siteSettings && $siteSettings->instagram_enabled)
            ? ($siteSettings->instagram_url ?: 'https://www.instagram.com/mikrokosmos_ourense')
            : null;
        $tiktokLink = ($siteSettings && $siteSettings->tiktok_enabled)
            ? ($siteSettings->tiktok_url ?: 'https://www.tiktok.com/@mikrokosmos_ourense')
            : null;
        $contactEmail = config('shop.contact.email');
    @endphp
    <body class="min-h-screen bg-[#1a0430] text-white antialiased">

        {{-- Decorative background blobs --}}
        <div class="pointer-events-none fixed inset-0 overflow-hidden">
            <div class="absolute -right-48 -top-48 h-[500px] w-[500px] rounded-full bg-[#8a2ab6]/30 blur-3xl"></div>
            <div class="absolute -bottom-48 -left-48 h-[500px] w-[500px] rounded-full bg-[#b184db]/20 blur-3xl"></div>
            <div class="absolute left-1/2 top-1/2 h-[700px] w-[700px] -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#8a2ab6]/10 blur-3xl"></div>
        </div>

        <main class="relative mx-auto flex min-h-screen w-full max-w-xl flex-col items-center justify-center px-6 py-16 text-center">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="mb-10">
                @if ($logoImageSource)
                    <img src="{{ $logoImageSource }}" alt="{{ $appName }}" class="h-52 w-52 object-contain drop-shadow-2xl sm:h-60 sm:w-60">
                @else
                    <div class="flex size-52 items-center justify-center rounded-full bg-[#8a2ab6] sm:size-60">
                        <x-app-logo-icon class="size-24 fill-current text-white" />
                    </div>
                @endif
            </a>

            {{-- Label --}}
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#b184db]">{{ $appName }}</p>

            {{-- Heading --}}
            <h1 class="mt-3 text-5xl font-black tracking-[0.15em] text-white sm:text-7xl">
                {{ __('PRÓXIMAMENTE') }}
            </h1>

            {{-- Orange accent rule --}}
            <div class="mt-6 h-0.5 w-20 rounded-full bg-[#f5a520]"></div>

            {{-- Description --}}
            <p class="mt-6 text-base leading-relaxed text-white/70 sm:text-lg">
                {{ __('Estamos trabajando para traerte una mejor experiencia. Vuelve pronto.') }}
            </p>

            {{-- Social + contact --}}
            @if ($instagramLink || $tiktokLink || $contactEmail)
                <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
                    @if ($instagramLink)
                        <a href="{{ $instagramLink }}" target="_blank" rel="noopener"
                            class="flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-medium text-white/80 transition hover:border-[#b184db] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current" aria-hidden="true">
                                <path d="M12 7.3A4.7 4.7 0 1 0 16.7 12 4.71 4.71 0 0 0 12 7.3Zm0 7.8A3.1 3.1 0 1 1 15.1 12 3.11 3.11 0 0 1 12 15.1Zm6-8.44a1.11 1.11 0 1 1-1.11-1.11 1.11 1.11 0 0 1 1.11 1.11ZM21.8 7a6.43 6.43 0 0 0-1.7-3.6A6.43 6.43 0 0 0 16.5 1.7c-1.43-.09-1.87-.12-4.5-.12s-3.07 0-4.5.12A6.43 6.43 0 0 0 3.9 3.4 6.43 6.43 0 0 0 2.2 7c-.09 1.43-.12 1.87-.12 4.5s0 3.07.12 4.5a6.43 6.43 0 0 0 1.7 3.6 6.43 6.43 0 0 0 3.6 1.7c1.43.09 1.87.12 4.5.12s3.07 0 4.5-.12a6.43 6.43 0 0 0 3.6-1.7 6.43 6.43 0 0 0 1.7-3.6c.09-1.43.12-1.87.12-4.5s0-3.07-.12-4.5ZM20 15.7a4.88 4.88 0 0 1-1.2 2.9 4.88 4.88 0 0 1-2.9 1.2c-1.14.05-1.48.07-4.1.07s-3 0-4.1-.07A4.88 4.88 0 0 1 4.8 18.6 4.88 4.88 0 0 1 3.6 15.7c-.05-1.14-.07-1.48-.07-4.1s0-3 .07-4.1a4.88 4.88 0 0 1 1.2-2.9 4.88 4.88 0 0 1 2.9-1.2c1.14-.05 1.48-.07 4.1-.07s3 0 4.1.07a4.88 4.88 0 0 1 2.9 1.2 4.88 4.88 0 0 1 1.2 2.9c.05 1.14.07 1.48.07 4.1s0 3-.07 4.1Z"/>
                            </svg>
                            Instagram
                        </a>
                    @endif

                    @if ($tiktokLink)
                        <a href="{{ $tiktokLink }}" target="_blank" rel="noopener"
                            class="flex items-center gap-2 rounded-full border border-white/20 px-4 py-2 text-sm font-medium text-white/80 transition hover:border-[#b184db] hover:text-white">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 fill-current" aria-hidden="true">
                                <path d="M21 7.4c-1.9 0-3.8-.6-5.3-1.8v7.9c0 3.9-3.2 7.1-7.1 7.1S1.5 17.4 1.5 13.5s3.2-7.1 7.1-7.1c.4 0 .9 0 1.3.1v3.3c-.4-.1-.8-.2-1.3-.2-2.1 0-3.9 1.7-3.9 3.9s1.7 3.9 3.9 3.9 3.9-1.7 3.9-3.9V1.5h3.2c.2 1.9 1.3 3.6 3 4.6 1 .6 2.2.9 3.4.9v3.4z"/>
                            </svg>
                            TikTok
                        </a>
                    @endif

                    @if ($contactEmail)
                        <a href="mailto:{{ $contactEmail }}"
                            class="rounded-full bg-[#f5a520] px-5 py-2 text-sm font-semibold text-[#8a2ab6] transition hover:bg-[#ffd978]">
                            {{ __('Contáctanos') }}
                        </a>
                    @endif
                </div>
            @endif

            <p class="mt-16 text-xs text-white/30">&copy; {{ now()->year }} {{ $appName }}</p>
        </main>

        @fluxScripts
    </body>
</html>
