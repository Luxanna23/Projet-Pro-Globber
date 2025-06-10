<?php

namespace Tests\Unit;

use Tests\TestCase; 
use App\Models\User;
use App\Models\Annonce;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    use RefreshDatabase;

    public function test_user_has_annonces_relation(): void
    {
        $user = User::factory()->create();
        $annonce = Annonce::factory()->create(['user_id' => $user->id]);

        $user->load('annonces'); 
        $this->assertTrue($user->annonces->contains($annonce));
    }

    public function test_user_email_verified_at_is_cast_to_datetime(): void
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $user->email_verified_at);
    }

    public function test_password_is_hidden(): void
    {
        $user = User::factory()->create([
            'password' => 'secret',
        ]);

        $this->assertArrayNotHasKey('password', $user->toArray());
    }
}
