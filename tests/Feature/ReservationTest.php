<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Annonce;
use App\Models\Calendrier;
use App\Models\Reservation;
use App\Models\User;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_reservation_on_available_dates(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();

        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $calendrier = Calendrier::factory()->create([
            'annonce_id' => $annonce->id,
            'type' => 'disponible',
            'start_date' => now()->addDays(5)->startOfDay(),
            'end_date' => now()->addDays(10)->endOfDay(),
        ]);

        $this->actingAs($user);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(6)->toDateString(),
            'end_date' => now()->addDays(8)->toDateString(),
        ]);

        $response->assertRedirect(); // vers confirmation

        $this->assertDatabaseHas('reservations', [
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
        ]);

        $this->assertDatabaseHas('calendriers', [
            'annonce_id' => $annonce->id,
            'type' => 'réservé',
            'start_date' => now()->addDays(6)->startOfDay()->toDateString(),
            'end_date' => now()->addDays(8)->endOfDay()->toDateString(),
        ]);
    }
}
