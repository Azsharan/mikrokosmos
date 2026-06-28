@php
    $userRegisteredEventIds = $userRegisteredEventIds ?? [];
@endphp

<x-layouts::shop :title="__('Eventos')">

    <section class="bg-[#6b70c4]">
        <div class="mx-auto flex w-full max-w-6xl flex-col gap-4 px-4 py-10 lg:flex-row lg:items-center lg:justify-between lg:px-8">
            <div class="space-y-1">
                <p class="text-xs font-semibold uppercase tracking-[0.25em] text-white">{{ __('Agenda Mikrokosmos') }}</p>
                <h1 class="text-3xl font-bold text-white">{{ $currentMonth->translatedFormat('F Y') }}</h1>
                <p class="text-sm text-white/80">{{ __('Torneos, lanzamientos y actividades de comunidad.') }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('shop.events.index', ['month' => $previousMonth]) }}" class="rounded-full border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10" wire:navigate>
                    &larr; {{ \Carbon\Carbon::createFromFormat('Y-m', $previousMonth)->translatedFormat('M') }}
                </a>
                <form method="GET" action="{{ route('shop.events.index') }}" class="flex items-center gap-2">
                    <input type="month" name="month" value="{{ $currentMonth->format('Y-m') }}"
                        class="rounded-full border border-white/30 bg-white/10 px-3 py-2 text-sm text-white focus:border-white focus:outline-none"
                        onchange="this.form.submit()">
                </form>
                <a href="{{ route('shop.events.index', ['month' => $nextMonth]) }}" class="rounded-full border border-white/30 px-4 py-2 text-sm font-semibold text-white transition hover:bg-white/10" wire:navigate>
                    {{ \Carbon\Carbon::createFromFormat('Y-m', $nextMonth)->translatedFormat('M') }} &rarr;
                </a>
            </div>
        </div>
    </section>

    <section class="bg-[#f8f7ff]">
        <div class="mx-auto w-full max-w-6xl px-4 py-10 lg:px-8">

            @if (session('event_registration_status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('event_registration_status') }}</div>
            @endif

            @if (session('event_suggestion_status'))
                <div class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-800">{{ session('event_suggestion_status') }}</div>
            @endif

            @if (($errors->eventRegistration ?? null) && $errors->eventRegistration->any())
                <div class="mb-6 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">{{ $errors->eventRegistration->first('event') }}</div>
            @endif

            @php
                $dates = iterator_to_array($calendarPeriod);
                $calendarWeeks = array_chunk($dates, 7);
                $referenceDate = $calendarReferenceDate ?? ($selectedDate ? $selectedDate->copy() : now());
                $mobileWeek = collect($calendarWeeks)->first(fn ($week) => collect($week)->first(fn ($date) => $date->isSameDay($referenceDate)));
                if (! $mobileWeek) { $mobileWeek = $calendarWeeks[0] ?? []; }
                $mobileWeekStart = $mobileWeek[0] ?? null;
                $mobileWeekEnd = $mobileWeek ? $mobileWeek[array_key_last($mobileWeek)] : null;
                $mobileWeekPreviousStart = $mobileWeekStart ? $mobileWeekStart->copy()->subWeek()->startOfWeek() : null;
                $mobileWeekNextStart = $mobileWeekStart ? $mobileWeekStart->copy()->addWeek()->startOfWeek() : null;
            @endphp

            {{-- Desktop: weekday labels --}}
            <div class="mb-2 hidden grid-cols-7 text-center text-xs font-semibold uppercase tracking-[0.2em] text-[#6b70c4] md:grid">
                @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $label)
                    <div class="py-2">{{ $label }}</div>
                @endforeach
            </div>

            {{-- Mobile: week navigation --}}
            <div class="mb-4 space-y-3 md:hidden">
                <div class="flex items-center justify-between">
                    <a href="{{ $mobileWeekPreviousStart ? route('shop.events.index', ['month' => $mobileWeekPreviousStart->format('Y-m'), 'week' => $mobileWeekPreviousStart->toDateString()]) : route('shop.events.index', ['month' => $previousMonth]) }}" class="rounded-full border border-[#dddeff] px-3 py-1.5 text-sm font-semibold text-[#4a4fa8] transition hover:border-[#6b70c4] hover:bg-white" wire:navigate>&larr; {{ __('Semana') }}</a>
                    <span class="text-sm font-semibold text-[#1e2e74]">
                        {{ $mobileWeekStart?->translatedFormat('j') }}
                        @if ($mobileWeekStart && $mobileWeekEnd && !$mobileWeekStart->isSameDay($mobileWeekEnd)) – {{ $mobileWeekEnd->translatedFormat('j') }} @endif
                        {{ $mobileWeekStart?->translatedFormat('F') }}
                    </span>
                    <a href="{{ $mobileWeekNextStart ? route('shop.events.index', ['month' => $mobileWeekNextStart->format('Y-m'), 'week' => $mobileWeekNextStart->toDateString()]) : route('shop.events.index', ['month' => $nextMonth]) }}" class="rounded-full border border-[#dddeff] px-3 py-1.5 text-sm font-semibold text-[#4a4fa8] transition hover:border-[#6b70c4] hover:bg-white" wire:navigate>{{ __('Semana') }} &rarr;</a>
                </div>

                <div class="space-y-2">
                    @foreach ($mobileWeek as $date)
                        @php
                            $dateKey = $date->toDateString();
                            $dayEvents = $eventsByDate[$dateKey] ?? collect();
                            $isSelected = $selectedDate && $selectedDate->isSameDay($date);
                            $isToday = $date->isToday();
                        @endphp
                        <a href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                            class="block rounded-xl border p-3 text-sm transition hover:-translate-y-0.5 {{ $isSelected ? 'border-[#6b70c4] bg-[#eeeeff]' : ($isToday ? 'border-[#f5a520] bg-[#fff8eb]' : 'border-[#dddeff] bg-white hover:border-[#6b70c4]') }}"
                            wire:navigate>
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-semibold {{ $isSelected ? 'text-[#6b70c4]' : ($isToday ? 'text-[#c47a00]' : 'text-[#4a4fa8]') }}">{{ $date->translatedFormat('D') }}</span>
                                <span class="text-sm font-bold text-[#1e2e74]">{{ $date->translatedFormat('j') }}</span>
                                @if ($isToday)
                                    <span class="ml-auto rounded-full bg-[#f5a520] px-2 py-0.5 text-[10px] font-bold text-[#1e2e74]">{{ __('Hoy') }}</span>
                                @endif
                            </div>
                            @if ($dayEvents->isNotEmpty())
                                <div class="mt-2 flex flex-wrap gap-1">
                                    @foreach ($dayEvents->take(2) as $event)
                                        <span class="rounded-md bg-[#6b70c4] px-2 py-0.5 text-[10px] font-semibold text-white">{{ $event->name }}</span>
                                    @endforeach
                                    @if ($dayEvents->count() > 2)
                                        <span class="text-[10px] font-semibold text-[#4a4fa8]">+{{ $dayEvents->count() - 2 }}</span>
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
                            <a href="{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m'), 'day' => $date->toDateString()]) }}"
                                class="min-h-[110px] rounded-xl border p-2.5 transition hover:-translate-y-0.5 {{ $isSelected ? 'border-[#6b70c4] bg-[#eeeeff]' : ($isToday ? 'border-[#f5a520] bg-[#fff8eb]' : 'border-[#dddeff] bg-white hover:border-[#6b70c4]') }} {{ !$isCurrentMonth ? 'opacity-40' : '' }}"
                                wire:navigate>
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-bold {{ $isSelected ? 'text-[#6b70c4]' : ($isToday ? 'text-[#c47a00]' : 'text-[#1e2e74]') }}">{{ $date->translatedFormat('j') }}</span>
                                    @if ($isToday)
                                        <span class="rounded-full bg-[#f5a520] px-1.5 py-0.5 text-[9px] font-bold text-[#1e2e74]">{{ __('Hoy') }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 space-y-1">
                                    @foreach ($dayEvents->take(3) as $event)
                                        <div class="truncate rounded-md bg-[#6b70c4] px-1.5 py-0.5 text-[10px] font-semibold text-white">{{ $event->start_at->format('H:i') }} {{ $event->name }}</div>
                                    @endforeach
                                    @if ($dayEvents->count() > 3)
                                        <div class="text-[10px] font-semibold text-[#4a4fa8]">+{{ $dayEvents->count() - 3 }}</div>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endforeach
            </div>

            {{-- Day detail modal --}}
            <div id="day-events-modal" class="fixed inset-0 z-40 {{ $selectedDate ? '' : 'hidden' }}">
                <button type="button" class="absolute inset-0 h-full w-full bg-[#1e2e74]/50" onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'" aria-label="{{ __('Cerrar') }}"></button>

                <div class="relative mx-auto mt-12 flex w-full max-w-2xl rounded-2xl border border-[#dddeff] bg-white px-5 py-6 shadow-2xl">
                    <button type="button" class="absolute right-4 top-4 flex size-7 items-center justify-center rounded-full bg-[#eeeeff] text-[#4a4fa8] transition hover:bg-[#dddeff]" onclick="window.location.href='{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}'" aria-label="{{ __('Cerrar') }}">✕</button>

                    <div class="max-h-[85vh] w-full overflow-y-auto pr-1">
                        @if ($selectedDate)
                            <div class="mb-6">
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-[#6b70c4]">{{ __('Eventos') }}</p>
                                <h2 class="text-2xl font-bold text-[#1e2e74]">{{ $selectedDate->translatedFormat('l j \\d\\e F') }}</h2>
                            </div>

                            @if (($errors->eventProposal ?? null) && $errors->eventProposal->any())
                                <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-semibold text-rose-800">{{ $errors->eventProposal->first() }}</div>
                            @endif

                            <div class="space-y-4">
                                @forelse ($selectedDayEvents as $event)
                                    <article class="rounded-xl border border-[#dddeff] bg-[#f8f7ff] p-4">
                                        <div class="flex flex-col gap-2 lg:flex-row lg:items-start lg:justify-between">
                                            <div>
                                                <p class="text-lg font-bold text-[#1e2e74]">{{ $event->name }}</p>
                                                <p class="text-sm text-[#4a4fa8]">{{ $event->eventType?->name ?? __('Actividad de comunidad') }}</p>
                                            </div>
                                            <div class="shrink-0 text-sm text-[#4a4fa8]">
                                                <p class="font-semibold">{{ optional($event->start_at)->format('H:i') }} – {{ optional($event->end_at)->format('H:i') }}</p>
                                                <p>{{ $event->is_online ? __('Online') : ($event->location ?? __('En tienda')) }}</p>
                                            </div>
                                        </div>

                                        @php $registrationsCount = $event->registrations_count ?? 0; $capacity = $event->capacity ?? 0; @endphp
                                        <div class="mt-2 flex flex-wrap items-center gap-2 text-xs font-semibold text-[#4a4fa8]">
                                            <span>{{ __('Cupo') }}: {{ max($capacity - $registrationsCount, 0) }}/{{ $capacity }}</span>
                                            @if ($event->isFull())
                                                <span class="rounded-full bg-rose-50 px-2 py-0.5 text-rose-700">{{ __('Cupo lleno') }}</span>
                                            @endif
                                        </div>

                                        @if ($event->description)
                                            <p class="mt-2 text-sm text-[#4a4fa8]">{{ \Illuminate\Support\Str::limit($event->description, 200) }}</p>
                                        @endif

                                        <div class="mt-3">
                                            @auth('shop')
                                                @php $isRegistered = in_array($event->id, $userRegisteredEventIds, true); @endphp
                                                @if ($isRegistered)
                                                    <div class="flex flex-wrap gap-2">
                                                        <span class="rounded-full bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-700">{{ __('Ya estás registrado') }}</span>
                                                        <form method="POST" action="{{ route('shop.events.unregister', $event) }}">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="rounded-full border border-rose-200 bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700 transition hover:bg-rose-100">{{ __('Cancelar registro') }}</button>
                                                        </form>
                                                    </div>
                                                @elseif ($event->isFull())
                                                    <span class="rounded-full bg-rose-50 px-4 py-2 text-sm font-semibold text-rose-700">{{ __('Evento lleno') }}</span>
                                                @else
                                                    <form method="POST" action="{{ route('shop.events.register', $event) }}">
                                                        @csrf
                                                        <button type="submit" class="rounded-full bg-[#f5a520] px-5 py-2 text-sm font-semibold text-[#1e2e74] transition hover:bg-[#ffd978]">{{ __('Registrarme') }}</button>
                                                    </form>
                                                @endif
                                            @else
                                                <form method="POST" action="{{ route('shop.events.register', $event) }}">
                                                    @csrf
                                                    <button type="submit" class="rounded-full bg-[#f5a520] px-5 py-2 text-sm font-semibold text-[#1e2e74] transition hover:bg-[#ffd978]">{{ __('Registrarme') }}</button>
                                                </form>
                                            @endauth
                                        </div>
                                    </article>
                                @empty
                                    <div class="rounded-xl border border-[#dddeff] bg-white p-5">
                                        <h3 class="font-bold text-[#1e2e74]">{{ __('Sin eventos este día') }}</h3>
                                        <p class="mt-1 text-sm text-[#4a4fa8]">{{ __('¿Quieres proponer algo? Completa el formulario y lo revisaremos.') }}</p>

                                        <form method="POST" action="{{ route('shop.events.suggest') }}" class="mt-5 space-y-3">
                                            @csrf
                                            <input type="hidden" name="date" value="{{ $selectedDate->toDateString() }}">
                                            @foreach ([['proposal-name','name',__('Tu nombre'),'text'], ['proposal-email','email',__('Correo'),'email'], ['proposal-title','title',__('Título del evento'),'text']] as [$id,$name,$label,$type])
                                                <div>
                                                    <label for="{{ $id }}" class="mb-1 block text-xs font-semibold text-[#4a4fa8]">{{ $label }}</label>
                                                    <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" required value="{{ old($name) }}" class="w-full rounded-lg border border-[#dddeff] px-3 py-2 text-sm text-[#1e2e74] focus:border-[#6b70c4] focus:outline-none">
                                                </div>
                                            @endforeach
                                            <div>
                                                <label for="proposal-description" class="mb-1 block text-xs font-semibold text-[#4a4fa8]">{{ __('Detalles') }}</label>
                                                <textarea id="proposal-description" name="description" rows="3" required class="w-full rounded-lg border border-[#dddeff] px-3 py-2 text-sm text-[#1e2e74] focus:border-[#6b70c4] focus:outline-none">{{ old('description') }}</textarea>
                                            </div>
                                            <button type="submit" class="rounded-full bg-[#f5a520] px-5 py-2 text-sm font-semibold text-[#1e2e74] transition hover:bg-[#ffd978]">{{ __('Enviar propuesta') }}</button>
                                        </form>
                                    </div>
                                @endforelse
                            </div>
                        @else
                            <p class="text-sm text-[#4a4fa8]">{{ __('Selecciona un día para ver los eventos.') }}</p>
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
                const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
                body.style.overflow = 'hidden';
                if (scrollbarWidth > 0) body.style.paddingRight = `${scrollbarWidth}px`;
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') window.location.href = '{{ route('shop.events.index', ['month' => $currentMonth->format('Y-m')]) }}';
                });
            })();
        </script>
    @endif

</x-layouts::shop>
