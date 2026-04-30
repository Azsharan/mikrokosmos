@php($appName = config('app.name', 'Mikrokosmos'))
@php($shopLocationAddress = config('shop.location.address'))
@php($shopMapsLink = config('shop.location.maps_link'))
@php($shopMapsEmbedUrl = config('shop.location.maps_embed_url'))
@php($shopMapsHref = $shopMapsLink ?: ('https://www.google.com/maps/search/?api=1&query='.urlencode((string) $shopLocationAddress)))
@php($shopContactEmail = config('shop.contact.email'))
@php($shopContactPhone = config('shop.contact.phone'))

<x-layouts::shop :title="__('Home')">
    {{-- <section class="relative overflow-hidden bg-gradient-to-br from-zinc-950 via-zinc-900 to-zinc-800 text-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-16 lg:flex-row lg:items-center lg:px-8 lg:py-24">
            <div class="flex-1 space-y-6">
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-white">{{ $appName }}</p>
                <div class="space-y-4">
                    <h1 class="text-4xl font-semibold leading-tight tracking-tight sm:text-5xl">
                        {{ $tagline }}
                    </h1>
                    <p class="max-w-2xl text-lg text-zinc-200">
                        {{ __('Colecciona, juega y comparte tus pasiones geek con una experiencia curada y humana.') }}
                    </p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="#featured" class="rounded-full bg-white px-6 py-2 font-semibold text-zinc-900 transition hover:bg-zinc-100">
                        {{ __('Ver productos destacados') }}
                    </a>
                    <a href="{{ route('shop.events.index') }}" class="rounded-full bg-amber-400/90 px-6 py-2 font-semibold text-amber-950 transition hover:bg-amber-300" wire:navigate>
                        {{ __('Calendario de eventos') }}
                    </a>
                </div>
            </div>

            @if ($sellingPoints->isNotEmpty())
                <div class="w-full max-w-sm rounded-3xl border border-white/10 bg-white/5 p-6 backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-300">{{ __('Lo que encontrarás') }}</p>
                    <ul class="mt-4 space-y-4 text-sm text-zinc-100">
                        @foreach ($sellingPoints as $point)
                            <li class="flex items-center gap-4 rounded-2xl bg-white/5 p-3">
                                <span class="flex size-10 items-center justify-center rounded-full bg-white/10 text-base font-semibold">
                                    {{ sprintf('%02d', $loop->iteration) }}
                                </span>
                                <span class="font-medium">{{ $point }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section> --}}

    @if ($featuredProducts->isNotEmpty())
        <section id="featured" class="bg-white">
            <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
                <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        {{-- <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Novedades y best sellers') }}</p> --}}
                        <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Novedades') }}</h2>
                    </div>
                    {{-- <p class="max-w-2xl text-sm text-zinc-600">
                        {{ __('Disponibilidad limitada: aparta tus piezas antes de que vuelen del inventario.') }}
                    </p> --}}
                </div>

                <div class="mb-6 flex items-center justify-end gap-2">
                    <button
                        type="button"
                        class="inline-flex size-10 items-center justify-center rounded-full border border-zinc-300 bg-white text-zinc-700 transition hover:border-amber-300 hover:text-amber-700"
                        aria-label="{{ __('Anterior') }}"
                        data-slider-prev
                    >
                        &larr;
                    </button>
                    <button
                        type="button"
                        class="inline-flex size-10 items-center justify-center rounded-full border border-zinc-300 bg-white text-zinc-700 transition hover:border-amber-300 hover:text-amber-700"
                        aria-label="{{ __('Siguiente') }}"
                        data-slider-next
                    >
                        &rarr;
                    </button>
                </div>

                <div
                    class="flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
                    data-featured-slider
                >
                    @foreach ($featuredProducts as $product)
                        <a href="{{ route('shop.products.show', $product) }}" class="group block h-full w-[84%] shrink-0 snap-start sm:w-[48%] lg:w-[31%]" wire:navigate>
                            <article class="flex h-[30rem] flex-col rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                                <div class="mb-4 aspect-[4/3] w-full overflow-hidden rounded-2xl bg-zinc-100">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold uppercase tracking-[0.2em] text-zinc-400">
                                            {{ __('Sin imagen') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="flex flex-1 flex-col space-y-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">
                                        {{ $product->category?->name ?? __('Colección especial') }}
                                    </p>
                                    <h3 class="max-h-16 overflow-hidden text-2xl font-semibold text-zinc-900 group-hover:text-amber-700">{{ $product->name }}</h3>
                                    @if ($product->description)
                                        <p class="max-h-[4.5rem] overflow-hidden text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit($product->description, 140) }}</p>
                                    @endif
                                </div>
                                {{-- <div class="mt-6 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Precio') }}</p>
                                        <p class="text-3xl font-semibold text-zinc-900">${{ number_format((float) $product->price, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Disponibles') }}</p>
                                        <p class="text-2xl font-semibold text-emerald-600">{{ $product->stock }}</p>
                                    </div>
                                </div> --}}
                                <div class="mt-6 flex items-center justify-between text-sm font-semibold">
                                    <span class="text-amber-700">{{ __('Ver detalles') }}</span>
                                    <span aria-hidden="true" class="text-amber-700 group-hover:translate-x-1 transition">&rarr;</span>
                                </div>
                            </article>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="weekly-events" class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-8 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                <div class="lg:ml-auto lg:text-right">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Agenda semanal') }}</p>
                    <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Eventos de esta semana') }}</h2>
                </div>
                {{-- <p class="max-w-2xl text-sm text-zinc-600">
                    {{ __('Inscríbete con antelación para asegurar tu lugar en los eventos publicados.') }}
                </p> --}}
            </div>

            <div class="mb-6 flex items-center justify-end gap-2">
                <button
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-zinc-300 bg-white text-zinc-700 transition hover:border-amber-300 hover:text-amber-700"
                    aria-label="{{ __('Anterior') }}"
                    data-slider-prev="events"
                >
                    &larr;
                </button>
                <button
                    type="button"
                    class="inline-flex size-10 items-center justify-center rounded-full border border-zinc-300 bg-white text-zinc-700 transition hover:border-amber-300 hover:text-amber-700"
                    aria-label="{{ __('Siguiente') }}"
                    data-slider-next="events"
                >
                    &rarr;
                </button>
            </div>

            <div
                class="flex snap-x snap-mandatory gap-6 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [-ms-overflow-style:none] [&::-webkit-scrollbar]:hidden"
                data-slider="events"
            >
                @foreach ($weeklyDays as $day)
                    <a href="{{ route('shop.events.index', ['month' => $day['date']->format('Y-m'), 'day' => $day['date']->toDateString()]) }}" class="group block h-full w-[84%] shrink-0 snap-start sm:w-[48%] lg:w-[31%]" wire:navigate>
                        <article class="flex h-[24rem] flex-col rounded-3xl border border-zinc-200 bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                            <div class="mb-4 rounded-2xl bg-zinc-100 p-4">
                                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-700">{{ $day['date']->translatedFormat('D d M') }}</p>
                                <p class="mt-2 text-sm font-semibold text-zinc-700">{{ __('Eventos del día') }}</p>
                            </div>
                            <div class="flex flex-1 flex-col space-y-3">
                                @if ($day['events']->isEmpty())
                                    <p class="text-sm font-medium text-zinc-600">{{ __('No hay eventos este día') }}</p>
                                @else
                                    @foreach ($day['events']->take(3) as $event)
                                        <div class="rounded-xl border border-zinc-200 p-3">
                                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-zinc-500">{{ $event->start_at->format('H:i') }}</p>
                                            <p class="mt-1 text-sm font-semibold text-zinc-900">{{ $event->name }}</p>
                                            <p class="mt-1 text-xs text-zinc-600">{{ $event->is_online ? __('Online') : ($event->location ?? __('En tienda')) }}</p>
                                        </div>
                                    @endforeach
                                    @if ($day['events']->count() > 3)
                                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-amber-700">
                                            {{ __(':count eventos más', ['count' => $day['events']->count() - 3]) }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                            <div class="mt-6 flex items-center justify-between text-sm font-semibold">
                                <span class="text-amber-700">{{ __('Ver día en calendario') }}</span>
                                <span aria-hidden="true" class="text-amber-700 transition group-hover:translate-x-1">&rarr;</span>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section id="shop-info" class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-10">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Conócenos') }}</p>
                <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Información de la tienda') }}</h2>
            </div>

            <div class="flex flex-col gap-6">
                <article class="w-full rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-zinc-900">{{ __('Quiénes somos') }}</h3>
                    <p class="mt-4 text-sm leading-relaxed text-zinc-700">
                        {{ __('Somos una tienda especializada en cultura geek, juegos de mesa, TCG y coleccionables. Nuestro enfoque combina comunidad, asesoría cercana y productos seleccionados para cada tipo de jugador.') }}
                    </p>
                </article>

                <article class="w-full rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm">
                    <div class="grid gap-6 md:grid-cols-[minmax(220px,320px)_1fr] md:items-start">
                        <div>
                            @if ($shopMapsEmbedUrl)
                                <div class="aspect-square overflow-hidden rounded-2xl border border-zinc-200 bg-zinc-100">
                                    <iframe
                                        src="{{ $shopMapsEmbedUrl }}"
                                        width="100%"
                                        height="100%"
                                        style="border:0;"
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        title="{{ __('Ubicación en Google Maps') }}"
                                        class="pointer-events-none h-full w-full"
                                    ></iframe>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-xl font-semibold text-zinc-900">{{ __('Dónde encontrarnos') }}</h3>
                            <p class="mt-4 text-sm leading-relaxed text-zinc-700">
                                {{ __('Visítanos en nuestra tienda física para conocer novedades, participar en eventos y recibir recomendaciones personalizadas.') }}
                            </p>
                            <p class="mt-3 text-sm font-medium text-zinc-800">{{ $shopLocationAddress }}</p>
                                {{-- <a
                                    href="{{ $shopMapsHref }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="mt-5 inline-flex items-center text-sm font-semibold text-amber-700 transition hover:text-amber-800"
                                >
                                    {{ __('Ver ubicación en mapa') }}
                                </a> --}}
                        </div>
                    </div>
                </article>

                <article class="w-full rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm">
                    <h3 class="text-xl font-semibold text-zinc-900">{{ __('Contacto') }}</h3>
                    <p class="mt-4 text-sm leading-relaxed text-zinc-700">
                        {{ __('Si necesitas ayuda con un producto o con una reserva, escríbenos y te responderemos lo antes posible.') }}
                    </p>
                    <div class="mt-5 flex flex-col gap-2 text-sm font-semibold">
                        <a href="mailto:{{ $shopContactEmail }}" class="text-amber-700 transition hover:text-amber-800">{{ $shopContactEmail }}</a>
                        <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $shopContactPhone) }}" class="text-amber-700 transition hover:text-amber-800">{{ $shopContactPhone }}</a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const connectSlider = (key, fallbackSelector = null) => {
                const slider = document.querySelector(`[data-slider="${key}"]`)
                    || (fallbackSelector ? document.querySelector(fallbackSelector) : null);
                const prev = document.querySelector(`[data-slider-prev="${key}"]`)
                    || (key === 'featured' ? document.querySelector('[data-slider-prev]') : null);
                const next = document.querySelector(`[data-slider-next="${key}"]`)
                    || (key === 'featured' ? document.querySelector('[data-slider-next]') : null);

                if (!slider || !prev || !next) {
                    return;
                }

                const getStep = () => Math.max(320, Math.floor(slider.clientWidth * 0.9));

                prev.addEventListener('click', () => {
                    slider.scrollBy({ left: -getStep(), behavior: 'smooth' });
                });

                next.addEventListener('click', () => {
                    slider.scrollBy({ left: getStep(), behavior: 'smooth' });
                });
            };

            connectSlider('featured', '[data-featured-slider]');
            connectSlider('events');
        });
    </script>
</x-layouts::shop>
