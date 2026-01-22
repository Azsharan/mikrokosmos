<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\EventType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventType>
 */
class EventTypeFactory extends Factory
{
    protected $model = EventType::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(2, true);

        return [
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'category_id' => Category::factory(),
            'description' => $this->faker->sentence(),
        ];
    }
}
