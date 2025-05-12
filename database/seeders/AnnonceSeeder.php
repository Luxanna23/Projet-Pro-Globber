<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
                'title' => "Appart 2 pièces à Paris",
                'description' => "Magnifiique appartement a Paris vu sur la tour Eiffel, proche de tous les commerces et transports en commun.",
                'price_per_night' => $faker->randomFloat(2, 100, 600),
                'address' => "1 rue de la paix, Paris",
                'postal_code' => "75001",
                'city' => "Paris",
                'country' => "France",
                'user_id' => 2,
            ]);

            \App\Models\AnnonceImage::create([
                'path' => rand(1, 2).".jpg ",
                'annonce_id' => $annonce->id,
            ]);

            \App\Models\AnnonceImage::create([
                'path' => rand(1, 2).".jpg ",
                'annonce_id' => $annonce->id,
            ]);

            
            $start = Carbon::now()->addDays(rand(10, 30));
            $end = (clone $start)->addDays(rand(10, 20));

            \App\Models\Calendrier::create([
                    'annonce_id' => $annonce->id,
                    'start_date' => $start,
                    'end_date' => $end,
                    'type' => 'disponible',
            ]);
            
        }
    }
}
