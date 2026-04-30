<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public shop landing page.
     */
    public function __invoke(): View
    {
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        $featuredProducts = Product::query()
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->where('is_featured', true)
            ->orderByDesc('updated_at')
            ->with('category')
            ->take(10)
            ->get();

        $weeklyEvents = Event::query()
            ->where('is_published', true)
            ->where('is_approved', true)
            ->whereBetween('start_at', [$weekStart->copy()->startOfDay(), $weekEnd->copy()->endOfDay()])
            ->orderBy('start_at')
            ->with('eventType')
            ->get();
        $eventsByDate = $weeklyEvents->groupBy(fn (Event $event) => $event->start_at->toDateString());
        $weeklyDays = collect(range(0, 6))->map(function (int $offset) use ($weekStart, $eventsByDate) {
            $date = $weekStart->copy()->addDays($offset);

            return [
                'date' => $date,
                'events' => $eventsByDate->get($date->toDateString(), collect()),
            ];
        });

        $highlightsPath = base_path('description');

        $highlights = collect(file_exists($highlightsPath) ? file($highlightsPath, FILE_IGNORE_NEW_LINES) : [])
            ->map(fn (string $line) => trim($line))
            ->filter()
            ->values();

        $tagline = $highlights->shift() ?: __('Your geek universe in one place');

        return view('shop.home', [
            'tagline' => $tagline,
            'sellingPoints' => $highlights,
            'featuredProducts' => $featuredProducts,
            'weeklyDays' => $weeklyDays,
        ]);
    }
}
