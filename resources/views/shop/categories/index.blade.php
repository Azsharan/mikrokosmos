@php($appName = config('app.name', 'Mikrokosmos'))

<x-layouts::shop :title="__('Colecciones')">
    <section class="bg-gradient-to-br from-[#200a3b] via-[#3c1768] to-[#0c0413] text-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 lg:flex-row lg:items-center lg:px-8">
            <div class="flex-1 space-y-5">
                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#f6d98f]">{{ __('Explora colecciones') }}</p>
                <h1 class="text-4xl font-semibold leading-tight">{{ __('Colecciones disponibles') }}</h1>
                <p class="text-base text-white/70">
                    {{ __('Navega por nuestras líneas de producto y descubre piezas por temática, edición y estilo.') }}
                </p>
                <div class="flex flex-wrap gap-3 text-sm">
                    <a href="{{ route('home') }}#featured" class="rounded-full bg-[#f6d98f] px-6 py-2 font-semibold text-[#402f00] transition hover:bg-[#ffd96e]">
                        {{ __('Ver productos destacados') }}
                    </a>
                </div>
            </div>
            <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-6 text-sm text-white/80">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Resumen') }}</p>
                <dl class="mt-4 space-y-4">
                    <div class="flex justify-between">
                        <dt>{{ __('Colecciones activas') }}</dt>
                        <dd class="font-semibold text-white">{{ $categories->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt>{{ __('Disponibles hoy') }}</dt>
                        <dd class="font-semibold text-white">{{ $categories->sum('products_count') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-[0.3em] text-white/60">{{ __('Origen') }}</dt>
                        <dd class="mt-1 text-lg font-semibold text-white">{{ $appName }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-16 lg:px-8">
            <div class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Colecciones') }}</p>
                <h2 class="text-3xl font-semibold text-zinc-900">{{ __('Descubre por categoría') }}</h2>
            </div>

            @if ($categories->isEmpty())
                <div class="rounded-3xl border border-dashed border-zinc-200/70 bg-zinc-50 p-10 text-center text-zinc-500">
                    <p>{{ __('No hay colecciones activas disponibles en este momento.') }}</p>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
                    @foreach ($categories as $category)
                        <a href="{{ route('shop.categories.show', $category) }}" class="group flex h-full flex-col gap-4 rounded-3xl border border-zinc-200 bg-gradient-to-br from-white to-zinc-50 p-6 shadow-sm ring-1 ring-transparent transition hover:-translate-y-1 hover:border-amber-200" wire:navigate>
                            <div class="flex items-center justify-between text-xs font-semibold uppercase tracking-[0.3em] text-zinc-400">
                                <span>{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span>
                                <span>{{ __('Colección') }}</span>
                            </div>
                            <div class="space-y-3">
                                <h3 class="text-2xl font-semibold text-zinc-900 group-hover:text-amber-700">{{ $category->name }}</h3>
                                @if ($category->description)
                                    <p class="text-sm text-zinc-600">{{ \Illuminate\Support\Str::limit($category->description, 130) }}</p>
                                @else
                                    <p class="text-sm text-zinc-600">{{ __('Una selección curada de lanzamientos, reposiciones y ediciones especiales.') }}</p>
                                @endif
                            </div>
                            <div class="mt-auto space-y-2">
                                <div class="flex items-center justify-between text-sm text-zinc-700">
                                    <span>{{ __('Productos activos') }}</span>
                                    <span class="font-semibold">{{ $category->products_count }}</span>
                                </div>
                                <div class="flex items-center justify-between text-sm font-semibold text-zinc-700">
                                    <span>{{ __('Ver colección') }}</span>
                                    <span aria-hidden="true" class="transition group-hover:translate-x-1">&rarr;</span>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
</x-layouts::shop>
