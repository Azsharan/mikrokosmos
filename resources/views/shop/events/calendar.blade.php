@php($userRegisteredEventIds = $userRegisteredEventIds ?? [])

<x-layouts::shop :title="__('Eventos')">
    <section class="bg-gradient-to-br from-[#140528] via-[#37105e] to-[#0a0313] text-white">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 lg:flex-row lg:items-center lg:gap-14 lg:px-8">
            <div class="flex-1 space-y-4">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-[#f6d98f]">{{ __('Agenda Mikrokosmos') }}</p>
                <h1 class="text-4xl font-semibold leading-tight">{{ __('Calendario de eventos') }}</h1>
                <p class="text-base text-white/70">
                    {{ __('Consulta los torneos, lanzamientos y actividades de comunidad para este mes y planea tu visita a la tienda.') }}
                </p>
            </div>
            <div class="w-full max-w-sm rounded-3xl border border-white/10 bg-white/5 p-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-white/60">{{ __('Mes actual') }}</p>
                <p class="mt-2 text-4xl font-semibold text-white">{{ $currentMonth->translatedFormat('F Y') }}</p>
                <p class="mt-3 text-sm text-white/70">
                    {{ __('Los eventos se actualizan semanalmente. Mantente pendiente de nuestras redes para sorpresas adicionales.') }}
                </p>
            </div>
        </div>
    </section>

    <section class="bg-white">
        <div class="mx-auto w-full max-w-6xl px-4 py-12 lg:px-8">
            @if (session('event_registration_status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('event_registration_status') }}
                </div>
            @endif

            @if (($errors->eventRegistration ?? null) && $errors->eventRegistration->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                    {{ $errors->eventRegistration->first('event') }}
                </div>
            @endif

            <div class="flex items-center justify-between pb-4">
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('shop.events.index', ['month' => $previousMonth]) }}"
                        class="rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100"
                        wire:navigate
                    >
                        &larr; {{ \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->translatedFormat('F Y') }}
                    </a>
                    <a
                        href="{{ route('shop.events.index', ['month' => $nextMonth]) }}"
                        class="rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100"
                        wire:navigate
                    >
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->translatedFormat('F Y') }} &rarr;
                    </a>
                </div>

                <form method="GET" action="{{ route('shop.events.index') }}" class="flex items-center gap-2 text-sm">
                    <label for="month" class="text-zinc-600">{{ __('Ir al mes') }}</label>
                    <input
                        type="month"
                        id="month"
                        name="month"
                        value="{{ $currentMonth->format('Y-m') }}"
                        class="rounded-lg border border-zinc-200 px-3 py-1 text-sm text-zinc-700 focus:border-amber-400 focus:ring-amber-400"
                        onchange="this.form.submit()"
                    >
                </form>
            </div>

            <div class="grid grid-cols-7 text-center text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $label)
                    <div class="py-2">{{ $label }}</div>
                @endforeach
            </div>

            @php($dates = iterator_to_array($calendarPeriod))
            @foreach (array_chunk($dates, 7) as $week)
                <div class="grid grid-cols-7 gap-3 border-b border-zinc-100 py-4 last:border-b-0">
                    @foreach ($week as $date)
                        @php($dateKey = $date->toDateString())
                        @php($dayEvents = $eventsByDate[$dateKey] ?? collect())
                        @php($isSelected = $selectedDate && $selectedDate->isSameDay($date))
                        <a
                            href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                            class="min-h-[120px] rounded-2xl border p-3 text-sm transition hover:-translate-y-0.5 hover:border-amber-200 {{ $isSelected ? 'border-amber-300 bg-amber-50' : 'border-zinc-200/70' }}"
                            wire:navigate
                        >
                            <div class="flex items-center justify-between text-xs font-semibold {{ $isSelected ? 'text-amber-700' : 'text-zinc-500' }}">
                                <span>{{ $date->translatedFormat('j') }}</span>
                                @if ($date->isToday())
                                    <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">{{ __('Hoy') }}</span>
                                @endif
                                @if ($isSelected)
                                    <span class="rounded-full bg-amber-200 px-2 py-0.5 text-[10px] font-semibold text-amber-900">{{ __('Seleccionado') }}</span>
                                @endif
                            </div>
                            <div class="mt-2 space-y-2 text-xs text-zinc-600">
                                @forelse ($dayEvents as $event)
                                    <div class="rounded-xl bg-gradient-to-r from-[#fbe5a1] to-[#dcd0ff] p-2 text-left">
                                        <p class="text-[11px] font-semibold text-zinc-900">{{ $event->name }}</p>
                                        <p class="text-[10px] text-zinc-700">
                                            {{ optional($event->start_at)->format('H:i') }}
                                            @if ($event->is_online)
                                                · {{ __('Online') }}
                                            @endif
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-[11px] text-zinc-400">{{ __('Sin eventos') }}</p>
                                @endforelse
                            </div>
                        </a>
                    @endforeach
                </div>
            @endforeach

            <div class="mt-10 rounded-3xl border border-zinc-200/70 bg-white/80 p-6 shadow-sm">
                @if ($selectedDate)
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-amber-600">{{ __('Eventos para') }}</p>
                            <h2 class="text-3xl font-semibold text-zinc-900">{{ $selectedDate->translatedFormat('l j \\d\\e F') }}</h2>
                        </div>
                        <div class="text-sm text-zinc-600">
                            <p>{{ __('Explora los detalles de cada evento seleccionado en el calendario.') }}</p>
                        </div>
                    </div>

                    <div class="mt-6 space-y-4">
                        @forelse ($selectedDayEvents as $event)
                            <article class="rounded-2xl border border-zinc-200/70 bg-white p-4 shadow-sm">
                                <div class="flex flex-col gap-2 lg:flex-row lg:items-center lg:justify-between">
                                    <div>
                                        <p class="text-lg font-semibold text-zinc-900">{{ $event->name }}</p>
                                        <p class="text-sm text-zinc-600">{{ $event->eventType?->name ?? __('Actividad de comunidad') }}</p>
                                    </div>
                                    <div class="text-right text-sm text-zinc-600">
                                        <p>{{ optional($event->start_at)->format('H:i') }} - {{ optional($event->end_at)->format('H:i') }}</p>
                                        <p>{{ $event->is_online ? __('Online') : ($event->location ?? __('En tienda')) }}</p>
                                    </div>
                                </div>
                                <div class="mt-2 flex flex-wrap items-center gap-3 text-xs font-semibold text-zinc-600">
                                    @php($registrationsCount = $event->registrations_count ?? 0)
                                    @php($capacity = $event->capacity ?? 0)
                                    <span>{{ __('Cupo') }}: {{ max($capacity - $registrationsCount, 0) }} / {{ $capacity }}</span>
                                    @if ($event->isFull())
                                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-rose-700">{{ __('Cupo lleno') }}</span>
                                    @endif
                                </div>
                                @if ($event->description)
                                    <p class="mt-3 text-sm text-zinc-700">{{ \Illuminate\Support\Str::limit($event->description, 200) }}</p>
                                @endif
                                <div class="mt-4">
                                    @auth('shop')
                                        @php($isRegistered = in_array($event->id, $userRegisteredEventIds, true))
                                        @if ($isRegistered)
                                            <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">{{ __('Ya estás registrado') }}</span>
                                        @elseif ($event->isFull())
                                            <span class="rounded-full bg-rose-100 px-4 py-2 text-sm font-semibold text-rose-700">{{ __('Evento lleno') }}</span>
                                        @else
                                            <form method="POST" action="{{ route('shop.events.register', $event) }}" class="inline-flex">
                                                @csrf
                                                <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#5a38a6] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#7a5fd3]">
                                                    {{ __('Registrarme') }}
                                                </button>
                                            </form>
                                        @endif
                                    @else
                                        <form method="POST" action="{{ route('shop.events.register', $event) }}" class="inline-flex">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#5a38a6] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#7a5fd3]">
                                                {{ __('Registrarme') }}
                                            </button>
                                        </form>
                                    @endauth
                                </div>
                            </article>
                        @empty
                            <p class="text-sm text-zinc-600">{{ __('No hay eventos para este día.') }}</p>
                        @endforelse
                    </div>
                @else
                    <p class="text-sm text-zinc-600">{{ __('Selecciona un día en el calendario para ver los detalles de los eventos.') }}</p>
                @endif
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-r from-[#fef5d7] to-[#dcd0ff]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 text-zinc-900 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <h2 class="text-2xl font-semibold">{{ __('¿Quieres proponer un evento?') }}</h2>
                <p class="mt-2 text-sm text-zinc-700">{{ __('Escríbenos en redes o acércate a tienda para colaborar en torneos, lanzamientos o workshops temáticos.') }}</p>
            </div>
            <a href="{{ route('home') }}#community" class="rounded-full border border-zinc-900 px-6 py-3 text-sm font-semibold text-zinc-900 transition hover:bg-zinc-900 hover:text-white">
                {{ __('Ir a la comunidad') }}
            </a>
        </div>
    </section>
</x-layouts::shop>
