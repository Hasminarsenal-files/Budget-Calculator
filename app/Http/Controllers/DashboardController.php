<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Income;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(
            ['user_id' => $userId],
            ['currency_symbol' => '₱']
        );

        $incomes = Income::where('user_id', $userId)->get();
        $transactions = Transaction::where('user_id', $userId)->get();
        $budgets = Budget::where('user_id', $userId)->where('status', 'active')->get();
        $savingsGoals = SavingsGoal::where('user_id', $userId)->get();

        $totalIncome = (float) $incomes->sum('amount');
        $totalExpenses = (float) $transactions->sum('amount');
        $totalBudget = (float) $budgets->sum('total_amount');
        $remainingBudget = max(0, $totalBudget - $totalExpenses);

        $recentTransactions = Transaction::with(['category', 'budget'])
            ->where('user_id', $userId)
            ->orderBy('transaction_date', 'desc')
            ->limit(5)
            ->get();

        // Category Doughnut Data
        $categoryBreakdown = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.uuid')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->get();

        $topCategory = $categoryBreakdown->first();
        $topCategoryName = $topCategory ? $topCategory->name : 'N/A';

        // Friendly Insights Calculation
        $variance = $totalBudget - $totalExpenses;
        $varianceText = $variance >= 0
            ? "You are {$setting->currency_symbol}" . number_format($variance, 2) . " below your planned spending!"
            : "You're {$setting->currency_symbol}" . number_format(abs($variance), 2) . " over your planned budget.";

        $primaryGoal = $savingsGoals->first();
        $savingsText = "Total saved across goals: {$setting->currency_symbol}" . number_format($savingsGoals->sum('current_amount'), 2);

        return view('dashboard', compact(
            'totalIncome',
            'totalExpenses',
            'totalBudget',
            'remainingBudget',
            'recentTransactions',
            'categoryBreakdown',
            'topCategoryName',
            'varianceText',
            'savingsText',
            'budgets',
            'savingsGoals',
            'primaryGoal',
            'setting'
        ));
    }
}
