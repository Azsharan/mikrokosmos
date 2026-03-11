@php($appName = config('app.name', 'Mikrokosmos'))

<x-layouts::shop :title="$category->name">
    <section class="bg-gradient-to-br from-[#200a3b] via-[#3c1768] to-[#0c0413] text-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 lg:flex-row lg:items-center lg:gap-16 lg:px-8">
            <div class="flex-1 space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#f6d98f]">{{ __('Colección de productos') }}</p>
                <h1 class="text-4xl font-semibold leading-tight">{{ $category->name }}</h1>
                <p class="text-base text-white/70">
                    {{ $category->description ?: __('Una selección especial de productos dentro de esta categoría.') }}
                </p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="{{ route('shop.categories.index') }}" class="rounded-full border border-white/30 px-6 py-2 font-semibold text-white transition hover:bg-white/10">
                        {{ __('Ver todas las colecciones') }}
                    </a>
                    <a href="{{ route('home') }}#featured" class="rounded-full bg-[#f6d98f] px-6 py-2 font-semibold text-[#402f00] transition hover:bg-[#ffd96e]">
                        {{ __('Explorar destacados') }}
                    </a>
                </div>
            </div>
            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-6 text-sm text-white/80">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Resumen') }}</p>
                <dl class="mt-4 space-y-4">
                    <div class="flex justify-between">
                        <dt>{{ __('Productos activos') }}</dt>
                        <dd class="font-semibold text-white">{{ $products->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Actualizado') }}</dt>
                        <dd class="font-semibold text-white">{{ optional($category->updated_at)->diffForHumans() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Colección') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-white">{{ $appName }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Inventario') }}</p>
                <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Productos disponibles') }}</h2>
            </div>

            @if ($products->isEmpty())
                <div class="rounded-3xl border border-dashed border-zinc-200/70 bg-zinc-50 p-10 text-center text-zinc-500">
                    <p>{{ __('Actualmente no hay productos activos en esta categoría, vuelve pronto para nuevas adiciones.') }}</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($products as $product)
                        <a href="{{ route('shop.products.show', $product) }}" class="group block rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm transition hover:-translate-y-1 hover:border-amber-200" wire:navigate>
                            @if ($product->image_url)
                                <div class="mb-4 overflow-hidden rounded-2xl bg-zinc-100">
                                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-48 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                </div>
                            @endif
                            <div class="space-y-3">
                                <h3 class="text-2xl font-semibold text-zinc-900 group-hover:text-amber-700">{{ $product->name }}</h3>
                                @if ($product->description)
                                    <p class="text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit($product->description, 130) }}</p>
                                @endif
                            </div>
                            <div class="mt-6 flex items-center justify-between text-sm font-semibold text-zinc-700">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.3em] text-zinc-500">{{ __('Precio') }}</p>
                                    <p class="text-2xl text-zinc-900">${{ number_format((float) $product->price, 2) }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs uppercase tracking-[0.3em] text-zinc-500">{{ __('Stock') }}</p>
                                    <p class="text-2xl text-emerald-600">{{ $product->stock }}</p>
                                </div>
                            </div>
                            <div class="mt-4 flex items-center justify-between text-sm font-semibold text-amber-700">
                                <span>{{ __('Ver producto') }}</span>
                                <span aria-hidden="true" class="transition group-hover:translate-x-1">&rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts::shop>
