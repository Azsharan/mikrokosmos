@php
    $userRegisteredEventIds = $userRegisteredEventIds ?? [];
@endphp

<x-layouts::shop :title="__('Eventos')">

    {{-- Hero / month nav --}}
    <section class="bg-[#1c0f3f]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-10 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-[#e6c45c]">{{ __('Agenda Mikrokosmos') }}</p>
                <h1 class="text-3xl font-bold text-white">{{ $currentMonth->translatedFormat('F Y') }}</h1>
                <p class="text-sm text-white/50">{{ __('Torneos, lanzamientos y actividades de comunidad.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a
                    href="{{ route('shop.events.index', ['month' => $previousMonth]) }}"
                    class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white/70 transition hover:border-white/40 hover:text-white"
                    wire:navigate
                >
                    &larr; {{ \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->translatedFormat('M') }}
                </a>
                <form method="GET" action="{{ route('shop.events.index') }}" class="flex items-center gap-2">
                    <input
                        type="month"
                        name="month"
                        value="{{ $currentMonth->format('Y-m') }}"
                        class="rounded-full border border-white/20 bg-white/10 px-3 py-2 text-sm text-white focus:border-[#e6c45c] focus:outline-none"
                        onchange="this.form.submit()"
                    >
                </form>
                <a
                    href="{{ route('shop.events.index', ['month' => $nextMonth]) }}"
                    class="rounded-full border border-white/20 px-4 py-2 text-sm font-semibold text-white/70 transition hover:border-white/40 hover:text-white"
                    wire:navigate
                >
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->translatedFormat('M') }} &rarr;
                </a>
            </div>
        </div>
    </section>

    <section class="bg-[#faf7ff]">
        <div class="mx-auto w-full max-w-6xl px-4 py-10 lg:px-8">

            @if (session('event_registration_status'))
                <div class="mb-6 rounded-xl border border-[#b8f5cc] bg-[#edfff5] px-4 py-3 text-sm font-semibold text-[#1a6638]">
                    {{ session('event_registration_status') }}
                </div>
            @endif

            @if (session('event_suggestion_status'))
                <div class="mb-6 rounded-xl border border-[#b8f5cc] bg-[#edfff5] px-4 py-3 text-sm font-semibold text-[#1a6638]">
                    {{ session('event_suggestion_status') }}
                </div>
            @endif

            @if (($errors->eventRegistration ?? null) && $errors->eventRegistration->any())
                <div class="mb-6 rounded-xl border border-[#f5c8c8] bg-[#fff0f0] px-4 py-3 text-sm font-semibold text-[#8b1a1a]">
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

            {{-- Desktop: weekday labels --}}
            <div class="mb-2 hidden grid-cols-7 text-center text-xs font-semibold uppercase tracking-[0.2em] text-[#7b5fd0] md:grid">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $label)
                    <div class="py-2">{{ $label }}</div>
                @endforeach
            </div>

            {{-- Mobile: week navigation --}}
            <div class="mb-4 space-y-3 md:hidden">
                <div class="flex items-center justify-between">
                    <a
                        href="{{ $mobileWeekPreviousStart ? route('shop.events.index', ['month' => $mobileWeekPreviousStart->format('Y-m'), 'week' => $mobileWeekPreviousStart->toDateString()]) : route('shop.events.index', ['month' => $previousMonth]) }}"
                        class="rounded-full border border-[#d5c8f5] px-3 py-1.5 text-sm font-semibold text-[#3d1f78] transition hover:border-[#5a38a6] hover:bg-white"
                        wire:navigate
                    >&larr; {{ __('Semana') }}</a>
                    <span class="text-sm font-semibold text-[#1c0f3f]">
                        {{ $mobileWeekStart?->translatedFormat('j') }}
                        @if ($mobileWeekStart && $mobileWeekEnd && !$mobileWeekStart->isSameDay($mobileWeekEnd))
                            – {{ $mobileWeekEnd->translatedFormat('j') }}
                        @endif
                        {{ $mobileWeekStart?->translatedFormat('F') }}
                    </span>
                    <a
                        href="{{ $mobileWeekNextStart ? route('shop.events.index', ['month' => $mobileWeekNextStart->format('Y-m'), 'week' => $mobileWeekNextStart->toDateString()]) : route('shop.events.index', ['month' => $nextMonth]) }}"
                        class="rounded-full border border-[#d5c8f5] px-3 py-1.5 text-sm font-semibold text-[#3d1f78] transition hover:border-[#5a38a6] hover:bg-white"
                        wire:navigate
                    >{{ __('Semana') }} &rarr;</a>
                </div>

                <div class="space-y-2">
                    @foreach ($mobileWeek as $date)
                        @php
                            $dateKey = $date->toDateString();
                            $dayEvents = $eventsByDate[$dateKey] ?? collect();
                            $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                            $isToday = $date->isToday();
                        @endphp
                        <a
                            href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                            class="block rounded-xl border p-3 text-sm transition hover:-translate-y-0.5 {{ $isSelected ? 'border-[#5a38a6] bg-[#f0ebff]' : ($isToday ? 'border-[#e6c45c] bg-[#fffbeb]' : 'border-[#e0d5f5] bg-white hover:border-[#7b5fd0]') }}"
                            wire:navigate
                        >
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold {{ $isSelected ? 'text-[#5a38a6]' : ($isToday ? 'text-[#92660a]' : 'text-[#7b5fd0]') }}">
                                    {{ $date->translatedFormat('D') }}
                                </span>
                                <span class="text-sm font-bold {{ $isSelected ? 'text-[#3d1f78]' : 'text-[#1c0f3f]' }}">
                                    {{ $date->translatedFormat('j') }}
                                </span>
                                @if ($isToday)
                                    <span class="ml-auto rounded-full bg-[#e6c45c] px-2 py-0.5 text-[10px] font-bold text-[#1c0f3f]">{{ __('Hoy') }}</span>
                                @endif
                            </div>
                            @if ($dayEvents->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($dayEvents->take(2) as $event)
                                        <span class="rounded-md bg-[#3d1f78] px-2 py-0.5 text-[10px] font-semibold text-white">
                                            {{ $event->name }}
                                        </span>
                                    @endforeach
                                    @if ($dayEvents->count() > 2)
                                        <span class="text-[10px] font-semibold text-[#7b5fd0]">+{{ $dayEvents->count() - 2 }}</span>
                                    @endif
                                </div>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Desktop: full month grid --}}
            <div class="hidden space-y-1 md:block">
                @foreach ($calendarWeeks as $week)
                    <div class="grid grid-cols-7 gap-1">
                        @foreach ($week as $date)
                            @php
                                $dateKey = $date->toDateString();
                                $dayEvents = $eventsByDate[$dateKey] ?? collect();
                                $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                                $isToday = $date->isToday();
                                $isCurrentMonth = $date->isSameMonth($currentMonth);
                            @endphp
                            <a
                                href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                                class="min-h-[110px] rounded-xl border p-2.5 text-sm transition hover:-translate-y-0.5 {{ $isSelected ? 'border-[#5a38a6] bg-[#f0ebff]' : ($isToday ? 'border-[#e6c45c] bg-[#fffbeb]' : 'border-[#e0d5f5] bg-white hover:border-[#7b5fd0]') }} {{ !$isCurrentMonth ? 'opacity-40' : '' }}"
                                wire:navigate
                            >
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold {{ $isSelected ? 'text-[#5a38a6]' : ($isToday ? 'text-[#92660a]' : 'text-[#1c0f3f]') }}">
                                        {{ $date->translatedFormat('j') }}
                                    </span>
                                    @if ($isToday)
                                        <span class="rounded-full bg-[#e6c45c] px-1.5 py-0.5 text-[9px] font-bold text-[#1c0f3f]">{{ __('Hoy') }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 space-y-1">
                                    @foreach ($dayEvents->take(3) as $event)
                                        <div class="truncate rounded-md bg-[#3d1f78] px-1.5 py-0.5 text-[10px] font-semibold text-white">
                                            {{ $event->start_at->format('H:i') }} {{ $event->name }}
                                        </div>
                                    @endforeach
                                    @if ($dayEvents->count() > 3)
                                        <div class="text-[10px] font-semibold text-[#7b5fd0]">+{{ $dayEvents->count() - 3 }}</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Day detail modal --}}
            <div id="day-events-modal" class="fixed inset-0 z-40 {{ $selectedDate ? '' : 'hidden' }}">
                <button
                    type="button"
                    class="absolute inset-0 h-full w-full bg-black/60"
                    onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'"
                    aria-label="{{ __('Cerrar') }}"
                ></button>

                <div class="relative mx-auto mt-12 flex w-full max-w-2xl rounded-2xl border border-[#e0d5f5] bg-white px-5 py-6 shadow-2xl">
                    <button
                        type="button"
                        class="absolute right-4 top-4 flex size-7 items-center justify-center rounded-full bg-[#f3eeff] text-[#3d1f78] transition hover:bg-[#e0d5f5]"
                        onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'"
                        aria-label="{{ __('Cerrar') }}"
                    >✕</button>

                    <div class="max-h-[85vh] w-full overflow-y-auto pr-1">
                        @if ($selectedDate)
                            <div class="mb-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#7b5fd0]">{{ __('Eventos') }}</p>
                                <h2 class="text-2xl font-bold text-[#1c0f3f]">{{ $selectedDate->translatedFormat('l j \\d\\e F') }}</h2>
                            </div>

                            @if (($errors->eventProposal ?? null) && $errors->eventProposal->any())
                                <div class="mb-4 rounded-xl border border-[#f5c8c8] bg-[#fff0f0] px-4 py-3 text-sm font-semibold text-[#8b1a1a]">
                                    {{ $errors->eventProposal->first() }}
                                </div>
                            @endif

                            <div class="space-y-4">
                                @forelse ($selectedDayEvents as $event)
                                    <article class="rounded-xl border border-[#e0d5f5] bg-[#faf7ff] p-4">
                                        <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                                            <div>
                                                <p class="text-lg font-bold text-[#1c0f3f]">{{ $event->name }}</p>
                                                <p class="text-sm text-[#7b5fd0]">{{ $event->eventType?->name ?? __('Actividad de comunidad') }}</p>
                                            </div>
                                            <div class="shrink-0 text-sm text-[#4b2d7f]/70">
                                                <p class="font-semibold">{{ optional($event->start_at)->format('H:i') }} – {{ optional($event->end_at)->format('H:i') }}</p>
                                                <p>{{ $event->is_online ? __('Online') : ($event->location ?? __('En tienda')) }}</p>
                                            </div>
                                        </div>

                                        @php
                                            $registrationsCount = $event->registrations_count ?? 0;
                                            $capacity = $event->capacity ?? 0;
                                        @endphp
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-[#7b5fd0]">
                                            <span>{{ __('Cupo') }}: {{ max($capacity - $registrationsCount, 0) }}/{{ $capacity }}</span>
                                            @if ($event->isFull())
                                                <span class="rounded-full bg-[#fff0f0] px-2 py-0.5 text-[#8b1a1a]">{{ __('Cupo lleno') }}</span>
                                            @endif
                                        </div>

                                        @if ($event->description)
                                            <p class="mt-2 text-sm text-[#4b2d7f]/70">{{ \Illuminate\Support\Str::limit($event->description, 200) }}</p>
                                        @endif

                                        <div class="mt-3">
                                            @auth('shop')
                                                @php $isRegistered = in_array($event->id, $userRegisteredEventIds, true); @endphp
                                                @if ($isRegistered)
                                                    <div class="flex flex-wrap gap-2">
                                                        <span class="rounded-full bg-[#edfff5] px-4 py-2 text-sm font-semibold text-[#1a6638]">{{ __('Ya estás registrado') }}</span>
                                                        <form method="POST" action="{{ route('shop.events.unregister', $event) }}">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="rounded-full border border-[#f5c8c8] bg-[#fff0f0] px-4 py-2 text-sm font-semibold text-[#8b1a1a] transition hover:bg-[#fde0e0]">
                                                                {{ __('Cancelar registro') }}
                                                            </button>
                                                        </form>
                                                    </div>
                                                @elseif ($event->isFull())
                                                    <span class="rounded-full bg-[#fff0f0] px-4 py-2 text-sm font-semibold text-[#8b1a1a]">{{ __('Evento lleno') }}</span>
                                                @else
                                                    <form method="POST" action="{{ route('shop.events.register', $event) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-full bg-[#e6c45c] px-5 py-2 text-sm font-semibold text-[#1c0f3f] transition hover:bg-[#f6d98f]">
                                                            {{ __('Registrarme') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            @else
                                                <form method="POST" action="{{ route('shop.events.register', $event) }}">
                                                    @csrf
                                                    <button type="submit" class="rounded-full bg-[#e6c45c] px-5 py-2 text-sm font-semibold text-[#1c0f3f] transition hover:bg-[#f6d98f]">
                                                        {{ __('Registrarme') }}
                                                    </button>
                                                </form>
                                            @endauth
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-xl border border-[#e0d5f5] bg-white p-5">
                                        <h3 class="font-bold text-[#1c0f3f]">{{ __('Sin eventos este día') }}</h3>
                                        <p class="mt-1 text-sm text-[#4b2d7f]/60">{{ __('¿Quieres proponer algo? Completa el formulario y lo revisaremos.') }}</p>

                                        <form method="POST" action="{{ route('shop.events.suggest') }}" class="mt-5 space-y-3">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                                            <div>
                                                <label for="proposal-name" class="mb-1 block text-xs font-semibold text-[#3d1f78]">{{ __('Tu nombre') }}</label>
                                                <input id="proposal-name" name="name" type="text" required value="{{ old('name') }}"
                                                    class="w-full rounded-lg border border-[#d5c8f5] px-3 py-2 text-sm text-[#1c0f3f] focus:border-[#5a38a6] focus:outline-none">
                                            </div>
                                            <div>
                                                <label for="proposal-email" class="mb-1 block text-xs font-semibold text-[#3d1f78]">{{ __('Correo') }}</label>
                                                <input id="proposal-email" name="email" type="email" required value="{{ old('email') }}"
                                                    class="w-full rounded-lg border border-[#d5c8f5] px-3 py-2 text-sm text-[#1c0f3f] focus:border-[#5a38a6] focus:outline-none">
                                            </div>
                                            <div>
                                                <label for="proposal-title" class="mb-1 block text-xs font-semibold text-[#3d1f78]">{{ __('Título del evento') }}</label>
                                                <input id="proposal-title" name="title" type="text" required value="{{ old('title') }}"
                                                    class="w-full rounded-lg border border-[#d5c8f5] px-3 py-2 text-sm text-[#1c0f3f] focus:border-[#5a38a6] focus:outline-none">
                                            </div>
                                            <div>
                                                <label for="proposal-description" class="mb-1 block text-xs font-semibold text-[#3d1f78]">{{ __('Detalles') }}</label>
                                                <textarea id="proposal-description" name="description" rows="3" required
                                                    class="w-full rounded-lg border border-[#d5c8f5] px-3 py-2 text-sm text-[#1c0f3f] focus:border-[#5a38a6] focus:outline-none">{{ old('description') }}</textarea>
                                            </div>
                                            <button type="submit" class="rounded-full bg-[#e6c45c] px-5 py-2 text-sm font-semibold text-[#1c0f3f] transition hover:bg-[#f6d98f]">
                                                {{ __('Enviar propuesta') }}
                                            </button>
                                        </form>
                                    </div>
                                @endforelse
                            </div>
                        @else
                            <p class="text-sm text-[#4b2d7f]/60">{{ __('Selecciona un día para ver los eventos.') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    @if ($selectedDate)
        <script>
            (function () {
                const body = document.body;
                const root = document.documentElement;
                const prevOverflow = body.style.overflow;
                const scrollbarWidth = window.innerWidth - root.clientWidth;
                body.style.overflow = 'hidden';
                if (scrollbarWidth > 0) body.style.paddingRight = `${scrollbarWidth}px`;
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') window.location.href = '{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}';
                });
            })();
        </script>
    @endif

</x-layouts::shop>
