<x-layouts::shop :title="$category->name">

    <section class="bg-[#6b70c4]">
        <div class="mx-auto w-full max-w-6xl px-4 py-14 lg:px-8">
            <nav class="mb-4 flex items-center gap-2 text-xs text-white/60">
                <a href="{{ route('shop.categories.index') }}" class="transition hover:text-white" wire:navigate>{{ __('Colecciones') }}</a>
                <span>/</span>
                <span class="text-white">{{ $category->name }}</span>
            </nav>
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-white">{{ __('Colección') }}</p>
            <h1 class="mt-2 text-4xl font-bold text-white">{{ $category->name }}</h1>
            @if ($category->description)
                <p class="mt-3 max-w-xl text-base text-white/80">{{ $category->description }}</p>
            @endif
        </div>
    </section>

    <section class="bg-[#f8f7ff]">
        <div class="mx-auto w-full max-w-6xl px-4 py-14 lg:px-8">
            @if ($products->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#dddeff] bg-white p-12 text-center text-[#4a4fa8]">
                    <p>{{ __('No hay productos activos en esta colección, vuelve pronto.') }}</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <a href="{{ route('shop.products.show', $product) }}" class="group block rounded-2xl border border-[#dddeff] bg-white shadow-sm transition hover:-translate-y-1 hover:border-[#6b70c4] hover:shadow-md" wire:navigate>
                            @if ($product->image_url)
                                <div class="overflow-hidden rounded-t-2xl bg-[#f0eeff]">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-52 w-full object-cover transition duration-500 group-hover:scale-[1.04]">
                                </div>
                            @endif
                            <div class="p-5">
                                <h3 class="font-bold text-[#1e2e74] transition group-hover:text-[#6b70c4]">{{ $product->name }}</h3>
                                @if ($product->description)
                                    <p class="mt-1 text-sm text-[#4a4fa8]">{{ \Illuminate\Support\Str::limit($product->description, 100) }}</p>
                                @endif
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-xl font-bold text-[#1e2e74]">{{ number_format((float) $product->price, 2) }} €</span>
                                    <span class="text-sm font-semibold text-[#6b70c4] transition group-hover:translate-x-1">&rarr;</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layouts::shop>
