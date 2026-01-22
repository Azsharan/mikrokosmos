<?php

namespace Tests\Feature\Shop;

use App\Models\ShopUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_customers_can_register_via_shop_guard(): void
    {
        $response = $this->post(route('shop.register.store'), [
            'name' => 'Cliente Uno',
            'email' => 'cliente@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shop.account', absolute: false));

        $this->assertAuthenticated('shop');
        $this->assertGuest();
        $this->assertDatabaseHas('shop_users', ['email' => 'cliente@example.com']);
    }

    public function test_customers_can_login_without_affecting_staff_session(): void
    {
        $shopUser = ShopUser::factory()->create([
            'email' => 'cliente@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post(route('shop.login.store'), [
            'email' => $shopUser->email,
            'password' => 'secret123',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('shop.account', absolute: false));

        $this->assertAuthenticated('shop');
        $this->assertGuest();
    }
}
