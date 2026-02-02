<?php

namespace Tests\Feature\Shop;

use App\Models\ShopUser;
use App\Models\TableReservation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TableReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_access_table_reservation_page(): void
    {
        $this->get(route('shop.tables.index'))->assertRedirect(route('shop.login'));
    }

    public function test_shop_users_can_create_table_reservations(): void
    {
        $user = ShopUser::factory()->create();
        $start = Carbon::now()->addDay()->setHour(18)->minute(0);

        $this->actingAs($user, 'shop')
            ->post(route('shop.tables.store'), [
                'table_number' => 2,
                'reserved_for' => $start->format('Y-m-d H:i:s'),
                'party_size' => 4,
                'notes' => 'Traeremos Commander decks',
            ])
            ->assertRedirect(route('shop.tables.index'))
            ->assertSessionHas('table_reservation_status');

        $this->assertDatabaseHas('table_reservations', [
            'shop_user_id' => $user->id,
            'table_number' => 2,
            'party_size' => 4,
        ]);
    }

    public function test_cannot_exceed_table_capacity_when_slot_is_partially_full(): void
    {
        $existing = TableReservation::factory()->create([
            'table_number' => 1,
            'party_size' => 4,
            'reserved_for' => Carbon::now()->addDays(2)->setHour(15)->minute(0),
            'reserved_until' => Carbon::now()->addDays(2)->setHour(17)->minute(0),
        ]);

        $user = ShopUser::factory()->create();

        $this->actingAs($user, 'shop')
            ->from(route('shop.tables.index'))
            ->post(route('shop.tables.store'), [
                'table_number' => 1,
                'reserved_for' => $existing->reserved_for->format('Y-m-d H:i:s'),
                'party_size' => 3,
            ])
            ->assertSessionHasErrors(['reserved_for']);

        // but booking the remaining 2 seats should pass
        $this->actingAs($user, 'shop')
            ->from(route('shop.tables.index'))
            ->post(route('shop.tables.store'), [
                'table_number' => 1,
                'reserved_for' => $existing->reserved_for->format('Y-m-d H:i:s'),
                'party_size' => 2,
            ])
            ->assertRedirect(route('shop.tables.index'))
            ->assertSessionHas('table_reservation_status');
    }

    public function test_users_can_cancel_their_reservation(): void
    {
        $reservation = TableReservation::factory()->create([
            'status' => TableReservation::STATUS_PENDING,
        ]);

        $this->actingAs($reservation->shopUser, 'shop')
            ->delete(route('shop.tables.destroy', $reservation))
            ->assertRedirect(route('shop.tables.index'))
            ->assertSessionHas('table_reservation_status');

        $this->assertEquals(TableReservation::STATUS_CANCELLED, $reservation->fresh()->status);
    }
}
