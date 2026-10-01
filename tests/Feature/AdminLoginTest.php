<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): Admin
    {
        return Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ]);
    }

    public function test_login_page_is_displayed(): void
    {
        $this->get('/admin/login')->assertOk()->assertSee('Sign in');
    }

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $admin = $this->admin();

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])->assertRedirect('/admin');

        $this->assertAuthenticatedAs($admin, 'admin');
        $this->get('/admin')->assertOk()->assertSee('Dashboard');
    }

    public function test_admin_cannot_log_in_with_wrong_password(): void
    {
        $this->admin();

        $this->from('/admin/login')->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong',
        ])->assertRedirect('/admin/login')->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_regular_users_cannot_access_the_admin_panel(): void
    {
        $user = User::factory()->create(['password' => 'secret-password']);

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'secret-password',
        ])->assertSessionHasErrors('email');

        $this->actingAs($user)->get('/admin')->assertRedirect('/admin/login');
    }

    public function test_login_is_throttled_after_too_many_attempts(): void
    {
        $this->admin();

        foreach (range(1, 5) as $attempt) {
            $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong']);
        }

        $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'secret-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest('admin');
    }

    public function test_authenticated_admin_is_redirected_away_from_login(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get('/admin/login')
            ->assertRedirect('/admin');
    }

    public function test_admin_can_log_out(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->post('/admin/logout')
            ->assertRedirect('/admin/login');

        $this->assertGuest('admin');
    }
}
