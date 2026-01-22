<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        $start = $this->faker->dateTimeBetween('+1 days', '+1 month');
        $end = (clone $start)->modify('+2 hours');

        return [
            'name' => $name,
            'type' => $this->faker->randomElement(['casual', 'tournament', 'workshop', 'community', 'online']),
            'event_type_id' => EventType::factory(),
            'category_id' => Category::factory(),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'description' => $this->faker->paragraph(),
            'start_at' => $start,
            'end_at' => $end,
            'location' => $this->faker->address(),
            'is_online' => $this->faker->boolean,
            'capacity' => $this->faker->numberBetween(10, 100),
            'is_published' => true,
            'cover_image' => null,
            'metadata' => ['sponsor' => $this->faker->company()],
        ];
    }
}
