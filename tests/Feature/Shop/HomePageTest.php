<?php

namespace Tests\Feature\Shop;

use App\Models\Event;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class HomePageTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_home_shows_latest_ten_featured_products_only(): void
    {
        $baseTime = Carbon::parse('2026-01-01 12:00:00');
        $createdFeatured = collect();

        for ($i = 1; $i <= 12; $i++) {
            $createdFeatured->push(Product::factory()->create([
                'name' => "Featured Product {$i}",
                'is_active' => true,
                'is_featured' => true,
                'stock' => 10,
                'updated_at' => $baseTime->copy()->addMinutes($i),
            ]));
        }

        Product::factory()->create([
            'name' => 'Inactive Featured Product',
            'is_active' => false,
            'is_featured' => true,
            'stock' => 10,
            'updated_at' => $baseTime->copy()->addMinutes(99),
        ]);

        Product::factory()->create([
            'name' => 'Out Of Stock Featured Product',
            'is_active' => true,
            'is_featured' => true,
            'stock' => 0,
            'updated_at' => $baseTime->copy()->addMinutes(100),
        ]);

        Product::factory()->create([
            'name' => 'Regular Product',
            'is_active' => true,
            'is_featured' => false,
            'stock' => 10,
            'updated_at' => $baseTime->copy()->addMinutes(101),
        ]);

        $expected = $createdFeatured
            ->sortByDesc('updated_at')
            ->take(10)
            ->values();
        $excluded = $createdFeatured
            ->sortBy('updated_at')
            ->take(2)
            ->values();

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder($expected->pluck('name')->all());

        foreach ($expected as $product) {
            $response->assertSee(route('shop.products.show', $product), false);
        }

        foreach ($excluded as $product) {
            $response->assertDontSee(route('shop.products.show', $product), false);
        }

        $response->assertDontSee('Inactive Featured Product');
        $response->assertDontSee('Out Of Stock Featured Product');
        $response->assertDontSee('Regular Product');
    }

    public function test_home_includes_featured_products_slider_markup(): void
    {
        Product::factory()->create([
            'is_active' => true,
            'is_featured' => true,
            'stock' => 5,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('data-featured-slider', false)
            ->assertSee('data-slider-prev', false)
            ->assertSee('data-slider-next', false);
    }

    public function test_home_shows_current_week_published_and_approved_events_only(): void
    {
        Carbon::setTestNow('2026-04-30 10:00:00');

        $inWeekOne = Event::factory()->create([
            'name' => 'Evento Semana Uno',
            'start_at' => Carbon::now()->startOfWeek()->addDay()->setTime(18, 0),
            'end_at' => Carbon::now()->startOfWeek()->addDay()->setTime(20, 0),
            'is_published' => true,
            'is_approved' => true,
        ]);

        $inWeekTwo = Event::factory()->create([
            'name' => 'Evento Semana Dos',
            'start_at' => Carbon::now()->endOfWeek()->subDay()->setTime(17, 0),
            'end_at' => Carbon::now()->endOfWeek()->subDay()->setTime(19, 0),
            'is_published' => true,
            'is_approved' => true,
        ]);

        $outOfWeek = Event::factory()->create([
            'name' => 'Evento Fuera de Semana',
            'start_at' => Carbon::now()->endOfWeek()->addDay()->setTime(12, 0),
            'end_at' => Carbon::now()->endOfWeek()->addDay()->setTime(14, 0),
            'is_published' => true,
            'is_approved' => true,
        ]);

        $notPublished = Event::factory()->create([
            'name' => 'Evento No Publicado',
            'start_at' => Carbon::now()->startOfWeek()->addDays(2)->setTime(12, 0),
            'end_at' => Carbon::now()->startOfWeek()->addDays(2)->setTime(14, 0),
            'is_published' => false,
            'is_approved' => true,
        ]);

        $notApproved = Event::factory()->create([
            'name' => 'Evento No Aprobado',
            'start_at' => Carbon::now()->startOfWeek()->addDays(3)->setTime(12, 0),
            'end_at' => Carbon::now()->startOfWeek()->addDays(3)->setTime(14, 0),
            'is_published' => true,
            'is_approved' => false,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSeeInOrder([$inWeekOne->name, $inWeekTwo->name]);
        $response->assertSee('day='.$inWeekOne->start_at->toDateString(), false);
        $response->assertSee('month='.$inWeekOne->start_at->format('Y-m'), false);
        $response->assertSee('day='.$inWeekTwo->start_at->toDateString(), false);
        $response->assertSee('month='.$inWeekTwo->start_at->format('Y-m'), false);
        $response->assertDontSee($outOfWeek->name);
        $response->assertDontSee($notPublished->name);
        $response->assertDontSee($notApproved->name);
    }

    public function test_home_includes_weekly_events_slider_markup(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="weekly-events"', false)
            ->assertSee('data-slider="events"', false)
            ->assertSee('data-slider-prev="events"', false)
            ->assertSee('data-slider-next="events"', false);
    }

    public function test_home_shows_no_events_message_for_days_without_events(): void
    {
        Carbon::setTestNow('2026-04-30 10:00:00');

        Event::factory()->create([
            'start_at' => Carbon::now()->startOfWeek()->addDay()->setTime(18, 0),
            'end_at' => Carbon::now()->startOfWeek()->addDay()->setTime(20, 0),
            'is_published' => true,
            'is_approved' => true,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee(__('No hay eventos este día'));
    }

    public function test_home_includes_shop_information_section(): void
    {
        config()->set('shop.location.address', 'Calle 123, Ciudad');
        config()->set('shop.location.maps_link', 'https://maps.google.com/?q=Calle+123');
        config()->set('shop.contact.email', 'tienda@example.com');
        config()->set('shop.contact.phone', '+52 55 1111 2222');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('id="shop-info"', false)
            ->assertSee(__('Información de la tienda'))
            ->assertSee(__('Quiénes somos'))
            ->assertSee(__('Dónde encontrarnos'))
            ->assertSee(__('Contacto'))
            ->assertSee('Calle 123, Ciudad')
            ->assertSee('https://maps.google.com/?q=Calle+123', false)
            ->assertSee('tienda@example.com')
            ->assertSee('+52 55 1111 2222');
    }

    public function test_home_renders_google_map_if_embed_url_is_configured(): void
    {
        config()->set('shop.location.maps_embed_url', 'https://www.google.com/maps/embed?pb=test-map');

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('https://www.google.com/maps/embed?pb=test-map', false)
            ->assertSee('title="'.__('Ubicación en Google Maps').'"', false);
    }
}
