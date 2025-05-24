<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Annonce>
 */
class AnnonceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $villes = [
            'Paris', 'London', 'Berlin', 'Rome', 'Madrid', 'Lisbon',
            'Ottawa', 'Washington', 'Tokyo', 'Beijing', 'Seoul', 'Moscow',
            'Monaco', 'Brasilia', 'Marrakech', 'Cairo', 'New Delhi',
            'Bangkok', 'Singapore', 'Dubai', 'Istanbul', 'Amsterdam',
        ];

        return [
            'title' => "Appart 2 pièces à louer",
            'description' => $this->faker->paragraph(),
            'address' => $this->faker->address(),
            'postal_code' => $this->faker->postcode(),
            'city' => $this->faker->randomElement($villes),
            'country' => $this->faker->country(),
            'price_per_night' => $this->faker->numberBetween(50, 500),
            'user_id' => 2,
        ];
    }
}
