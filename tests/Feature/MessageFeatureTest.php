<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\Message;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class MessageFeatureTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_send_message_related_to_reservation(): void
    {
        $owner = User::factory()->create();
        $traveler = User::factory()->create();

        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $calendrier = \App\Models\Calendrier::factory()->create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(12),
        ]);

        $reservation = Reservation::factory()->create([
            'annonce_id' => $annonce->id,
            'user_id' => $traveler->id,
            'calendrier_id' => $calendrier->id,
        ]);

        $this->actingAs($traveler);

        $response = $this->post("/reservations/{$reservation->id}/messages", [
            'content' => 'Nouveau message test',
        ]);

        $response->assertRedirect(); 
        $this->assertDatabaseHas('messages', [
            'reservation_id' => $reservation->id,
            'sender_id' => $traveler->id,
            'content' => 'Nouveau message test',
        ]);
    }

    public function test_guests_cannot_access_messages(): void
    {
        $owner = User::factory()->create();
        $traveler = User::factory()->create();

        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $calendrier = \App\Models\Calendrier::factory()->create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(7),
            'end_date' => now()->addDays(12),
        ]);

        $reservation = Reservation::factory()->create([
            'annonce_id' => $annonce->id,
            'user_id' => $traveler->id,
            'calendrier_id' => $calendrier->id,
            'status' => 'accepted',
        ]);

        $response = $this->get("/reservations/{$reservation->id}/messages");

        $response->assertRedirect('/login');
    }
}
