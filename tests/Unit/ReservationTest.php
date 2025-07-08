<?php
namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Annonce;
use App\Models\Reservation;
use App\Models\Calendrier;
use Illuminate\Support\Facades\Http;

class ReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_reservation()
    {

        Http::fake([
            'http://localhost:3000/api/sync' => Http::response(['success' => true], 200),
        ]);

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

    public function test_reservation_belongs_to_user()
    {
        $user = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $user->id]);
        $calendrier = Calendrier::factory()->create(['annonce_id' => $annonce->id]);

        $reservation = Reservation::factory()->create([
            'user_id' => $user->id,
            'annonce_id' => $annonce->id,
            'calendrier_id' => $calendrier->id,
        ]);

        $this->assertInstanceOf(User::class, $reservation->user);
    }

    public function test_reservation_has_status_enum()
    {
        $reservation = new Reservation(['status' => 'accepted']);
        $this->assertEquals('accepted', $reservation->status);
    }
}
