<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => $this->faker->paragraph(),
            'price' => $this->faker->randomFloat(2, 5, 200),
            'cost_price' => $this->faker->randomFloat(2, 2, 150),
            'stock' => $this->faker->numberBetween(0, 50),
            'category_id' => Category::factory(),
            'is_active' => true,
            'barcode' => $this->faker->unique()->ean13(),
            'image_path' => null,
            'is_featured' => $this->faker->boolean,
            'tags' => implode(',', $this->faker->words(3)),
            'meta_title' => $this->faker->sentence(3),
            'meta_description' => $this->faker->sentence(8),
            'meta_keywords' => implode(', ', $this->faker->words(5)),
        ];
    }
}
