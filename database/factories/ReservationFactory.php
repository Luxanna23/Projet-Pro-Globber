<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Calendrier;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Reservation>
 */
class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = Carbon::now()->addDays($this->faker->numberBetween(1, 30));
        $end = (clone $start)->addDays($this->faker->numberBetween(2, 7));

        return [
            'price' => $this->faker->numberBetween(200, 1000),
            'start_date' => $start,
            'end_date' => $end,
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
            'annonce_id' => Annonce::inRandomOrder()->first()?->id ?? Annonce::factory(),
            'calendrier_id' => null,
            'status' => $this->faker->randomElement(['pending', 'accepted', 'refused', 'cancelled']),
            'last_read_at' => now(),
            'has_commented' => false,
        ];
    }
}
