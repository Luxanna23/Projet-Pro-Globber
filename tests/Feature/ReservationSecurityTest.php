<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use App\Models\User;

class ReservationSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_cannot_make_a_reservation(): void
    {
       $user = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $user->id]);
        $calendrier = Calendrier::factory()->create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(10),
        ]);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => $calendrier->start_date->toDateString(),
            'end_date' => $calendrier->end_date->toDateString(),
        ]);

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_their_own_reservation_confirmation(): void
    {
        $user = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $user->id]);
        $calendrier = Calendrier::factory()->create(['annonce_id' => $annonce->id]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
            'calendrier_id' => $calendrier->id,
        ]);

        $this->actingAs($user);

        $response = $this->get("/reservations/{$reservation->id}/confirmation");

        $response->assertStatus(200);
        $response->assertSee((string) $reservation->id);
    }

    public function test_user_cannot_view_confirmation_of_others_reservation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $user1->id]);
        $calendrier = Calendrier::factory()->create(['annonce_id' => $annonce->id]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user1->id,
            'annonce_id' => $annonce->id,
            'calendrier_id' => $calendrier->id,
        ]);

        $this->actingAs($user2);

        $response = $this->get("/reservations/{$reservation->id}/confirmation");

        $response->assertStatus(403); // ou 404 selon ta logique
    }
}
