<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Budget;
use App\Models\Category;
use App\Models\Income;
use App\Models\PaymentMethod;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\SyncQueue;
use App\Models\Transaction;
use App\Models\UserSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SyncController extends Controller
{
    /**
     * Return user's bootstrapping dataset for offline caching in IndexedDB.
     */
    public function bootstrap(Request $request): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $setting = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            ['currency_symbol' => '₱']
        );

        $budgets = Budget::where('user_id', $user->id)->get();
        
        $categories = Category::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->get();

        $transactions = Transaction::where('user_id', $user->id)
            ->orderBy('transaction_date', 'desc')
            ->limit(100)
            ->get();

        $incomes = Income::where('user_id', $user->id)
            ->orderBy('date', 'desc')
            ->get();

        $savingsGoals = SavingsGoal::where('user_id', $user->id)->get();

        $savingsContributions = SavingsContribution::where('user_id', $user->id)->get();

        $paymentMethods = PaymentMethod::where(function ($q) use ($user) {
            $q->whereNull('user_id')->orWhere('user_id', $user->id);
        })->get();

        return response()->json([
            'status' => 'success',
            'data' => [
                'profile' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone_number' => $user->phone_number,
                    'currency_symbol' => $setting->currency_symbol,
                ],
                'budgets' => $budgets,
                'categories' => $categories,
                'transactions' => $transactions,
                'incomes' => $incomes,
                'savings_goals' => $savingsGoals,
                'savings_contributions' => $savingsContributions,
                'payment_methods' => $paymentMethods,
                'settings' => $setting,
            ]
        ]);
    }

    /**
     * Process offline sync queue payloads idempotently with security & ownership validation.
     */
    public function sync(Request $request): JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $items = $request->input('items', []);
        $processed = 0;
        $errors = [];

        foreach ($items as $item) {
            $entityType = strtolower($item['entity_type'] ?? $item['entity'] ?? '');
            $operation = strtoupper($item['operation'] ?? $item['action'] ?? 'CREATE');
            $payload = $item['payload'] ?? [];
            $uuid = $payload['uuid'] ?? ($item['uuid'] ?? null);

            if (!$uuid) {
                continue;
            }

            // Security Ownership Enforcement: Ensure payload user_id matches authenticated user
            if (isset($payload['user_id']) && (int)$payload['user_id'] !== (int)$userId) {
                return response()->json(['error' => 'Forbidden: Cannot modify data owned by another user'], 403);
            }

            // Security Validation: Amount must be non-negative if present
            if (isset($payload['amount']) && (float)$payload['amount'] < 0) {
                $errors[] = "Invalid amount for entity {$uuid}";
                continue;
            }

            // Log item to server SyncQueue for auditing
            SyncQueue::updateOrCreate(
                ['uuid' => $uuid],
                [
                    'user_id' => $userId,
                    'action' => strtolower($operation),
                    'entity_type' => $entityType,
                    'entity_id' => $uuid,
                    'payload' => $payload,
                    'status' => 'synced',
                    'synced_at' => now(),
                ]
            );

            // Execute Entity Synchronization
            if ($entityType === 'transaction') {
                $existing = Transaction::withTrashed()->where('uuid', $uuid)->first();
                
                if ($existing && (int)$existing->user_id !== (int)$userId) {
                    return response()->json(['error' => 'Forbidden: Record belongs to another user'], 403);
                }

                if ($operation === 'DELETE') {
                    if ($existing) {
                        $existing->delete();
                    }
                } else {
                    // Idempotent CREATE / UPDATE (Last-write-wins)
                    Transaction::updateOrCreate(
                        ['uuid' => $uuid],
                        [
                            'uuid' => $uuid,
                            'user_id' => $userId,
                            'budget_id' => $payload['budget_id'] ?? null,
                            'category_id' => $payload['category_id'] ?? null,
                            'amount' => $payload['amount'] ?? 0,
                            'transaction_date' => $payload['transaction_date'] ?? now()->toDateString(),
                            'payment_method' => $payload['payment_method'] ?? 'Cash',
                            'description' => $payload['description'] ?? null,
                            'notes' => $payload['notes'] ?? null,
                            'deleted_at' => null,
                        ]
                    );
                }
            } elseif ($entityType === 'income') {
                $existing = Income::where('uuid', $uuid)->first();

                if ($existing && (int)$existing->user_id !== (int)$userId) {
                    return response()->json(['error' => 'Forbidden: Record belongs to another user'], 403);
                }

                if ($operation === 'DELETE') {
                    if ($existing) {
                        $existing->delete();
                    }
                } else {
                    Income::updateOrCreate(
                        ['uuid' => $uuid],
                        [
                            'uuid' => $uuid,
                            'user_id' => $userId,
                            'amount' => $payload['amount'] ?? 0,
                            'source' => $payload['source'] ?? 'General',
                            'date' => $payload['date'] ?? now()->toDateString(),
                            'description' => $payload['description'] ?? null,
                        ]
                    );
                }
            } elseif ($entityType === 'budget') {
                $existing = Budget::withTrashed()->where('uuid', $uuid)->first();

                if ($existing && (int)$existing->user_id !== (int)$userId) {
                    return response()->json(['error' => 'Forbidden: Record belongs to another user'], 403);
                }

                if ($operation === 'DELETE') {
                    if ($existing) {
                        $existing->delete();
                    }
                } else {
                    Budget::updateOrCreate(
                        ['uuid' => $uuid],
                        [
                            'uuid' => $uuid,
                            'user_id' => $userId,
                            'name' => $payload['name'] ?? 'Untitled Budget',
                            'type' => $payload['type'] ?? 'monthly',
                            'total_amount' => $payload['total_amount'] ?? 0,
                            'start_date' => $payload['start_date'] ?? now()->toDateString(),
                            'end_date' => $payload['end_date'] ?? null,
                            'description' => $payload['description'] ?? null,
                            'status' => $payload['status'] ?? 'active',
                            'deleted_at' => null,
                        ]
                    );
                }
            } elseif ($entityType === 'savings_goal') {
                $existing = SavingsGoal::where('uuid', $uuid)->first();

                if ($existing && (int)$existing->user_id !== (int)$userId) {
                    return response()->json(['error' => 'Forbidden: Record belongs to another user'], 403);
                }

                if ($operation === 'DELETE') {
                    if ($existing) {
                        $existing->delete();
                    }
                } else {
                    SavingsGoal::updateOrCreate(
                        ['uuid' => $uuid],
                        [
                            'uuid' => $uuid,
                            'user_id' => $userId,
                            'name' => $payload['name'] ?? 'Savings Goal',
                            'target_amount' => $payload['target_amount'] ?? 0,
                            'current_amount' => $payload['current_amount'] ?? 0,
                            'target_date' => $payload['target_date'] ?? null,
                            'description' => $payload['description'] ?? null,
                            'status' => $payload['status'] ?? 'active',
                        ]
                    );
                }
            }

            $processed++;
        }

        return response()->json([
            'status' => 'success',
            'processed' => $processed,
            'errors' => $errors,
            'message' => 'Offline data synchronized successfully.'
        ]);
    }
}
