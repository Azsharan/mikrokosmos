<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function show(Category $category): View
    {
        abort_unless($category->is_active, 404);

        $products = Product::query()
            ->where('is_active', true)
            ->where('category_id', $category->id)
            ->orderByDesc('is_featured')
            ->orderByDesc('stock')
            ->orderBy('name')
            ->get();

        return view('shop.categories.show', [
            'category' => $category,
            'products' => $products,
        ]);
    }
}
