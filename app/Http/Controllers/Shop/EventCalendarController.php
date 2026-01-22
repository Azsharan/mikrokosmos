<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;

class EventCalendarController extends Controller
{
    public function __invoke(): View
    {
        $requestedMonth = request('month');
        $currentMonth = Carbon::now()->startOfMonth();

        if ($requestedMonth) {
            try {
                $currentMonth = Carbon::createFromFormat('Y-m', $requestedMonth)->startOfMonth();
            } catch (\Exception $e) {
                $currentMonth = Carbon::now()->startOfMonth();
            }
        }
        $calendarStart = $currentMonth->copy()->startOfWeek();
        $calendarEnd = $currentMonth->copy()->endOfMonth()->endOfWeek();

        $events = Event::query()
            ->where('is_published', true)
            ->whereBetween('start_at', [$calendarStart->copy()->startOfDay(), $calendarEnd->copy()->endOfDay()])
            ->orderBy('start_at')
            ->with('eventType')
            ->withCount('registrations')
            ->get();

        $eventsByDate = $events->groupBy(fn ($event) => $event->start_at->toDateString());

        $selectedDate = null;
        if ($selectedDay = request('day')) {
            try {
                $selectedDate = Carbon::createFromFormat('Y-m-d', $selectedDay);
            } catch (\Exception $e) {
                $selectedDate = null;
            }
        } elseif ($eventsByDate->isNotEmpty()) {
            $selectedDate = Carbon::parse($eventsByDate->keys()->first());
        }

        if ($selectedDate && ! $selectedDate->betweenIncluded($calendarStart, $calendarEnd)) {
            $selectedDate = null;
        }

        $selectedDayEvents = $selectedDate
            ? ($eventsByDate[$selectedDate->toDateString()] ?? collect())
            : collect();

        $userRegisteredEventIds = [];
        if ($user = auth('shop')->user()) {
            $userRegisteredEventIds = $user->eventRegistrations()->pluck('event_id')->all();
        }

        return view('shop.events.calendar', [
            'currentMonth' => $currentMonth,
            'calendarPeriod' => CarbonPeriod::create($calendarStart, $calendarEnd),
            'eventsByDate' => $eventsByDate,
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
            'selectedDate' => $selectedDate,
            'selectedDayEvents' => $selectedDayEvents,
            'userRegisteredEventIds' => $userRegisteredEventIds,
        ]);
    }
}
