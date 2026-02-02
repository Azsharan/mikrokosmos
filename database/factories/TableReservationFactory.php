<?php

namespace Database\Factories;

use App\Models\TableReservation;
use App\Models\ShopUser;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @extends Factory<TableReservation>
 */
class TableReservationFactory extends Factory
{
    protected $model = TableReservation::class;

    public function definition(): array
    {
        $start = Carbon::instance($this->faker->dateTimeBetween('+1 day', '+10 days'))
            ->minute(0)
            ->second(0);
        $end = (clone $start)->addMinutes(TableReservation::SESSION_DURATION_MINUTES);

        return [
            'shop_user_id' => ShopUser::factory(),
            'table_number' => $this->faker->numberBetween(1, TableReservation::TOTAL_TABLES),
            'party_size' => $this->faker->numberBetween(2, TableReservation::MAX_PARTY_SIZE),
            'reserved_for' => $start,
            'reserved_until' => $end,
            'status' => TableReservation::STATUS_PENDING,
            'notes' => $this->faker->optional()->sentence(),
            'code' => strtoupper(Str::random(8)),
        ];
    }
}
