<x-layouts::shop :title="__('Crear cuenta')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-10 px-4 py-16 lg:px-8">
        <div class="max-w-2xl space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Clientes Mikrokosmos') }}</p>
            <h1 class="text-3xl font-semibold text-zinc-900">{{ __('Crea tu cuenta en minutos') }}</h1>
            <p class="text-sm text-zinc-600">
                {{ __('Recibe beneficios como apartados anticipados, acceso prioritario a torneos y recompensas personalizadas.') }}
            </p>
        </div>

        <div class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
            <form method="POST" action="{{ route('shop.register.store') }}" class="grid gap-6 md:grid-cols-2">
                @csrf

                <div class="md:col-span-2 space-y-2">
                    <label for="name" class="text-sm font-semibold text-zinc-700">{{ __('Nombre completo') }}</label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        required
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="{{ __('Tu nombre') }}"
                    />
                    @error('name')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-zinc-700">{{ __('Correo electrónico') }}</label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="email@example.com"
                    />
                    @error('email')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="phone" class="text-sm font-semibold text-zinc-700">{{ __('Teléfono de contacto') }}</label>
                    <input
                        id="phone"
                        name="phone"
                        type="text"
                        value="{{ old('phone') }}"
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="+52 55 0000 0000"
                    />
                    @error('phone')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="md:col-span-2 space-y-2">
                    <label for="address" class="text-sm font-semibold text-zinc-700">{{ __('Dirección (opcional)') }}</label>
                    <textarea
                        id="address"
                        name="address"
                        rows="3"
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="{{ __('Dónde prefieres recibir tus compras') }}"
                    >{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-zinc-700">{{ __('Contraseña') }}</label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="{{ __('Crea una contraseña segura') }}"
                    />
                    @error('password')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password_confirmation" class="text-sm font-semibold text-zinc-700">{{ __('Confirma tu contraseña') }}</label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        required
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="{{ __('Repite la contraseña') }}"
                    />
                </div>

                <div class="md:col-span-2 flex items-start gap-3 rounded-2xl border border-zinc-200 bg-zinc-50 px-4 py-3 text-sm text-zinc-600">
                    <input
                        id="newsletter_opt_in"
                        name="newsletter_opt_in"
                        type="checkbox"
                        value="1"
                        class="mt-1 h-4 w-4 rounded border-zinc-300 text-zinc-900 focus:ring-zinc-900"
                        {{ old('newsletter_opt_in') ? 'checked' : '' }}
                    />
                    <label for="newsletter_opt_in" class="text-sm leading-tight text-zinc-700">
                        {{ __('Deseo recibir novedades, preventas y promociones por correo.') }}
                    </label>
                </div>

                <div class="md:col-span-2">
                    <button
                        type="submit"
                        class="w-full rounded-full bg-zinc-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-zinc-800"
                    >
                        {{ __('Crear cuenta') }}
                    </button>
                    <p class="mt-4 text-center text-sm text-zinc-600">
                        {{ __('¿Ya tienes cuenta?') }}
                        <a href="{{ route('shop.login') }}" class="font-semibold text-zinc-900 underline-offset-4 hover:underline">
                            {{ __('Inicia sesión aquí') }}
                        </a>
                    </p>
                </div>
            </form>
        </div>
    </section>
</x-layouts::shop>
