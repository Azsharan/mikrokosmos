<?php

namespace Tests\Feature\Shop;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_product_details(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'stock' => 5,
            'price' => 49.99,
        ]);

        $this->get(route('shop.products.show', $product))
            ->assertOk()
            ->assertSee($product->name)
            ->assertSee(number_format(49.99, 2))
            ->assertSee(__('Detalles del producto'))
            ->assertSee(__('Reserva en línea'))
            ->assertSee(__('Enviar y confirmar reserva'));
    }

    public function test_inactive_products_return_not_found(): void
    {
        $product = Product::factory()->create([
            'is_active' => false,
            'stock' => 10,
        ]);

        $this->get(route('shop.products.show', $product))->assertNotFound();
    }

    public function test_featured_products_link_to_detail_page(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'is_featured' => true,
            'stock' => 5,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('shop.products.show', $product), false);
    }

    public function test_product_page_displays_uploaded_image(): void
    {
        $product = Product::factory()->create([
            'is_active' => true,
            'image_path' => 'product-images/example.jpg',
        ]);

        $this->get(route('shop.products.show', $product))
            ->assertOk()
            ->assertSee(asset('storage/product-images/example.jpg'), false);
    }
}
