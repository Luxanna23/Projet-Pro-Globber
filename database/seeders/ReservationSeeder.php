<?php

namespace Database\Seeders;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = ['pending', 'accepted', 'refused', 'cancelled'];

        // On récupère quelques annonces et un user (à adapter si besoin)
        $annonces = Annonce::inRandomOrder()->take(5)->get();
        $user = User::first(); 

        foreach ($statuses as $index => $status) {
            $annonce = $annonces[$index % $annonces->count()];
            $startDate = Carbon::now()->addDays(5 + $index);
            $endDate = (clone $startDate)->addDays(3);

            // Créer une entrée calendrier dispo liée à cette période
            $calendrier = Calendrier::create([
                'annonce_id' => $annonce->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'type' => 'disponible',
            ]);

            // Créer la réservation à venir
            Reservation::create([
                'price' => $annonce->price_per_night * 3,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'user_id' => $user->id,
                'annonce_id' => $annonce->id,
                'calendrier_id' => $calendrier->id,
                'status' => $status,
                'last_read_at' => now(),
                'has_commented' => false,
            ]);
        }

        // Réservation passée acceptée
        $pastAnnonce = $annonces->first();
        $pastStart = Carbon::now()->subDays(10);
        $pastEnd = (clone $pastStart)->addDays(3);

        $pastCalendrier = Calendrier::create([
            'annonce_id' => $pastAnnonce->id,
            'start_date' => $pastStart,
            'end_date' => $pastEnd,
            'type' => 'réservé',
        ]);

        Reservation::create([
            'price' => $pastAnnonce->price_per_night * 3,
            'start_date' => $pastStart,
            'end_date' => $pastEnd,
            'user_id' => $user->id,
            'annonce_id' => $pastAnnonce->id,
            'calendrier_id' => $pastCalendrier->id,
            'status' => 'accepted',
            'last_read_at' => now(),
            'has_commented' => false,
        ]);

    }
}
