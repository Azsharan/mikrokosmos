<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
            ->where('is_approved', true)
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
        }

        $calendarReferenceDate = null;
        if ($requestedWeek = request('week')) {
            try {
                $calendarReferenceDate = Carbon::createFromFormat('Y-m-d', $requestedWeek)->startOfWeek();
            } catch (\Exception $e) {
                $calendarReferenceDate = null;
            }
        }

        if (! $calendarReferenceDate) {
            $calendarReferenceDate = $selectedDate ? $selectedDate->copy() : now()->startOfWeek();
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
            'calendarReferenceDate' => $calendarReferenceDate,
            'userRegisteredEventIds' => $userRegisteredEventIds,
        ]);
    }

    public function suggest(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('eventProposal', [
            'date' => ['required', 'date'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255'],
            'title' => ['required', 'string', 'max:160'],
            'description' => ['required', 'string', 'max:1200'],
        ]);


        $proposalDate = $validated['date'];
        [$proposalYear, $proposalMonth, $proposalDay] = array_map('intval', explode('-', $proposalDate));
        $slug = $this->generateUniqueEventSlug($validated['title']);

        Event::query()->create([
            'name' => $validated['title'],
            'slug' => $slug,
            'description' => $validated['description'],
            'type' => 'community',
            'start_at' => $proposalDate.' 00:00:00',
            'end_at' => $proposalDate.' 23:59:59',
            'is_online' => false,
            'is_published' => false,
            'is_approved' => false,
            'capacity' => 10,
            'metadata' => [
                'proposal_name' => $validated['name'],
                'proposal_email' => $validated['email'],
                'proposal_day' => $proposalDay,
                'proposal_month' => $proposalMonth,
                'proposal_year' => $proposalYear,
                'source' => 'calendar_form',
            ],
        ]);

        Log::info('Event proposal submitted', [
            'date' => $proposalDate,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'ip' => $request->ip(),
        ]);

        return redirect()->route('shop.events.index', [
            'month' => Carbon::parse($validated['date'])->startOfMonth()->format('Y-m'),
        ])->with('event_suggestion_status', __('¡Gracias por proponer este evento! Queda marcado como pendiente de aprobación.'));
    }

    private function generateUniqueEventSlug(string $title): string
    {
        $slug = Str::slug($title) ?: 'evento-propuesto';
        $baseSlug = Str::limit($slug, 210, '');
        $candidate = $baseSlug;
        $suffix = 1;

        while (Event::query()->where('slug', $candidate)->exists()) {
            $candidate = "{$baseSlug}-{$suffix}";
            $suffix++;
        }

        return $candidate;
    }
}
