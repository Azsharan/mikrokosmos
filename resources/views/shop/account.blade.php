<x-layouts::shop :title="__('Mi cuenta')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-16 lg:px-8">
        <div class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Centro de clientes') }}</p>
            <h1 class="text-3xl font-semibold text-zinc-900">{{ __('Hola, :name', ['name' => $user->name]) }}</h1>
            <p class="text-sm text-zinc-600">
                {{ __('Desde aquí podrás revisar tus datos de contacto y enterarte de los próximos lanzamientos y eventos.') }}
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Datos de contacto') }}</p>
                <ul class="mt-4 space-y-3 text-sm text-zinc-700">
                    <li>
                        <span class="font-semibold text-zinc-500">{{ __('Nombre') }}:</span>
                        <span class="ms-1">{{ $user->name }}</span>
                    </li>
                    <li>
                        <span class="font-semibold text-zinc-500">{{ __('Correo') }}:</span>
                        <span class="ms-1">{{ $user->email }}</span>
                    </li>
                    @if ($user->phone)
                        <li>
                            <span class="font-semibold text-zinc-500">{{ __('Teléfono') }}:</span>
                            <span class="ms-1">{{ $user->phone }}</span>
                        </li>
                    @endif
                    @if ($user->address)
                        <li>
                            <span class="font-semibold text-zinc-500">{{ __('Dirección') }}:</span>
                            <span class="ms-1">{{ $user->address }}</span>
                        </li>
                    @endif
                </ul>
            </article>

            <article class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Próximamente') }}</p>
                <p class="mt-4 text-sm text-zinc-600">
                    {{ __('Estamos preparando el historial de pedidos, recompensas y la zona de torneos para que tengas todo en un solo lugar.') }}
                </p>
                <p class="mt-2 text-sm text-zinc-600">
                    {{ __('Mientras tanto, si necesitas asistencia escríbenos a hola@mikrokosmos.mx') }}
                </p>
            </article>
        </div>
    </section>
</x-layouts::shop>
