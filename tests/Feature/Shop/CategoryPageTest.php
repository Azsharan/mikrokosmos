<?php

namespace Tests\Feature\Shop;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_can_view_category_products(): void
    {
        $category = Category::factory()->create(['is_active' => true]);
        $products = Product::factory()->count(2)->create([
            'category_id' => $category->id,
            'is_active' => true,
        ]);

        $this->get(route('shop.categories.show', $category))
            ->assertOk()
            ->assertSee($category->name)
            ->assertSee($products->first()->name)
            ->assertSee(__('Productos disponibles'));
    }

    public function test_inactive_categories_return_not_found(): void
    {
        $category = Category::factory()->create(['is_active' => false]);

        $this->get(route('shop.categories.show', $category))->assertNotFound();
    }

    public function test_home_categories_link_to_detail_page(): void
    {
        $category = Category::factory()->create();

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(route('shop.categories.show', $category), false);
    }
}
