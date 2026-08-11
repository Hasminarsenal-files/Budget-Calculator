<?php

namespace Database\Seeders;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Income;
use App\Models\PaymentMethod;
use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. System Default Categories
        $categories = [
            ['name' => 'Food & Dining', 'icon' => 'utensils', 'color' => '#FFC8DD', 'type' => 'expense'],
            ['name' => 'Transportation', 'icon' => 'car', 'color' => '#B8C5A8', 'type' => 'expense'],
            ['name' => 'Shopping', 'icon' => 'shopping-bag', 'color' => '#FFDFD3', 'type' => 'expense'],
            ['name' => 'Bills & Utilities', 'icon' => 'file-text', 'color' => '#E8C5C8', 'type' => 'expense'],
            ['name' => 'Entertainment', 'icon' => 'film', 'color' => '#D4A5A5', 'type' => 'expense'],
            ['name' => 'Travel & Trip', 'icon' => 'plane', 'color' => '#A9DEF9', 'type' => 'expense'],
            ['name' => 'Health & Personal', 'icon' => 'heart', 'color' => '#E4C1F9', 'type' => 'expense'],
            ['name' => 'Salary', 'icon' => 'wallet', 'color' => '#C3F0C8', 'type' => 'income'],
            ['name' => 'Freelance & Side Hustle', 'icon' => 'laptop', 'color' => '#D0F4DE', 'type' => 'income'],
            ['name' => 'Gifts & Allowance', 'icon' => 'gift', 'color' => '#FFF5BA', 'type' => 'income'],
        ];

        $categoryModels = [];
        foreach ($categories as $cat) {
            $categoryModels[$cat['name']] = Category::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => null,
                'name' => $cat['name'],
                'icon' => $cat['icon'],
                'color' => $cat['color'],
                'type' => $cat['type'],
            ]);
        }

        // 2. Default Payment Methods
        $methods = [
            ['name' => 'Cash', 'type' => 'cash', 'icon' => 'banknotes', 'is_default' => true],
            ['name' => 'GCash', 'type' => 'e-wallet', 'icon' => 'device-phone-mobile', 'is_default' => false],
            ['name' => 'Bank Transfer', 'type' => 'bank', 'icon' => 'building-library', 'is_default' => false],
            ['name' => 'Debit Card', 'type' => 'card', 'icon' => 'credit-card', 'is_default' => false],
            ['name' => 'Credit Card', 'type' => 'card', 'icon' => 'credit-card', 'is_default' => false],
        ];

        foreach ($methods as $m) {
            PaymentMethod::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => null,
                'name' => $m['name'],
                'type' => $m['type'],
                'icon' => $m['icon'],
                'is_default' => $m['is_default'],
            ]);
        }

        // 3. Demo User
        $user = User::create([
            'name' => 'Hasmin Companion',
            'email' => 'demo@purrse.app',
            'phone_number' => '09171234567',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
        ]);

        UserSetting::create([
            'user_id' => $user->id,
            'currency_symbol' => '₱',
            'theme' => 'pastel',
            'notify_overbudget' => true,
        ]);

        // 4. Sample Budgets
        $monthlyBudget = Budget::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'August Monthly Budget',
            'type' => 'monthly',
            'total_amount' => 25000.00,
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'description' => 'Personal monthly expenses for food, transport, bills',
            'status' => 'active',
        ]);

        $tripBudget = Budget::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Weekend Beach Trip',
            'type' => 'trip',
            'total_amount' => 6500.00,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(8),
            'description' => 'Beach resort getaway with friends',
            'status' => 'active',
        ]);

        // 5. Sample Incomes
        Income::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'amount' => 32000.00,
            'source' => 'Salary',
            'date' => now()->subDays(10),
            'description' => 'Monthly Salary payout',
        ]);

        Income::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'amount' => 4500.00,
            'source' => 'Freelance & Side Hustle',
            'date' => now()->subDays(3),
            'description' => 'UI Design Client Project',
        ]);

        // 6. Sample Transactions
        Transaction::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'budget_id' => $monthlyBudget->uuid,
            'category_id' => $categoryModels['Food & Dining']->uuid,
            'amount' => 1250.00,
            'transaction_date' => now()->subDays(2),
            'payment_method' => 'GCash',
            'description' => 'Grocery shopping at Supermarket',
        ]);

        Transaction::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'budget_id' => $monthlyBudget->uuid,
            'category_id' => $categoryModels['Transportation']->uuid,
            'amount' => 450.00,
            'transaction_date' => now()->subDays(1),
            'payment_method' => 'Cash',
            'description' => 'Grab Ride to Office',
        ]);

        Transaction::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'budget_id' => $monthlyBudget->uuid,
            'category_id' => $categoryModels['Bills & Utilities']->uuid,
            'amount' => 2400.00,
            'transaction_date' => now()->subDays(4),
            'payment_method' => 'Bank Transfer',
            'description' => 'Internet Bill',
        ]);

        // 7. Sample Savings Goals
        $emergencyFund = SavingsGoal::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'Emergency Fund 🛡️',
            'target_amount' => 50000.00,
            'current_amount' => 18500.00,
            'target_date' => now()->addMonths(6),
            'description' => '3 months buffer for rainy days',
            'status' => 'active',
        ]);

        SavingsContribution::create([
            'uuid' => (string) Str::uuid(),
            'savings_goal_id' => $emergencyFund->uuid,
            'user_id' => $user->id,
            'amount' => 3500.00,
            'contribution_date' => now()->subDays(5),
            'note' => 'Monthly automated saving',
        ]);

        $newPhone = SavingsGoal::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'name' => 'New Phone Upgrade 📱',
            'target_amount' => 15000.00,
            'current_amount' => 15000.00,
            'target_date' => now()->subDays(1),
            'description' => 'Target achieved!',
            'status' => 'completed',
        ]);
    }
}
