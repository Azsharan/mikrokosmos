<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Reservation>
 */
class ReservationFactory extends Factory
{
    protected $model = Reservation::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'name' => $this->faker->name(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'quantity' => $this->faker->numberBetween(1, 3),
            'status' => 'pending',
            'notes' => $this->faker->optional()->sentence(),
        ];
    }
}
