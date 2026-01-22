<?php

namespace Tests\Feature\Shop;

use App\Mail\ReservationConfirmationMail;
use App\Models\Product;
use App\Models\Reservation;
use App\Models\ShopUser;
use App\Models\User;
use App\Notifications\NewReservationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ProductReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_submit_a_reservation_request(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $payload = [
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'phone' => '+34 600 111 222',
            'quantity' => 2,
            'notes' => 'Me interesa recogerlo el fin de semana.',
        ];

        Mail::fake();

        $this->from(route('shop.products.show', $product))
            ->post(route('shop.products.reserve', $product), $payload)
            ->assertRedirect(route('shop.products.show', $product))
            ->assertSessionHas('reservation_status');

        $this->assertDatabaseHas('reservations', [
            'product_id' => $product->id,
            'name' => 'Ada Lovelace',
            'email' => 'ada@example.com',
            'quantity' => 2,
        ]);

        $reservation = Reservation::firstWhere('email', 'ada@example.com');
        $this->assertNotNull($reservation?->code);
        $this->assertMatchesRegularExpression('/^[A-Z0-9]{8}$/', $reservation->code);

        Mail::assertSent(ReservationConfirmationMail::class, function ($mail) use ($reservation) {
            return $mail->reservation->is($reservation);
        });
    }

    public function test_reservation_requires_valid_contact_details(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        $this->from(route('shop.products.show', $product))
            ->post(route('shop.products.reserve', $product), [
                'name' => '',
                'email' => 'invalid-email',
                'quantity' => 0,
            ])
            ->assertRedirect(route('shop.products.show', $product))
            ->assertSessionHasErrors(['name', 'email', 'quantity']);

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_inactive_products_cannot_receive_reservations(): void
    {
        $product = Product::factory()->create(['is_active' => false]);

        $this->post(route('shop.products.reserve', $product), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'quantity' => 1,
        ])->assertNotFound();

        $this->assertDatabaseCount('reservations', 0);
    }

    public function test_admins_receive_notification_when_reservation_is_created(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $admin = User::factory()->create();

        Mail::fake();
        Notification::fake();

        $this->post(route('shop.products.reserve', $product), [
            'name' => 'Carla',
            'email' => 'carla@example.com',
            'quantity' => 1,
        ])->assertRedirect();

        Notification::assertSentTo(
            $admin,
            NewReservationNotification::class,
            function ($notification) {
                return $notification->reservation instanceof Reservation;
            }
        );
    }

    public function test_authenticated_shop_user_uses_profile_details_for_reservation(): void
    {
        $product = Product::factory()->create(['is_active' => true]);
        $shopUser = ShopUser::factory()->create([
            'name' => 'Cliente Preferente',
            'email' => 'preferente@example.com',
            'phone' => '+34 600 222 333',
        ]);

        User::factory()->create(); // Ensure at least one admin exists for notifications

        Mail::fake();
        Notification::fake();

        $this->actingAs($shopUser, 'shop')
            ->from(route('shop.products.show', $product))
            ->post(route('shop.products.reserve', $product), [
                'quantity' => 3,
                'notes' => 'Prefiero recogerlo el viernes',
            ])
            ->assertRedirect(route('shop.products.show', $product));

        $reservation = Reservation::first();
        $this->assertSame('Cliente Preferente', $reservation->name);
        $this->assertSame('preferente@example.com', $reservation->email);
        $this->assertSame('+34 600 222 333', $reservation->phone);

        Mail::assertSent(ReservationConfirmationMail::class, function ($mail) use ($reservation) {
            return $mail->reservation->is($reservation);
        });
    }
}
