<?php

namespace Tests\Feature\Shop;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventCalendarTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow('2024-07-15');
    }

    public function test_calendar_shows_published_events(): void
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'start_at' => Carbon::now()->startOfMonth()->addDays(2)->setTime(18, 0),
        ]);

        $this->get(route('shop.events.index'))
            ->assertOk()
            ->assertSee($event->name);
    }

    public function test_users_can_view_future_months(): void
    {
        $futureMonth = Carbon::now()->addMonths(2)->startOfMonth();
        $event = Event::factory()->create([
            'is_published' => true,
            'start_at' => $futureMonth->copy()->addDays(4)->setTime(17, 30),
        ]);

        $this->get(route('shop.events.index', ['month' => $futureMonth->format('Y-m')]))
            ->assertOk()
            ->assertSee($event->name);
    }

    public function test_unpublished_events_are_hidden(): void
    {
        $event = Event::factory()->create([
            'is_published' => false,
            'start_at' => Carbon::now()->startOfMonth()->addDays(3),
        ]);

        $this->get(route('shop.events.index'))
            ->assertOk()
            ->assertDontSee($event->name);
    }

    public function test_clicking_day_shows_event_details(): void
    {
        $eventDate = Carbon::now()->startOfMonth()->addDays(4)->setTime(16, 0);
        $event = Event::factory()->create([
            'is_published' => true,
            'start_at' => $eventDate,
            'end_at' => $eventDate->copy()->addHours(2),
            'location' => 'Main Store',
            'is_online' => false,
        ]);

        $this->get(route('shop.events.index', [
            'month' => $eventDate->format('Y-m'),
            'day' => $eventDate->toDateString(),
        ]))
            ->assertOk()
            ->assertSee($event->name)
            ->assertSee('Main Store');
    }
}
