@php($defaultStart = now()->addDay()->setHour(12)->minute(0)->second(0))
@php($statusClasses = [
    \App\Models\TableReservation::STATUS_PENDING => 'bg-amber-100 text-amber-800',
    \App\Models\TableReservation::STATUS_CONFIRMED => 'bg-emerald-100 text-emerald-800',
    \App\Models\TableReservation::STATUS_CANCELLED => 'bg-rose-100 text-rose-700',
])

<x-layouts::shop :title="__('Reservar mesa')">
    <section class="mx-auto flex w-full max-w-5xl flex-col gap-10 px-4 py-16 lg:px-8">
        <div class="space-y-3">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Zona de juego') }}</p>
            <h1 class="text-3xl font-semibold text-zinc-900">{{ __('Reserva tu mesa') }}</h1>
            <p class="text-sm text-zinc-600">
                {{ __('Tenemos 4 mesas disponibles para partidas casuales de hasta 6 personas. Cada bloque cubre :hours horas continuas.', ['hours' => $sessionDurationMinutes / 60]) }}
            </p>
        </div>

        @if (session('table_reservation_status'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ session('table_reservation_status') }}
            </div>
        @endif

        <div class="grid gap-8 lg:grid-cols-2">
            <article class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Nueva reserva') }}</p>

                <form method="POST" action="{{ route('shop.tables.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <div class="space-y-2">
                        <label for="table-number" class="text-sm font-semibold text-zinc-700">{{ __('Mesa') }}</label>
                        <select
                            id="table-number"
                            name="table_number"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                            required
                        >
                            <option value="">{{ __('Elige una mesa') }}</option>
                            @for ($i = 1; $i <= \App\Models\TableReservation::TOTAL_TABLES; $i++)
                                <option value="{{ $i }}" @selected(old('table_number') == $i)>
                                    {{ __('Mesa :number', ['number' => $i]) }}
                                </option>
                            @endfor
                        </select>
                        @error('table_number')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="reserved-for" class="text-sm font-semibold text-zinc-700">{{ __('Fecha y hora') }}</label>
                        <input
                            type="datetime-local"
                            id="reserved-for"
                            name="reserved_for"
                            value="{{ old('reserved_for', $defaultStart->format('Y-m-d\\TH:i')) }}"
                            min="{{ now()->format('Y-m-d\\TH:i') }}"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                            required
                        >
                        @error('reserved_for')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                        <p class="text-xs text-zinc-500">{{ __('Cada reserva cubre :hours horas. Si necesitas más tiempo háblalo con el staff.', ['hours' => $sessionDurationMinutes / 60]) }}</p>
                    </div>

                    <div class="space-y-2">
                        <label for="party-size" class="text-sm font-semibold text-zinc-700">{{ __('Número de jugadores') }}</label>
                        <input
                            type="number"
                            id="party-size"
                            name="party_size"
                            value="{{ old('party_size', 4) }}"
                            min="2"
                            max="{{ \App\Models\TableReservation::MAX_PARTY_SIZE }}"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                            required
                        >
                        @error('party_size')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="space-y-2">
                        <label for="table-notes" class="text-sm font-semibold text-zinc-700">{{ __('Notas') }}</label>
                        <textarea
                            id="table-notes"
                            name="notes"
                            rows="3"
                            class="w-full rounded-2xl border border-zinc-200 px-4 py-2 text-sm outline-none transition focus:border-zinc-900 focus:ring-2 focus:ring-zinc-900/10"
                            placeholder="{{ __('Cuéntanos qué juego llevarán o si necesitan extra de snacks.') }}"
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <p class="text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button
                        type="submit"
                        class="w-full rounded-full bg-zinc-900 px-4 py-3 text-center text-sm font-semibold text-white transition hover:bg-zinc-800"
                    >
                        {{ __('Apartar mesa') }}
                    </button>
                </form>
            </article>

            <article class="rounded-3xl border border-zinc-200 bg-white/90 p-6 shadow-sm">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Tus reservas') }}</p>

                <div class="mt-4 space-y-4">
                    @forelse ($upcomingReservations as $reservation)
                        <div class="rounded-2xl border border-zinc-100 bg-zinc-50 px-4 py-3 text-sm text-zinc-700">
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <p class="font-semibold text-zinc-900">
                                    {{ __('Mesa :table · :date', [
                                        'table' => $reservation->table_number,
                                        'date' => $reservation->reserved_for->translatedFormat('d M H:i'),
                                    ]) }}
                                </p>
                                <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClasses[$reservation->status] ?? 'bg-zinc-200 text-zinc-700' }}">
                                    {{ $reservation->status_label }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-500">{{ __('Código: :code · Grupo: :size jugadores', ['code' => $reservation->code, 'size' => $reservation->party_size]) }}</p>
                            @if ($reservation->notes)
                                <p class="text-xs text-zinc-500">{{ $reservation->notes }}</p>
                            @endif
                            @if ($reservation->status !== \App\Models\TableReservation::STATUS_CANCELLED && $reservation->reserved_for->isFuture())
                                <form
                                    method="POST"
                                    action="{{ route('shop.tables.destroy', $reservation) }}"
                                    class="mt-3"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-semibold text-rose-600 hover:text-rose-500">
                                        {{ __('Cancelar reserva') }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <p class="rounded-2xl border border-dashed border-zinc-200 px-4 py-3 text-sm text-zinc-500">
                            {{ __('Aún no tienes reservas activas.') }}
                        </p>
                    @endforelse
                </div>
            </article>
        </div>

        <article class="rounded-3xl border border-zinc-200 bg-white/60 p-6 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">{{ __('Próximas mesas ocupadas') }}</p>
            <p class="mt-2 text-sm text-zinc-600">{{ __('Consulta los horarios ocupados de los próximos 7 días para planear mejor tu visita.') }}</p>

            <div class="mt-4 grid gap-4 md:grid-cols-2">
                @forelse ($scheduledBlocks as $date => $reservations)
                    <div class="rounded-2xl border border-zinc-100 bg-white px-4 py-3 text-sm">
                        <p class="font-semibold text-zinc-900">{{ \Illuminate\Support\Carbon::parse($date)->translatedFormat('l d \\d\\e F') }}</p>
                        <ul class="mt-2 space-y-1 text-xs text-zinc-600">
                            @foreach ($reservations as $reservation)
                                @if($reservation->status === \App\Models\TableReservation::STATUS_CONFIRMED)
                                    <li>
                                        {{ __('Mesa :table · :start - :end', [
                                            'table' => $reservation->table_number,
                                            'start' => $reservation->reserved_for->format('H:i'),
                                            'end' => $reservation->reserved_until->format('H:i'),
                                        ]) }}
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="rounded-2xl border border-dashed border-zinc-200 px-4 py-3 text-sm text-zinc-500">
                        {{ __('Aún no hay reservas registradas para los próximos días.') }}
                    </p>
                @endforelse
            </div>
        </article>
    </section>
</x-layouts::shop>
