<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public shop landing page.
     */
    public function __invoke(): View
    {
        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->take(6)
            ->get();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->orderByDesc('is_featured')
            ->orderByDesc('updated_at')
            ->with('category')
            ->take(8)
            ->get();

        $highlightsPath = base_path('description');

        $highlights = collect(file_exists($highlightsPath) ? file($highlightsPath, FILE_IGNORE_NEW_LINES) : [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        $tagline = $highlights->shift() ?: __('Your geek universe in one place');

        return view('shop.home', [
            'tagline' => $tagline,
            'sellingPoints' => $highlights,
            'categories' => $categories,
            'featuredProducts' => $featuredProducts,
        ]);
    }
}
