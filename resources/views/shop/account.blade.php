<x-layouts::shop :title="__('Mi cuenta')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-8 px-4 py-16 lg:px-8">
        <div class="space-y-2">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Centro de clientes') }}</p>
            <h1 class="text-3xl font-semibold text-zinc-900">{{ __('Hola, :name', ['name' => $user->name]) }}</h1>
            <p class="text-sm text-zinc-600">
                {{ __('Desde aquí podrás revisar tus datos de contacto y enterarte de los próximos lanzamientos y eventos.') }}
            </p>
            @if (session('account_status'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('account_status') }}
                </div>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <article class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Datos de contacto') }}</p>
                <form method="POST" action="{{ route('shop.account.update') }}" class="mt-4 space-y-4">
                    @csrf
                    @method('PUT')
                    <div class="space-y-2">
                        <label for="account-name" class="text-sm font-semibold text-zinc-700">{{ __('Nombre completo') }}</label>
                        <input
                            id="account-name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        />
                        @error('name')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="account-email" class="text-sm font-semibold text-zinc-700">{{ __('Correo electrónico') }}</label>
                        <input
                            id="account-email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        />
                        @error('email')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="account-phone" class="text-sm font-semibold text-zinc-700">{{ __('Teléfono') }}</label>
                        <input
                            id="account-phone"
                            name="phone"
                            type="text"
                            value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        />
                        @error('phone')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="space-y-2">
                        <label for="account-address" class="text-sm font-semibold text-zinc-700">{{ __('Dirección') }}</label>
                        <textarea
                            id="account-address"
                            name="address"
                            rows="3"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        >{{ old('address', $user->address) }}</textarea>
                        @error('address')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex items-start gap-3 rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600">
                        <input
                            id="account-newsletter"
                            name="newsletter_opt_in"
                            type="checkbox"
                            value="1"
                            class="mt-1 h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                            {{ old('newsletter_opt_in', $user->newsletter_opt_in) ? 'checked' : '' }}
                        />
                        <label for="account-newsletter" class="text-sm leading-tight text-zinc-700">
                            {{ __('Quiero recibir noticias, preventas y promociones por correo.') }}
                        </label>
                    </div>
                    <div>
                        <button
                            type="submit"
                            class="w-full rounded-full bg-zinc-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-zinc-800"
                        >
                            {{ __('Guardar cambios') }}
                        </button>
                    </div>
                </form>
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
