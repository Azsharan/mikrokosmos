<x-layouts::shop :title="$product->name">

    <section class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-12 lg:px-8">

            <nav class="mb-8 flex items-center gap-2 text-sm text-[#4a4fa8]">
                <a href="{{ route('home') }}" class="transition hover:text-[#6b70c4]" wire:navigate>{{ __('Inicio') }}</a>
                <span class="text-[#dddeff]">/</span>
                @if ($product->category)
                    <a href="{{ route('shop.categories.show', $product->category) }}" class="transition hover:text-[#6b70c4]" wire:navigate>{{ $product->category->name }}</a>
                    <span class="text-[#dddeff]">/</span>
                @endif
                <span class="font-semibold text-[#1e2e74]">{{ $product->name }}</span>
            </nav>

            <div class="flex flex-col gap-10 lg:flex-row lg:items-start lg:gap-14">

                <div class="w-full lg:w-1/2">
                    @if ($product->image_url)
                        <div class="overflow-hidden rounded-2xl border border-[#dddeff] bg-[#f0eeff]">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full object-cover">
                        </div>
                    @else
                        <div class="flex aspect-square w-full items-center justify-center rounded-2xl border border-[#dddeff] bg-[#f0eeff] text-sm font-semibold uppercase tracking-widest text-[#6b70c4]">
                            {{ __('Sin imagen') }}
                        </div>
                    @endif
                </div>

                <div class="flex-1 space-y-6">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#6b70c4]">{{ $product->category?->name ?? __('Colección especial') }}</p>
                        <h1 class="mt-2 text-3xl font-bold text-[#1e2e74] lg:text-4xl">{{ $product->name }}</h1>
                    </div>

                    @if ($product->description)
                        <p class="text-base leading-relaxed text-[#4a4fa8]">{{ $product->description }}</p>
                    @endif

                    <div class="rounded-2xl border border-[#dddeff] bg-[#f8f7ff] p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#6b70c4]">{{ __('Precio') }}</p>
                        <p class="mt-1 text-4xl font-bold text-[#1e2e74]">{{ number_format((float) $product->price, 2) }} €</p>
                    </div>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('shop.categories.index') }}" class="rounded-full border border-[#dddeff] px-5 py-2.5 text-sm font-semibold text-[#4a4fa8] transition hover:border-[#6b70c4] hover:bg-[#f0eeff]" wire:navigate>
                            {{ __('Ver más colecciones') }}
                        </a>
                        <a href="{{ route('home') }}#featured" class="rounded-full bg-[#1e2e74] px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-[#6b70c4]">
                            {{ __('Ver otros productos') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="bg-[#f8f7ff]">
            <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
                <div class="mb-8">
                    <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#6b70c4]">{{ __('También podría interesarte') }}</p>
                    <h2 class="text-2xl font-bold text-[#1e2e74]">{{ __('Productos relacionados') }}</h2>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <a href="{{ route('shop.products.show', $related) }}" class="group block rounded-2xl border border-[#dddeff] bg-white p-4 transition hover:-translate-y-1 hover:border-[#6b70c4] hover:shadow-sm" wire:navigate>
                            @if ($related->image_url)
                                <div class="mb-3 overflow-hidden rounded-xl bg-[#f0eeff]">
                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="h-36 w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                </div>
                            @endif
                            <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#6b70c4]">{{ $related->category?->name ?? __('Colección especial') }}</p>
                            <h3 class="mt-1 font-bold text-[#1e2e74] transition group-hover:text-[#6b70c4]">{{ $related->name }}</h3>
                            <p class="mt-3 text-sm font-bold text-[#1e2e74]">{{ number_format((float) $related->price, 2) }} €</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

</x-layouts::shop>
