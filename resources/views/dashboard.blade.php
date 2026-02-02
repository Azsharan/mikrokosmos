<x-layouts::app :title="__('Dashboard')">
    @php
        $weekDays = array_map(fn ($day) => __($day), ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun']);
        $days = iterator_to_array($calendarPeriod);
        $today = now()->startOfDay();
        $weekStart = $today->copy()->startOfWeek(\Carbon\CarbonInterface::MONDAY);
        $weekEnd = $weekStart->copy()->addDays(6);
        $mobileDays = collect($days)
            ->filter(fn ($date) => $date->betweenIncluded($weekStart, $weekEnd))
            ->values();
    @endphp

    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">
        <div class="flex flex-col gap-2 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-sm font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                    {{ __('Events overview') }}
                </p>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                    {{ $currentMonth->translatedFormat('F Y') }}
                </h1>
            </div>
            <div class="rounded-lg border border-neutral-200 px-4 py-2 text-sm text-neutral-600 dark:border-neutral-700 dark:text-neutral-300">
                {{ __('Showing :count scheduled events', ['count' => collect($eventsByDate)->flatten()->count()]) }}
            </div>
        </div>

        <div class="rounded-xl border border-neutral-200 bg-white p-4 shadow-sm dark:border-neutral-700 dark:bg-neutral-900">
            <div class="hidden lg:block">
                <div class="mb-2 grid grid-cols-7 text-center text-xs font-semibold uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                    @foreach ($weekDays as $day)
                        <div>{{ $day }}</div>
                    @endforeach
                </div>
                <div class="grid grid-cols-7 gap-2 text-sm">
                    @foreach ($days as $date)
                        @php
                            $dateKey = $date->toDateString();
                            $eventsForDay = collect($eventsByDate[$dateKey] ?? []);
                            $isCurrentMonth = $date->isSameMonth($currentMonth);
                            $isToday = $date->isSameDay($today);
                        @endphp
                        <div @class([
                            'min-h-[8rem] rounded-lg border p-2 transition-colors',
                            'border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900' => $isCurrentMonth,
                            'border-dashed border-neutral-200/70 bg-neutral-50 text-neutral-400 dark:border-neutral-700/70 dark:bg-neutral-900/60' => !$isCurrentMonth,
                            'ring-2 ring-primary-500' => $isToday,
                        ])>
                            <div class="mb-1 flex items-center justify-between text-xs font-semibold">
                                <span>{{ $date->format('j') }}</span>
                                @if ($eventsForDay->isNotEmpty())
                                    <span class="rounded-full bg-primary-100 px-2 py-0.5 text-[10px] font-semibold text-primary-700 dark:bg-primary-500/20 dark:text-primary-100">
                                        {{ $eventsForDay->count() }} {{ __('evt') }}
                                    </span>
                                @endif
                            </div>
                            <div class="space-y-1">
                                @forelse ($eventsForDay as $event)
                                    <div class="rounded-md border border-primary-100 bg-primary-50 px-2 py-1 text-xs text-primary-900 dark:border-primary-500/30 dark:bg-primary-500/20 dark:text-primary-100">
                                        <p class="font-semibold leading-tight">{{ \Illuminate\Support\Str::limit($event->name, 32) }}</p>
                                        <p class="text-[10px] uppercase tracking-wide text-primary-700 dark:text-primary-200">
                                            {{ $event->start_at->format('H:i') }}
                                            @if ($event->end_at)
                                                — {{ $event->end_at->format('H:i') }}
                                            @endif
                                        </p>
                                    </div>
                                @empty
                                    <p class="text-[11px] italic text-neutral-400 dark:text-neutral-500">
                                        {{ __('No events') }}
                                    </p>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="space-y-3 lg:hidden">
                @forelse ($mobileDays as $date)
                    @php
                        $dateKey = $date->toDateString();
                        $eventsForDay = collect($eventsByDate[$dateKey] ?? []);
                        $isCurrentMonth = $date->isSameMonth($currentMonth);
                        $isToday = $date->isSameDay($today);
                    @endphp

                    @continue(!$isCurrentMonth && $eventsForDay->isEmpty())

                    <div @class([
                        'rounded-xl border px-3 py-3 shadow-sm',
                        'border-primary-200 bg-primary-50/30 dark:border-primary-500/30 dark:bg-primary-500/10' => $isToday,
                        'border-neutral-200 bg-white dark:border-neutral-700 dark:bg-neutral-900' => ! $isToday,
                    ])>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-neutral-500 dark:text-neutral-400">
                                    {{ $date->translatedFormat('D') }}
                                </p>
                                <p class="text-lg font-semibold text-neutral-900 dark:text-white">
                                    {{ $date->format('j') }}
                                </p>
                                <p class="text-xs text-neutral-500 dark:text-neutral-400">
                                    {{ $date->translatedFormat('F') }}
                                </p>
                            </div>
                            @if ($eventsForDay->isNotEmpty())
                                <span class="rounded-full bg-primary-100 px-3 py-1 text-xs font-semibold text-primary-700 dark:bg-primary-500/20 dark:text-primary-100">
                                    {{ trans_choice(':count event|:count events', $eventsForDay->count(), ['count' => $eventsForDay->count()]) }}
                                </span>
                            @else
                                <span class="rounded-full bg-neutral-100 px-3 py-1 text-xs font-medium text-neutral-500 dark:bg-neutral-800 dark:text-neutral-300">
                                    {{ __('No events') }}
                                </span>
                            @endif
                        </div>

                        <div class="mt-3 space-y-2">
                            @forelse ($eventsForDay as $event)
                                <div class="rounded-lg border border-primary-100 bg-primary-50 px-3 py-2 text-sm text-primary-900 dark:border-primary-500/30 dark:bg-primary-500/20 dark:text-primary-100">
                                    <p class="font-semibold">{{ $event->name }}</p>
                                    <p class="text-xs uppercase tracking-wide text-primary-700 dark:text-primary-200">
                                        {{ $event->start_at->format('H:i') }}
                                        @if ($event->end_at)
                                            — {{ $event->end_at->format('H:i') }}
                                        @endif
                                    </p>
                                </div>
                            @empty
                                <p class="text-xs italic text-neutral-500 dark:text-neutral-400">
                                    {{ __('Enjoy a free day!') }}
                                </p>
                            @endforelse
                        </div>
                    </div>
                @empty
                    <p class="text-center text-sm text-neutral-500 dark:text-neutral-400">
                        {{ __('No events scheduled for this week.') }}
                    </p>
                @endforelse
            </div>
        </div>
    </div>
</x-layouts::app>
