<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Income;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $totalIncome = (float) Income::where('user_id', $userId)->sum('amount');
        $totalExpenses = (float) Transaction::where('user_id', $userId)->sum('amount');
        $netSavings = $totalIncome - $totalExpenses;

        // 1. Expense Categories Breakdown
        $categoryTotals = Transaction::where('transactions.user_id', $userId)
            ->join('categories', 'transactions.category_id', '=', 'categories.uuid')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.color')
            ->orderBy('total', 'desc')
            ->get();

        // 2. Income vs Expense Cash Flow Data (Last 6 Months)
        $cashFlowLabels = [];
        $incomeSeries = [];
        $expenseSeries = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->format('M Y');
            $cashFlowLabels[] = $monthLabel;

            $inc = Income::where('user_id', $userId)
                ->whereYear('date', $date->year)
                ->whereMonth('date', $date->month)
                ->sum('amount');

            $exp = Transaction::where('user_id', $userId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('amount');

            $incomeSeries[] = (float) $inc;
            $expenseSeries[] = (float) $exp;
        }

        // 3. Budget Performance
        $budgets = Budget::where('user_id', $userId)->get();

        // 4. Savings Progress
        $savingsGoals = SavingsGoal::where('user_id', $userId)->get();

        // Friendly Insights
        $topCategory = $categoryTotals->first();
        $topCategoryText = $topCategory 
            ? "Most of your spending went to " . $topCategory->name . " ({$setting->currency_symbol}" . number_format($topCategory->total, 2) . ")." 
            : "No category data available yet.";

        $varianceText = $netSavings >= 0 
            ? "You are {$setting->currency_symbol}" . number_format($netSavings, 2) . " under your overall budget limit!" 
            : "You are {$setting->currency_symbol}" . number_format(abs($netSavings), 2) . " over your planned budget.";

        return view('reports.index', compact(
            'totalIncome',
            'totalExpenses',
            'netSavings',
            'categoryTotals',
            'cashFlowLabels',
            'incomeSeries',
            'expenseSeries',
            'budgets',
            'savingsGoals',
            'topCategoryText',
            'varianceText',
            'setting'
        ));
    }
}
