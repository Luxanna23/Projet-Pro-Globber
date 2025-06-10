<?php

namespace Tests\Integration;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalendrierDecoupageTest extends TestCase
{
    use RefreshDatabase;

    public function test_disponibilite_est_decoupee_apres_reservation()
    {
        $owner = User::factory()->create();
        $user = User::factory()->create();
        $this->actingAs($user);

        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        // Disponibilité initiale : 10 → 20
        $dispo = Calendrier::create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(20),
        ]);

        // Réservation sur une partie : 12 → 15
        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(12)->toDateString(),
            'end_date' => now()->addDays(15)->toDateString(),
        ]);

        $response->assertRedirect();

        // L’ancienne dispo est supprimée
        $this->assertDatabaseMissing('calendriers', [
            'id' => $dispo->id,
        ]);

        // Nouvelle dispo 1 : 10 → 12
        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'type' => 'disponible',
        ]);

        // Nouvelle dispo 2 : 15 → 20
        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'start_date' => now()->addDays(15)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'type' => 'disponible',
        ]);
    }
}