<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Daftar terlebih dahulu')
            ->assertDontSee('remember_me')
            ->assertSee(route('register'));
    }

    public function test_authenticated_visitors_opening_login_must_enter_credentials_again(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/login')
            ->assertOk()
            ->assertSee('Log in');

        $this->assertGuest();
    }

    public function test_sessions_expire_when_the_browser_closes(): void
    {
        $this->assertTrue(config('session.expire_on_close'));
    }

    public function test_homepage_always_requires_a_fresh_login(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('href="' . route('login') . '" class="storefront-link"', false);

        $user = User::factory()->create();

        $this->actingAs($user)->get('/')
            ->assertOk()
            ->assertSee('href="' . route('login') . '" class="storefront-link"', false);

        $this->assertGuest();
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $user->forceFill(['remember_token' => null])->save();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
            'remember' => '1',
        ]);

        $this->assertAuthenticated();
        $this->assertNull($user->fresh()->remember_token);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
