<x-layouts::shop :title="__('Colecciones')">

    <section class="bg-[#1c0f3f]">
        <div class="mx-auto w-full max-w-6xl px-4 py-14 lg:px-8">
            <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#e6c45c]">{{ __('Catálogo') }}</p>
            <h1 class="mt-2 text-4xl font-bold text-white">{{ __('Colecciones') }}</h1>
            <p class="mt-3 max-w-xl text-base text-white/60">
                {{ __('Navega por nuestras líneas de producto y descubre piezas por temática, edición y estilo.') }}
            </p>
        </div>
    </section>

    <section class="bg-[#faf7ff]">
        <div class="mx-auto w-full max-w-6xl px-4 py-14 lg:px-8">
            @if ($categories->isEmpty())
                <div class="rounded-2xl border border-dashed border-[#d5c8f5] bg-white p-12 text-center text-[#7b5fd0]">
                    <p>{{ __('No hay colecciones disponibles en este momento.') }}</p>
                </div>
            @else
                <div class="grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.categories.show', $category) }}" class="group flex flex-col gap-4 rounded-2xl border border-[#e0d5f5] bg-white p-6 shadow-sm transition hover:-translate-y-1 hover:border-[#7b5fd0] hover:shadow-md" wire:navigate>
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-[0.25em] text-[#b69ef7]">{{ __('Colección') }}</span>
                                <span class="text-xs font-bold text-[#b69ef7]">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex-1 space-y-2">
                                <h2 class="text-xl font-bold text-[#1c0f3f] transition group-hover:text-[#5a38a6]">{{ $category->name }}</h2>
                                <p class="text-sm text-[#4b2d7f]/60">
                                    {{ $category->description ? \Illuminate\Support\Str::limit($category->description, 120) : __('Una selección curada de lanzamientos, reposiciones y ediciones especiales.') }}
                                </p>
                            </div>
                            <div class="flex items-center justify-between border-t border-[#f0eaff] pt-4 text-sm">
                                <span class="text-[#7b5fd0]">{{ $category->products_count }} {{ __('productos') }}</span>
                                <span class="font-semibold text-[#5a38a6] transition group-hover:translate-x-1">&rarr;</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

</x-layouts::shop>
