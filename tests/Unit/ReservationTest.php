<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\Calendrier;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_creer_reservation_fonctionne()
    {
        $user = User::factory()->create();

        $annonce = Annonce::factory()->create([
            'user_id' => $user->id,
        ]);
        $calendrier = Calendrier::factory()->create([
            'annonce_id' => $annonce->id,
            'start_date' => now()->addDays(1),
            'end_date' => now()->addDays(5),
            'type' => 'disponible',
        ]);
        $reservation = Reservation::create([
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
            'calendrier_id' => $calendrier->id,
            'start_date' => now()->addDays(2),
            'end_date' => now()->addDays(4),
            'price' => 150,
            'status' => 'pending',
            'last_read_at' => null,
        ]);

        $this->assertDatabaseHas('reservations', [
            'id' => $reservation->id,
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
            'calendrier_id' => $calendrier->id,
            'status' => 'pending',
        ]);
    }
}
