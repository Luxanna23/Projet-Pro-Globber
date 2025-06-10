<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationProcessTest extends TestCase
{
    use RefreshDatabase;

    public function test_full_reservation_flow_for_authenticated_user()
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $this->actingAs($user);

        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $calendrier = Calendrier::create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(10),
        ]);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date' => now()->addDays(8)->toDateString(),
        ]);

        $response->assertRedirect(); // confirmation

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
        ]);

        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'type' => 'réservé',
        ]);
    }
}
// la on voit une annonce disponible, effectue une réservation et on vérifie qu’une réservation et un calendrier de type "réservé" ont bien été créés.