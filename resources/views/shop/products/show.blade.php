@php($appName = config('app.name', 'Mikrokosmos'))
<x-layouts::shop :title="$product->name">
    <section class="bg-gradient-to-br from-[#150a24] via-[#2d154c] to-[#0f0617] text-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-8 px-4 py-12 lg:flex-row lg:items-center lg:gap-16 lg:px-8">
            <div class="flex-1 space-y-6">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#f6d98f]">
                    {{ $product->category?->name ?? __('Colección especial') }}
                </p>
                <h1 class="text-4xl font-semibold leading-tight">{{ $product->name }}</h1>
                @if ($product->description)
                    <p class="text-base text-white/70">{{ $product->description }}</p>
                @endif
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-2xl bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Precio') }}</p>
                        <p class="mt-2 text-4xl font-semibold text-[#ffe599]">${{ number_format((float) $product->price, 2) }}</p>
                    </div>
                    {{-- <div class="rounded-2xl bg-white/5 p-4">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Disponibilidad') }}</p>
                        <p class="mt-2 text-4xl font-semibold text-emerald-300">
                            {{ $product->stock > 0 ? $product->stock : __('Agotado') }}
                        </p>
                    </div> --}}
                </div>
                <div class="flex flex-wrap gap-4 text-sm">
                    {{-- <a href="#details" class="rounded-full bg-[#f6d98f] px-6 py-2 font-semibold text-[#402f00] transition hover:bg-[#ffd96e]">
                        {{ __('Ver especificaciones') }}
                    </a> --}}
                    {{-- <a href="{{ route('home') }}#featured" class="rounded-full border border-white/30 px-6 py-2 font-semibold text-white transition hover:bg-white/10">
                        {{ __('Volver a destacados') }}
                    </a> --}}
                </div>
            </div>

            <div class="flex w-full max-w-md flex-col gap-6">
                @if ($product->image_url)
                    <div class="overflow-hidden rounded-3xl border border-white/20 bg-white/5 p-2 shadow-2xl backdrop-blur">
                        <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="h-full w-full rounded-2xl object-cover">
                    </div>
                @endif
                {{-- <div class="flex flex-col gap-4 rounded-3xl border border-white/10 bg-white/5 p-6 text-sm text-white/80">
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Ficha rápida') }}</p>
                    <dl class="grid gap-4">
                        <div>
                            <dt class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Código') }}</dt>
                            <dd class="text-lg font-semibold text-white">{{ $product->barcode ?? __('No asignado') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Estado') }}</dt>
                            <dd class="text-lg font-semibold text-emerald-300">{{ $product->is_active ? __('Disponible') : __('No disponible') }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Actualizado') }}</dt>
                            <dd class="text-lg font-semibold text-white">{{ optional($product->updated_at)->format('F j, Y') }}</dd>
                        </div>
                    </dl>

                    @if ($product->tags)
                        <div class="mt-2">
                            <p class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Etiquetas') }}</p>
                            <div class="mt-2 flex flex-wrap gap-2">
                                @foreach (explode(',', $product->tags) as $tag)
                                    <span class="rounded-full bg-white/10 px-3 py-1 text-xs font-semibold text-white/80">{{ trim($tag) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div> --}}
            </div>
        </div>
    </section>

    <section id="details" class="bg-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-12 px-4 py-16 lg:px-8 lg:flex-row">
            {{-- <div class="flex-1 space-y-6">
                <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Detalles del producto') }}</h2>
                <div class="space-y-4 text-sm leading-relaxed text-zinc-700">
                    @if ($product->description)
                        <p>{{ $product->description }}</p>
                    @else
                        <p>{{ __('Este artículo aún no tiene una descripción extensa, pero puedes consultarnos cualquier duda en tienda.') }}</p>
                    @endif
                </div>
                <div class="grid gap-6 sm:grid-cols-2">
                    <div class="rounded-2xl border border-zinc-200/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Colección') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900">{{ $product->category?->name ?? __('Sin categoría') }}</p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200/70 p-5">
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Precio público') }}</p>
                        <p class="mt-2 text-2xl font-semibold text-zinc-900">${{ number_format((float) $product->price, 2) }}</p>
                    </div>
                </div>
            </div> --}}

            {{-- <div id="reservation" class="w-full max-w-md space-y-6 rounded-3xl border border-zinc-200/70 p-6">
                <h3 class="text-xl font-semibold text-zinc-900">{{ __('Información adicional') }}</h3>
                <dl class="space-y-4 text-sm text-zinc-700">
                    <div class="flex justify-between">
                        <dt>{{ __('SKU / Código de barras') }}</dt>
                        <dd class="font-semibold">{{ $product->barcode ?? __('No disponible') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Inventario en tienda') }}</dt>
                        <dd class="font-semibold">{{ $product->stock }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Actualizado') }}</dt>
                        <dd class="font-semibold">{{ optional($product->updated_at)->diffForHumans() }}</dd>
                    </div>
                </dl> --}}

                {{-- <div class="rounded-2xl bg-gradient-to-br from-amber-100 to-emerald-100 p-5 text-sm text-zinc-800">
                    <p class="font-semibold">{{ __('¿Listo para llevarlo a tu colección?') }}</p>
                    <p class="mt-2 text-zinc-700">{{ __('Visítanos en tienda física o contáctanos.') }}</p>
                </div> --}}

                {{-- <div class="space-y-4 rounded-2xl border border-zinc-200/70 p-5">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Reservas temporales') }}</p>
                        <p class="mt-2 text-sm text-zinc-600">{{ __('La reserva en línea está deshabilitada temporalmente. Puedes visitarnos en tienda o escribirnos para consultar disponibilidad.') }}</p>
                    </div>

                    <p class="rounded-lg bg-amber-100 px-3 py-2 text-xs font-semibold uppercase tracking-wide text-amber-700">
                        {{ __('Reserva en línea temporalmente no disponible') }}
                    </p>
                </div> --}}
            {{-- </div> --}}
        </div>
    </section>

    @if ($relatedProducts->isNotEmpty())
        <section class="bg-gradient-to-r from-white to-[#f7f0ff]">
            <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
                <div class="mb-6 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('También podría interesarte') }}</p>
                        <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Productos relacionados') }}</h2>
                    </div>
                    <a href="{{ route('home') }}#featured" class="text-sm font-semibold text-[#5a38a6] hover:text-[#7a5fd3]">
                        {{ __('Ver todos los destacados') }}
                    </a>
                </div>
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <a href="{{ route('shop.products.show', $related) }}" class="group block rounded-2xl border border-zinc-200 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:border-amber-200" wire:navigate>
                            @if ($related->image_url)
                                <div class="mb-4 overflow-hidden rounded-xl bg-zinc-50">
                                    <img src="{{ $related->image_url }}" alt="{{ $related->name }}" class="h-40 w-full object-cover transition duration-500 group-hover:scale-[1.03]">
                                </div>
                            @endif
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ $related->category?->name ?? __('Colección especial') }}</p>
                            <h3 class="mt-3 text-lg font-semibold text-zinc-900 group-hover:text-amber-600">{{ $related->name }}</h3>
                            <p class="mt-2 text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit($related->description, 90) }}</p>
                            <div class="mt-4 text-2xl font-semibold text-zinc-900">
                                ${{ number_format((float) $related->price, 2) }}
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
</x-layouts::shop>
