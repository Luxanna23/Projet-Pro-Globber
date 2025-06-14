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

        // j'initiialise des dispo sur mon annonce
        $dispo = Calendrier::create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(20),
        ]);

        // je reserve qlq jours sur mon annonce
        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(12)->toDateString(),
            'end_date' => now()->addDays(15)->toDateString(),
        ]);

        $response->assertRedirect();

        $this->assertDatabaseMissing('calendriers', [
            'id' => $dispo->id,
        ]);

        // on regarde si les nouvelles dispo sont bien créées avant et apres
        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'start_date' => now()->addDays(10)->toDateString(),
            'end_date' => now()->addDays(12)->toDateString(),
            'type' => 'disponible',
        ]);

        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'start_date' => now()->addDays(15)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'type' => 'disponible',
        ]);
    }
}