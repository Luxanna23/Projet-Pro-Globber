<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AnnonceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();  
        for ($i = 0; $i < 10; $i++) {
            $annonce = \App\Models\Annonce::create([
                'title' => "Appart a Paris",
                'description' => "Appart trop cool a Paris",
                'price_per_night' => $faker->randomFloat(2, 100, 1000),
                'address' => "1 rue de la paix, Paris",
                'postal_code' => "75001",
                'city' => "Paris",
                'country' => "France",
                'user_id' => 1,
            ]);

            \App\Models\AnnonceImage::create([
                'path' => "1.jpg ",
                'annonce_id' => $annonce->id,
            ]);

            \App\Models\AnnonceImage::create([
                'path' => "2.jpg ",
                'annonce_id' => $annonce->id,
            ]);
        }
    }
}
