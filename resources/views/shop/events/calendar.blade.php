@php
    $userRegisteredEventIds = $userRegisteredEventIds ?? [];
@endphp

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

            @if (session('event_suggestion_status'))
                <div class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">
                    {{ session('event_suggestion_status') }}
                </div>
            @endif

            @if (($errors->eventRegistration ?? null) && $errors->eventRegistration->any())
                <div class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                    {{ $errors->eventRegistration->first('event') }}
                </div>
            @endif

            @php
                $dates = iterator_to_array($calendarPeriod);
                $calendarWeeks = array_chunk($dates, 7);
                $referenceDate = $calendarReferenceDate ?? ($selectedDate ? $selectedDate->copy() : now());
                $mobileWeek = collect($calendarWeeks)->first(fn ($week) => collect($week)->first(fn ($date) => $date->isSameDay($referenceDate)));
                if (! $mobileWeek) {
                    $mobileWeek = $calendarWeeks[0] ?? [];
                }
                $mobileWeekStart = $mobileWeek[0] ?? null;
                $mobileWeekEnd = $mobileWeek ? $mobileWeek[array_key_last($mobileWeek)] : null;
                $mobileWeekPreviousStart = $mobileWeekStart ? $mobileWeekStart->copy()->subWeek()->startOfWeek() : null;
                $mobileWeekNextStart = $mobileWeekStart ? $mobileWeekStart->copy()->addWeek()->startOfWeek() : null;
            @endphp

            <div class="flex items-center justify-between pb-4">
                <div class="flex items-center gap-3">
                    <a
                        href="{{ route('shop.events.index', ['month' => $previousMonth]) }}"
                        class="hidden rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 md:inline-flex"
                        wire:navigate
                    >
                        &larr; {{ \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->translatedFormat('F Y') }}
                    </a>
                    <a
                        href="{{ route('shop.events.index', ['month' => $nextMonth]) }}"
                        class="hidden rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100 md:inline-flex"
                        wire:navigate
                    >
                        {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->translatedFormat('F Y') }} &rarr;
                    </a>
                </div>

                <form method="GET" action="{{ route('shop.events.index') }}" class="hidden items-center gap-2 text-sm md:flex">
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

            <div class="hidden grid-cols-7 text-center text-xs font-semibold uppercase tracking-[0.3em] text-zinc-500 md:grid">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $label)
                    <div class="py-2">{{ $label }}</div>
                @endforeach
            </div>

            <div class="space-y-3 md:hidden">
                <div class="flex items-center justify-between">
                    <a
                        href="{{ $mobileWeekPreviousStart ? route('shop.events.index', ['month' => $mobileWeekPreviousStart->format('Y-m'), 'week' => $mobileWeekPreviousStart->toDateString()]) : route('shop.events.index', ['month' => $previousMonth]) }}"
                        class="rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100"
                        wire:navigate
                    >
                        &larr; {{ __('Semana') }}
                    </a>
                    <a
                        href="{{ $mobileWeekNextStart ? route('shop.events.index', ['month' => $mobileWeekNextStart->format('Y-m'), 'week' => $mobileWeekNextStart->toDateString()]) : route('shop.events.index', ['month' => $nextMonth]) }}"
                        class="rounded-full border border-zinc-200 px-3 py-1 text-sm font-semibold text-zinc-700 hover:bg-zinc-100"
                        wire:navigate
                    >
                        {{ __('Semana') }} &rarr;
                    </a>
                </div>

                <div class="rounded-xl border border-zinc-200 bg-white p-3">
                    <p class="text-xs font-semibold text-zinc-500">
                        {{ $mobileWeekStart ? $mobileWeekStart->translatedFormat('j') : '' }}
                        @if ($mobileWeekStart && $mobileWeekEnd && ! $mobileWeekStart->isSameDay($mobileWeekEnd))
                            &nbsp;-&nbsp;{{ $mobileWeekEnd->translatedFormat('j') }}
                        @endif
                        {{ $mobileWeekStart ? $mobileWeekStart->translatedFormat(' F Y') : '' }}
                    </p>
                    <p class="text-sm text-zinc-500">{{ __('Vista semanal') }}</p>
                </div>

                <div class="space-y-3">
                    @foreach ($mobileWeek as $date)
                        @php
                            $dateKey = $date->toDateString();
                            $dayEvents = $eventsByDate[$dateKey] ?? collect();
                            $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                        @endphp
                        <a
                            href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                            class="block rounded-2xl border p-3 text-sm transition hover:-translate-y-0.5 hover:border-amber-200 {{ $isSelected ? 'border-amber-300 bg-amber-50' : 'border-zinc-200/70' }}"
                            wire:navigate
                        >
                            <div class="flex items-center justify-between gap-3 text-xs font-semibold {{ $isSelected ? 'text-amber-700' : 'text-zinc-500' }}">
                                <span>{{ $date->translatedFormat('D') }}</span>
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
            </div>

            @foreach ($calendarWeeks as $week)
                <div class="hidden grid-cols-7 gap-3 border-b border-zinc-100 py-4 last:border-b-0 md:grid">
                    @foreach ($week as $date)
                        @php
                            $dateKey = $date->toDateString();
                            $dayEvents = $eventsByDate[$dateKey] ?? collect();
                            $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                        @endphp
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

            <div id="day-events-modal" class="fixed inset-0 z-40 {{ $selectedDate ? '' : 'hidden' }}">
                <button
                    type="button"
                    class="absolute inset-0 h-full w-full bg-black/55"
                    onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'"
                    data-day-events-close
                    aria-label="{{ __('Cerrar detalle') }}"
                ></button>

                <div class="relative mx-auto mt-16 flex w-full max-w-3xl rounded-3xl border border-zinc-200/80 bg-white px-5 py-6 shadow-xl">
                    <button
                        type="button"
                        class="absolute right-4 top-4 inline-flex items-center justify-center rounded-full bg-zinc-100 px-2.5 py-1 text-lg font-semibold text-zinc-900"
                        onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'"
                        data-day-events-close
                        aria-label="{{ __('Cerrar') }}"
                    >
                        ×
                    </button>

                    <div class="max-h-[88vh] w-full overflow-y-auto pr-1">
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

                            @if (($errors->eventProposal ?? null) && $errors->eventProposal->any())
                                <div class="mt-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">
                                    {{ $errors->eventProposal->first() }}
                                </div>
                            @endif

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
                                            @php
                                                $registrationsCount = $event->registrations_count ?? 0;
                                                $capacity = $event->capacity ?? 0;
                                            @endphp
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
                                                @php
                                                    $isRegistered = in_array($event->id, $userRegisteredEventIds, true);
                                                @endphp
                                                @if ($isRegistered)
                                                    <div class="inline-flex gap-2">
                                                        <span class="rounded-full bg-emerald-100 px-4 py-2 text-sm font-semibold text-emerald-700">{{ __('Ya estás registrado') }}</span>
                                                        <form method="POST" action="{{ route('shop.events.unregister', $event) }}" class="inline-flex">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">
                                                                {{ __('Cancelar registro') }}
                                                            </button>
                                                        </form>
                                                    </div>
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
                                    <div class="rounded-2xl border border-zinc-200/70 bg-zinc-50 p-4">
                                        <h3 class="text-lg font-semibold text-zinc-900">{{ __('Sin eventos para este día') }}</h3>
                                        <p class="mt-2 text-sm text-zinc-600">{{ __('Si quieres que aparezca algo aquí, proponé un evento y lo revisaremos.') }}</p>

                                        <form method="POST" action="{{ route('shop.events.suggest') }}" class="mt-4 space-y-3">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                                            <div>
                                                <label for="proposal-name" class="mb-1 block text-sm font-semibold text-zinc-700">{{ __('Tu nombre') }}</label>
                                                <input id="proposal-name" name="name" type="text" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" value="{{ old('name') }}">
                                            </div>
                                            <div>
                                                <label for="proposal-email" class="mb-1 block text-sm font-semibold text-zinc-700">{{ __('Correo') }}</label>
                                                <input id="proposal-email" name="email" type="email" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" value="{{ old('email') }}">
                                            </div>
                                            <div>
                                                <label for="proposal-title" class="mb-1 block text-sm font-semibold text-zinc-700">{{ __('Título del evento') }}</label>
                                                <input id="proposal-title" name="title" type="text" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm" value="{{ old('title') }}">
                                            </div>
                                            <div>
                                                <label for="proposal-description" class="mb-1 block text-sm font-semibold text-zinc-700">{{ __('Detalles de la propuesta') }}</label>
                                                <textarea id="proposal-description" name="description" rows="4" required class="w-full rounded-lg border border-zinc-300 px-3 py-2 text-sm">{{ old('description') }}</textarea>
                                            </div>
                                            <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-[#5a38a6] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#7a5fd3]">
                                                {{ __('Enviar propuesta') }}
                                            </button>
                                        </form>
                                    </div>
                                @endforelse
                            </div>
                        @else
                            <p class="mt-2 text-sm text-zinc-600">{{ __('Selecciona un día en el calendario para ver los detalles de los eventos.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-gradient-to-r from-[#fef5d7] to-[#dcd0ff]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-6 px-4 py-12 text-zinc-900 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div>
                <h2 class="text-2xl font-semibold">{{ __('¿Quieres proponer un evento?') }}</h2>
                <p class="mt-2 text-sm text-zinc-700">{{ __('Escríbenos en redes o acércate a tienda para colaborar en torneos, lanzamientos o workshops temáticos.') }}</p>
            </div>
            <a href="{{ route('home') }}#featured" class="rounded-full border border-zinc-900 px-6 py-3 text-sm font-semibold text-zinc-900 transition hover:bg-zinc-900 hover:text-white">
                {{ __('Ver destacados') }}
            </a>
        </div>
    </section>

    @if ($selectedDate)
        <script>
            (function () {
                const body = document.body;
                const root = document.documentElement;
                const previousBodyOverflow = body.style.overflow;
                const previousRootOverflow = root.style.overflow;
                const scrollbarWidth = window.innerWidth - root.clientWidth;
                const previousBodyPaddingRight = body.style.paddingRight;

                body.style.overflow = 'hidden';
                root.style.overflow = 'hidden';
                if (scrollbarWidth > 0) {
                    body.style.paddingRight = `${scrollbarWidth}px`;
                }

                const restoreScroll = () => {
                    body.style.overflow = previousBodyOverflow;
                    root.style.overflow = previousRootOverflow;
                    body.style.paddingRight = previousBodyPaddingRight;
                };

                document.querySelectorAll('[data-day-events-close]').forEach((button) => {
                    button.addEventListener('click', restoreScroll, { once: true });
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape') {
                        window.location.href = '{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}';
                    }
                });
            })();
        </script>
    @endif
</x-layouts::shop>
