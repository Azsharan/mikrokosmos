<?php

namespace Tests\Feature\Shop;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\ShopUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class EventRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_register_for_event(): void
    {
        $user = ShopUser::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'capacity' => 5,
            'start_at' => Carbon::now()->addDays(2),
        ]);

        $response = $this->actingAs($user, 'shop')->post(route('shop.events.register', $event));

        $response->assertRedirect();
        $response->assertSessionHas('event_registration_status');
        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'shop_user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_register_twice(): void
    {
        $user = ShopUser::factory()->create();
        $event = Event::factory()->create([
            'is_published' => true,
            'capacity' => 2,
            'start_at' => Carbon::now()->addDays(3),
        ]);

        EventRegistration::factory()->create([
            'event_id' => $event->id,
            'shop_user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'shop')->post(route('shop.events.register', $event));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['event'], null, 'eventRegistration');
        $this->assertCount(1, EventRegistration::where('event_id', $event->id)->where('shop_user_id', $user->id)->get());
    }

    public function test_registration_respects_capacity(): void
    {
        $event = Event::factory()->create([
            'is_published' => true,
            'capacity' => 1,
            'start_at' => Carbon::now()->addDays(1),
        ]);

        EventRegistration::factory()->create([
            'event_id' => $event->id,
        ]);

        $user = ShopUser::factory()->create();

        $response = $this->actingAs($user, 'shop')->post(route('shop.events.register', $event));

        $response->assertRedirect();
        $response->assertSessionHasErrors(['event'], null, 'eventRegistration');
        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'shop_user_id' => $user->id,
        ]);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $event = Event::factory()->create();

        $response = $this->post(route('shop.events.register', $event));

        $response->assertRedirect(route('shop.login'));
    }
}
