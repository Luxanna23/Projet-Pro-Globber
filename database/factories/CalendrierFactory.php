<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Annonce;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Calendrier>
 */
class CalendrierFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $start = now()->addDays($this->faker->numberBetween(1, 5));
        $end = (clone $start)->addDays($this->faker->numberBetween(1, 5));

        return [
            'start_date' => $start,
            'end_date' => $end,
            'annonce_id' => Annonce::factory(), // crée une annonce si besoin
            'type' => $this->faker->randomElement(['disponible', 'réservé']),
        ];
    }
}
