<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::pluck('id');

        Product::factory()
            ->count(20)
            ->make()
            ->each(function (Product $product) use ($categories) {
                $product->category_id = $categories->random();
                $product->save();
            });
    }
}
