<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Models\Comment;

class AnnonceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villes = [
            'Paris', 'London', 'Berlin', 'Rome', 'Madrid', 'Lisbon',
            'Ottawa', 'Washington', 'Tokyo', 'Beijing', 'Seoul', 'Moscow',
            'Monaco', 'Brasilia', 'Marrakech', 'Cairo',
            'Bangkok', 'Singapore', 'Dubai', 'Istanbul', 'Amsterdam',
        ];
        
        $faker = \Faker\Factory::create();  
        for ($i = 0; $i < 50; $i++) {
            $annonce = \App\Models\Annonce::create([
                'title' => "Appart 2 pièces à louer",
                'description' => $faker->paragraph(1),
                'price_per_night' => $faker->randomFloat(2, 100, 600),
                'address' => $faker->streetAddress,
                'postal_code' => "75001",
                'city' => $faker->randomElement($villes),
                'country' => $faker->country,
                'country_code' => $faker->countryCode,
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

            //creer 2 commentaires pour l'annonce
            Comment::create([
                'user_id' => 3,
                'annonce_id' => $annonce->id,
                'content' => 'Merci pour cette expérience incroyable ! Tout était parfait, de l\'accueil à la propreté de l\'annonce. Je recommande vivement !',
                'rating' => rand(3, 5), 
            ]);

            // Comment::create([
            //     'user_id' => 4,
            //     'annonce_id' => $annonce->annonce_id,
            //     'content' => 'Génial ! On a passé un super weekend, l\'appartement est très bien situé et confortable.',
            //     'rating' => rand(3, 5), 
            // ]);
 
        }
    }
}
