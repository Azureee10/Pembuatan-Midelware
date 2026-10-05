<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'role' => 'pembeli',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'pembeli',
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_store_owners_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Store Owner',
            'email' => 'owner@example.com',
            'role' => 'pemilik_toko',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'owner@example.com',
            'role' => 'pemilik_toko',
        ]);
        $response->assertRedirect(route('login'));
    }

    public function test_storefront_navigation_changes_for_authenticated_users(): void
    {
        $this->get('/')
            ->assertSee('Masuk')
            ->assertSee('Daftar');

        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertSee('Dashboard')
            ->assertSee('Daftar')
            ->assertSee('Keluar')
            ->assertDontSee('Masuk');
    }

    public function test_authenticated_users_can_register_another_account_without_changing_current_session(): void
    {
        $currentUser = User::factory()->create();

        $this->actingAs($currentUser)
            ->get('/register')
            ->assertOk();

        $response = $this->post('/register', [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'role' => 'penjual',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticatedAs($currentUser);
        $this->assertDatabaseHas('users', [
            'email' => 'second@example.com',
            'role' => 'penjual',
        ]);
        $response->assertRedirect(route('register'));
    }
}
