@php($appName = config('app.name', 'Mikrokosmos'))
@php($shopLocationAddress = config('shop.location.address'))
@php($shopMapsEmbedUrl = config('shop.location.maps_embed_url'))
@php($shopContactEmail = config('shop.contact.email'))
@php($shopContactPhone = config('shop.contact.phone'))

<x-layouts::shop :title="__('Home')">

    {{-- Hero --}}
    <section class="bg-gradient-to-br from-[#fef5f0] via-[#f0eeff] to-[#e5ebff]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-20 lg:flex-row lg:items-center lg:gap-16 lg:px-8 lg:py-28">
            <div class="flex-1 space-y-6">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#8b8fcc]">{{ $appName }}</p>
                <h1 class="text-4xl font-bold leading-tight text-[#1e2e74] sm:text-5xl lg:text-6xl">
                    {{ $tagline }}
                </h1>
                <p class="max-w-xl text-lg text-[#4a4fa8]/70">
                    {{ __('Colecciona, juega y comparte tus pasiones geek con una experiencia curada y humana.') }}
                </p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="#featured" class="rounded-full bg-[#f5a520] px-6 py-3 font-semibold text-[#1e2e74] transition hover:bg-[#ffd978]">
                        {{ __('Ver novedades') }}
                    </a>
                    <a href="{{ route('shop.events.index') }}" class="rounded-full border border-[#1e2e74]/25 px-6 py-3 font-semibold text-[#1e2e74] transition hover:bg-[#1e2e74]/5" wire:navigate>
                        {{ __('Calendario de eventos') }}
                    </a>
                </div>
            </div>

            @if ($sellingPoints->isNotEmpty())
                <div class="w-full max-w-xs rounded-2xl border border-[#dddeff] bg-white/80 p-6 shadow-sm backdrop-blur">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#8b8fcc]">{{ __('Lo que encontrarás') }}</p>
                    <ul class="mt-4 space-y-3">
                        @foreach ($sellingPoints as $point)
                            <li class="flex items-center gap-3 text-sm text-[#1e2e74]">
                                <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-[#f5a520]/15 text-xs font-bold text-[#f5a520]">
                                    {{ $loop->iteration }}
                                </span>
                                {{ $point }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
    </section>

    {{-- Featured products --}}
    @if ($featuredProducts->isNotEmpty())
        <section id="featured" class="bg-white">
            <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
                <div class="mb-8 flex items-end justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#8b8fcc]">{{ __('Novedades') }}</p>
                        <h2 class="text-3xl font-bold text-[#1e2e74]">{{ __('Productos destacados') }}</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-full border border-[#dddeff] bg-white text-[#6b70c4] transition hover:border-[#6b70c4] hover:bg-[#eeeeff]"
                            aria-label="{{ __('Anterior') }}"
                            data-slider-prev="featured"
                        >&larr;</button>
                        <button
                            type="button"
                            class="inline-flex size-9 items-center justify-center rounded-full border border-[#dddeff] bg-white text-[#6b70c4] transition hover:border-[#6b70c4] hover:bg-[#eeeeff]"
                            aria-label="{{ __('Siguiente') }}"
                            data-slider-next="featured"
                        >&rarr;</button>
                    </div>
                </div>

                <div
                    class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                    data-slider="featured"
                >
                    @foreach ($featuredProducts as $product)
                        <a href="{{ route('shop.products.show', $product) }}" class="group block w-[82%] shrink-0 snap-start sm:w-[46%] lg:w-[30%]" wire:navigate>
                            <article class="flex h-full flex-col rounded-2xl border border-[#dddeff] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-[#8b8fcc] hover:shadow-md">
                                <div class="mb-4 aspect-[4/3] w-full overflow-hidden rounded-xl bg-[#f0eeff]">
                                    @if ($product->image_url)
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                    @else
                                        <div class="flex h-full w-full items-center justify-center text-xs font-semibold uppercase tracking-widest text-[#8b8fcc]">
                                            {{ __('Sin imagen') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="flex flex-1 flex-col gap-2">
                                    <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#8b8fcc]">
                                        {{ $product->category?->name ?? __('Colección especial') }}
                                    </p>
                                    <h3 class="text-lg font-bold text-[#1e2e74] transition group-hover:text-[#6b70c4]">{{ $product->name }}</h3>
                                    @if ($product->description)
                                        <p class="text-sm text-[#4a4fa8]/60">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                                    @endif
                                </div>
                                <div class="mt-4 flex items-center justify-between text-sm font-semibold">
                                    <span class="text-[#6b70c4]">{{ __('Ver detalles') }}</span>
                                    <span aria-hidden="true" class="text-[#6b70c4] transition group-hover:translate-x-1">&rarr;</span>
                                </div>
                            </article>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    {{-- Weekly events --}}
    <section id="weekly-events" class="bg-[#f8f7ff]">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-8 flex items-end justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#8b8fcc]">{{ __('Esta semana') }}</p>
                    <h2 class="text-3xl font-bold text-[#1e2e74]">{{ __('Agenda semanal') }}</h2>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        class="inline-flex size-9 items-center justify-center rounded-full border border-[#dddeff] bg-white text-[#6b70c4] transition hover:border-[#6b70c4] hover:bg-[#eeeeff]"
                        aria-label="{{ __('Anterior') }}"
                        data-slider-prev="events"
                    >&larr;</button>
                    <button
                        type="button"
                        class="inline-flex size-9 items-center justify-center rounded-full border border-[#dddeff] bg-white text-[#6b70c4] transition hover:border-[#6b70c4] hover:bg-[#eeeeff]"
                        aria-label="{{ __('Siguiente') }}"
                        data-slider-next="events"
                    >&rarr;</button>
                </div>
            </div>

            <div
                class="flex snap-x snap-mandatory gap-5 overflow-x-auto scroll-smooth pb-2 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden"
                data-slider="events"
            >
                @foreach ($weeklyDays as $day)
                    <a
                        href="{{ route('shop.events.index', ['month' => $day['date']->format('Y-m'), 'day' => $day['date']->toDateString()]) }}"
                        class="group block w-[82%] shrink-0 snap-start sm:w-[46%] lg:w-[30%]"
                        wire:navigate
                    >
                        <article class="flex h-[22rem] flex-col rounded-2xl border border-[#dddeff] bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-[#8b8fcc] hover:shadow-md">
                            <div class="mb-4 rounded-xl bg-[#1e2e74] px-4 py-3">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#f5a520]">{{ $day['date']->translatedFormat('D') }}</p>
                                <p class="text-2xl font-bold text-white">{{ $day['date']->translatedFormat('j') }}</p>
                                <p class="text-xs text-white/50">{{ $day['date']->translatedFormat('F') }}</p>
                            </div>
                            <div class="flex flex-1 flex-col gap-2">
                                @if ($day['events']->isEmpty())
                                    <p class="text-sm text-[#8b8fcc]/60">{{ __('Sin eventos este día') }}</p>
                                @else
                                    @foreach ($day['events']->take(3) as $event)
                                        <div class="rounded-lg border border-[#dddeff] bg-[#f8f7ff] p-3">
                                            <p class="text-xs font-semibold text-[#8b8fcc]">{{ $event->start_at->format('H:i') }}</p>
                                            <p class="mt-0.5 text-sm font-semibold text-[#1e2e74]">{{ $event->name }}</p>
                                            <p class="mt-0.5 text-xs text-[#4a4fa8]/60">{{ $event->is_online ? __('Online') : ($event->location ?? __('En tienda')) }}</p>
                                        </div>
                                    @endforeach
                                    @if ($day['events']->count() > 3)
                                        <p class="text-xs font-semibold text-[#8b8fcc]">
                                            +{{ $day['events']->count() - 3 }} {{ __('más') }}
                                        </p>
                                    @endif
                                @endif
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm font-semibold">
                                <span class="text-[#6b70c4]">{{ __('Ver en calendario') }}</span>
                                <span aria-hidden="true" class="text-[#6b70c4] transition group-hover:translate-x-1">&rarr;</span>
                            </div>
                        </article>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Shop info --}}
    <section class="bg-[#1e2e74]">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-10">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#f5a520]">{{ __('Conócenos') }}</p>
                <h2 class="text-3xl font-bold text-white">{{ __('La tienda') }}</h2>
            </div>

            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-bold text-white">{{ __('Quiénes somos') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-white/60">
                        {{ __('Somos una tienda especializada en cultura geek, juegos de mesa, TCG y coleccionables. Nuestro enfoque combina comunidad, asesoría cercana y productos seleccionados para cada tipo de jugador.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-bold text-white">{{ __('Dónde encontrarnos') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-white/60">
                        {{ __('Visítanos en nuestra tienda física para conocer novedades, participar en eventos y recibir recomendaciones personalizadas.') }}
                    </p>
                    @if ($shopLocationAddress)
                        <p class="mt-3 text-sm font-semibold text-[#f5a520]">{{ $shopLocationAddress }}</p>
                    @endif
                    @if ($shopMapsEmbedUrl)
                        <div class="mt-4 aspect-video overflow-hidden rounded-xl border border-white/10">
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

                <div class="rounded-2xl border border-white/10 bg-white/5 p-6">
                    <h3 class="text-lg font-bold text-white">{{ __('Contacto') }}</h3>
                    <p class="mt-3 text-sm leading-relaxed text-white/60">
                        {{ __('Si necesitas ayuda con un producto o con una reserva, escríbenos y te responderemos lo antes posible.') }}
                    </p>
                    <div class="mt-4 flex flex-col gap-2 text-sm font-semibold">
                        @if ($shopContactEmail)
                            <a href="mailto:{{ $shopContactEmail }}" class="text-[#f5a520] transition hover:text-[#ffd978]">{{ $shopContactEmail }}</a>
                        @endif
                        @if ($shopContactPhone)
                            <a href="tel:{{ preg_replace('/[^0-9+]/', '', (string) $shopContactPhone) }}" class="text-[#f5a520] transition hover:text-[#ffd978]">{{ $shopContactPhone }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const connectSlider = (key) => {
                const slider = document.querySelector(`[data-slider="${key}"]`);
                const prev = document.querySelector(`[data-slider-prev="${key}"]`);
                const next = document.querySelector(`[data-slider-next="${key}"]`);
                if (!slider || !prev || !next) return;
                const getStep = () => Math.max(300, Math.floor(slider.clientWidth * 0.88));
                prev.addEventListener('click', () => slider.scrollBy({ left: -getStep(), behavior: 'smooth' }));
                next.addEventListener('click', () => slider.scrollBy({ left: getStep(), behavior: 'smooth' }));
            };
            connectSlider('featured');
            connectSlider('events');
        });
    </script>

</x-layouts::shop>
