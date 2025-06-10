<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservationValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_start_and_end_dates_are_required(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($user);

        $response = $this->post("/annonces/{$annonce->id}/reserver", []);

        $response->assertSessionHasErrors(['start_date', 'end_date']);
    }

    public function test_end_date_must_be_after_start_date(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($user);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(3)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['end_date']);
    }

    public function test_cannot_reserve_if_no_available_period(): void
    {
        $user = User::factory()->create();
        $owner = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $owner->id]);

        $this->actingAs($user);

        $response = $this->post("/annonces/{$annonce->id}/reserver", [
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
        ]);

        $response->assertSessionHasErrors(['start_date']);
    }
}
