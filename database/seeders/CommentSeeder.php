<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Reservation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // On récupère les réservations passées et acceptées
        $reservations = Reservation::where('status', 'accepted')
            ->where('end_date', '<', now())
            ->where('has_commented', false)
            ->get();

        foreach ($reservations as $reservation) {
            Comment::create([
                'user_id' => $reservation->user_id,
                'annonce_id' => $reservation->annonce_id,
                'content' => 'Merci pour cette expérience incroyable ! Tout était parfait, de l\'accueil à la propreté de l\'annonce. Je recommande vivement !',
                'rating' => rand(3, 5), // Tu peux varier si tu veux 1–5
            ]);

            // Marquer la réservation comme commentée
            $reservation->update(['has_commented' => true]);

            ///
            
            $reservation = Reservation::where('status', 'accepted')
                ->where('end_date', '<', now())
                ->first();

            if ($reservation) {
                Comment::factory()->create([
                    'user_id' => $reservation->user_id,
                    'annonce_id' => $reservation->annonce_id,
                ]);

                $reservation->update(['has_commented' => true]);
            }
        }
    }
}
