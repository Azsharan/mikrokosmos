@php($appName = config('app.name', 'Mikrokosmos'))

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
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Novedades y best sellers') }}</p>
                        <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Productos destacados') }}</h2>
                    </div>
                    <p class="max-w-2xl text-sm text-zinc-600">
                        {{ __('Disponibilidad limitada: aparta tus piezas antes de que vuelen del inventario.') }}
                    </p>
                </div>

                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($featuredProducts as $product)
                        <a href="{{ route('shop.products.show', $product) }}" class="group block h-full" wire:navigate>
                            <article class="flex h-full flex-col justify-between rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-200 hover:shadow-lg">
                                @if ($product->image_url)
                                    <div class="mb-4 overflow-hidden rounded-2xl bg-zinc-100">
                                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                    </div>
                                @endif
                                <div class="space-y-4">
                                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">
                                        {{ $product->category?->name ?? __('Colección especial') }}
                                    </p>
                                    <h3 class="text-2xl font-semibold text-zinc-900 group-hover:text-amber-700">{{ $product->name }}</h3>
                                    @if ($product->description)
                                        <p class="text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit($product->description, 140) }}</p>
                                    @endif
                                </div>
                                <div class="mt-6 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Precio') }}</p>
                                        <p class="text-3xl font-semibold text-zinc-900">${{ number_format((float) $product->price, 2) }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Disponibles') }}</p>
                                        <p class="text-2xl font-semibold text-emerald-600">{{ $product->stock }}</p>
                                    </div>
                                </div>
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
</x-layouts::shop>
