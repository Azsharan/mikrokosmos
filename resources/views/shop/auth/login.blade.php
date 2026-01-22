<x-layouts::shop :title="__('Iniciar sesión')">
    <section class="mx-auto flex w-full max-w-4xl flex-col gap-8 px-4 py-16 lg:flex-row lg:px-8">
        <div class="flex-1 space-y-4">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Clientes Mikrokosmos') }}</p>
            <h1 class="text-3xl font-semibold text-zinc-900">{{ __('Ingresa a tu cuenta') }}</h1>
            <p class="text-sm text-zinc-600">
                {{ __('Administra tus pedidos, reserva eventos y recibe avisos antes de los lanzamientos exclusivos.') }}
            </p>
        </div>

        <div class="flex-1 rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
            <form method="POST" action="{{ route('shop.login.store') }}" class="space-y-6">
                @csrf

                <div class="space-y-2">
                    <label for="email" class="text-sm font-semibold text-zinc-700">{{ __('Correo electrónico') }}</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        autofocus
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="email@example.com"
                    />
                    @error('email')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="password" class="text-sm font-semibold text-zinc-700">{{ __('Contraseña') }}</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        class="w-full rounded-2xl border border-zinc-200 px-4 py-3 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                        placeholder="{{ __('Tu contraseña segura') }}"
                    />
                    @error('password')
                        <p class="text-sm text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <button
                    type="submit"
                    class="w-full rounded-full bg-zinc-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-zinc-800"
                >
                    {{ __('Iniciar sesión') }}
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-zinc-600">
                {{ __('¿Todavía no tienes cuenta?') }}
                <a href="{{ route('shop.register') }}" class="font-semibold text-zinc-900 underline-offset-4 hover:underline">
                    {{ __('Regístrate aquí') }}
                </a>
            </p>
        </div>
    </section>
</x-layouts::shop>
