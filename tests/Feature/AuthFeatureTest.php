<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Models\User;
use Tests\TestCase;

class AuthFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
{
    $response = $this->post('/register', [
        'firstname' => 'toto',
        'lastname' => 'toto',
        'phone' => '0600000000',
        'address' => '123 rue de la Paix',
        'postal_code' => '75000',
        'city' => 'Paris',
        'country' => 'France',
        'email' => 'test@gmail.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertRedirect('/annonces');
    $this->assertAuthenticated();
}

    public function test_user_cannot_register_with_invalid_data(): void
    {
        $response = $this->post('/register', [
            'firstname' => '',
            'lastname' => '',
            'phone' => '',
            'address' => '',
            'postal_code' => '',
            'city' => '',
            'country' => '',
            'email' => 'toto',
            'password' => '123',
        ]);

        $response->assertSessionHasErrors(['firstname', 'lastname', 'phone', 'address', 'postal_code', 'city', 'country', 'email', 'password']);
        $this->assertGuest();
    }

    public function test_user_can_login(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect('/annonces');
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrongpassword',
        ]);

        $response->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $response->assertRedirect('/');
        $this->assertGuest();
    }
}
