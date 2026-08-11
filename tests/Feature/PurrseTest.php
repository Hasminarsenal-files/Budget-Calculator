<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PurrseTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'testuser@purrse.app',
            'phone_number' => '09170000000',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'testuser@purrse.app']);
    }

    public function test_user_can_login_with_phone_number(): void
    {
        $user = User::factory()->create([
            'phone_number' => '09181112222',
            'password' => bcrypt('secret123'),
        ]);

        $response = $this->post('/login', [
            'login' => '09181112222',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_budget_details_and_trip_mode_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $tripBudget = Budget::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Palawan Beach Vacation',
            'type' => 'trip',
            'total_amount' => 12000.00,
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(5)->toDateString(),
            'status' => 'active',
        ]);

        $response = $this->get('/budgets/' . $tripBudget->uuid);
        $response->assertStatus(200);
        $response->assertSee('Palawan Beach Vacation');
        $response->assertSee('Trip Budget Mode');
    }

    public function test_financial_calendar_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/calendar');
        $response->assertStatus(200);
        $response->assertSee('Financial Calendar');
    }

    public function test_reports_view(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get('/reports');
        $response->assertStatus(200);
        $response->assertSee('Financial Insights');
    }

    public function test_settings_currency_symbol_update(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->put('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
            'currency_symbol' => '$',
            'theme' => 'pastel',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_settings', [
            'user_id' => $user->id,
            'currency_symbol' => '$',
        ]);
    }
}
