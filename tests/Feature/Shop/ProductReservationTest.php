<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
}
