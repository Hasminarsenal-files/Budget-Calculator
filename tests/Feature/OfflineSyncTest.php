<?php

namespace Tests\Feature;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OfflineSyncTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'email' => 'offlineuser@purrse.app',
        ]);

        $this->category = Category::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $this->user->id,
            'name' => 'Food & Dining',
            'type' => 'expense',
            'color' => '#FFAAA6',
        ]);
    }

    public function test_1_online_expense_creation(): void
    {
        $response = $this->actingAs($this->user)->post('/transactions', [
            'amount' => 350.00,
            'category_id' => $this->category->uuid,
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'GCash',
            'description' => 'Lunch at Cafe',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('transactions', [
            'user_id' => $this->user->id,
            'amount' => 350.00,
            'payment_method' => 'GCash',
        ]);
    }

    public function test_2_offline_expense_creation_and_sync_payload(): void
    {
        $txUuid = (string) Str::uuid();

        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $txUuid,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $txUuid,
                        'user_id' => $this->user->id,
                        'category_id' => $this->category->uuid,
                        'amount' => 500.00,
                        'transaction_date' => now()->toDateString(),
                        'payment_method' => 'Cash',
                        'description' => 'Offline Grocery Purchase',
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['status' => 'success', 'processed' => 1]);

        $this->assertDatabaseHas('transactions', [
            'uuid' => $txUuid,
            'amount' => 500.00,
            'description' => 'Offline Grocery Purchase',
        ]);
    }

    public function test_3_sync_after_reconnect_bulk_transactions(): void
    {
        $tx1 = (string) Str::uuid();
        $tx2 = (string) Str::uuid();

        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $tx1,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $tx1,
                        'user_id' => $this->user->id,
                        'category_id' => $this->category->uuid,
                        'amount' => 120.00,
                        'payment_method' => 'GCash',
                    ]
                ],
                [
                    'uuid' => $tx2,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $tx2,
                        'user_id' => $this->user->id,
                        'category_id' => $this->category->uuid,
                        'amount' => 250.00,
                        'payment_method' => 'Cash',
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['processed' => 2]);
        $this->assertDatabaseHas('transactions', ['uuid' => $tx1, 'amount' => 120.00]);
        $this->assertDatabaseHas('transactions', ['uuid' => $tx2, 'amount' => 250.00]);
    }

    public function test_4_duplicate_uuid_submission_is_idempotent(): void
    {
        $txUuid = (string) Str::uuid();

        $payload = [
            'items' => [
                [
                    'uuid' => $txUuid,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $txUuid,
                        'user_id' => $this->user->id,
                        'category_id' => $this->category->uuid,
                        'amount' => 850.00,
                        'payment_method' => 'Maya',
                    ]
                ]
            ]
        ];

        // First submission
        $res1 = $this->actingAs($this->user)->postJson('/api/v1/sync', $payload);
        $res1->assertStatus(200);

        // Duplicate second submission with identical UUID
        $res2 = $this->actingAs($this->user)->postJson('/api/v1/sync', $payload);
        $res2->assertStatus(200);

        // Verify only 1 record exists in database (no duplicates created)
        $count = Transaction::where('uuid', $txUuid)->count();
        $this->assertEquals(1, $count);
    }

    public function test_5_invalid_negative_amount_handling(): void
    {
        $txUuid = (string) Str::uuid();

        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $txUuid,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $txUuid,
                        'user_id' => $this->user->id,
                        'amount' => -150.00,
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('transactions', ['uuid' => $txUuid]);
    }

    public function test_6_multiple_offline_entity_types_sync(): void
    {
        $incomeUuid = (string) Str::uuid();
        $budgetUuid = (string) Str::uuid();

        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $incomeUuid,
                    'entity_type' => 'income',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $incomeUuid,
                        'user_id' => $this->user->id,
                        'amount' => 15000.00,
                        'source' => 'Freelance Design',
                        'date' => now()->toDateString(),
                    ]
                ],
                [
                    'uuid' => $budgetUuid,
                    'entity_type' => 'budget',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $budgetUuid,
                        'user_id' => $this->user->id,
                        'name' => 'Boracay Trip 2026',
                        'type' => 'trip',
                        'total_amount' => 20000.00,
                        'start_date' => now()->toDateString(),
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['processed' => 2]);
        $this->assertDatabaseHas('incomes', ['uuid' => $incomeUuid, 'amount' => 15000.00]);
        $this->assertDatabaseHas('budgets', ['uuid' => $budgetUuid, 'name' => 'Boracay Trip 2026']);
    }

    public function test_7_offline_bootstrap_dataset_endpoint(): void
    {
        $response = $this->actingAs($this->user)->getJson('/api/v1/bootstrap');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => [
                    'profile',
                    'budgets',
                    'categories',
                    'transactions',
                    'incomes',
                    'savings_goals',
                    'savings_contributions',
                    'payment_methods',
                    'settings',
                ]
            ]);
    }

    public function test_8_unauthorized_sync_request(): void
    {
        $response = $this->postJson('/api/v1/sync', ['items' => []]);
        $response->assertStatus(401);
    }

    public function test_9_unauthorized_ownership_modification_attempt(): void
    {
        $otherUser = User::factory()->create();
        $txUuid = (string) Str::uuid();

        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $txUuid,
                    'entity_type' => 'transaction',
                    'operation' => 'CREATE',
                    'payload' => [
                        'uuid' => $txUuid,
                        'user_id' => $otherUser->id, // Attempting to inject item for another user
                        'amount' => 9999.00,
                    ]
                ]
            ]
        ]);

        $response->assertStatus(403);
    }

    public function test_10_deleted_transaction_tombstone_synchronization(): void
    {
        // Create an existing transaction first
        $txUuid = (string) Str::uuid();
        $tx = Transaction::create([
            'uuid' => $txUuid,
            'user_id' => $this->user->id,
            'category_id' => $this->category->uuid,
            'amount' => 450.00,
            'transaction_date' => now()->toDateString(),
            'payment_method' => 'Cash',
        ]);

        // Submit sync operation DELETE
        $response = $this->actingAs($this->user)->postJson('/api/v1/sync', [
            'items' => [
                [
                    'uuid' => $txUuid,
                    'entity_type' => 'transaction',
                    'operation' => 'DELETE',
                    'payload' => [
                        'uuid' => $txUuid,
                        'user_id' => $this->user->id,
                    ]
                ]
            ]
        ]);

        $response->assertStatus(200)->assertJson(['processed' => 1]);
        $this->assertSoftDeleted('transactions', ['uuid' => $txUuid]);
    }
}
