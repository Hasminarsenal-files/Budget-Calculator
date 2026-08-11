<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class BudgetController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $budgets = Budget::with('transactions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('budgets.index', compact('budgets', 'setting'));
    }

    public function show(Budget $budget): View
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $setting = UserSetting::firstOrCreate(['user_id' => Auth::id()]);

        $transactions = Transaction::where('budget_id', $budget->uuid)
            ->orderBy('transaction_date', 'desc')
            ->get();

        $spentAmount = (float) $transactions->sum('amount');
        $remainingAmount = max(0, $budget->total_amount - $spentAmount);
        $percentageUsed = $budget->total_amount > 0 ? min(100, round(($spentAmount / $budget->total_amount) * 100, 1)) : 0;

        // Days Remaining Calculation
        $daysRemaining = 1;
        if ($budget->end_date) {
            $endDate = Carbon::parse($budget->end_date);
            $daysRemaining = max(1, now()->diffInDays($endDate, false));
        }

        $dailyRecommendedSpending = $remainingAmount > 0 && $daysRemaining > 0 ? round($remainingAmount / $daysRemaining, 2) : 0;

        // Category Breakdown for Trip / Budget
        $categoryBreakdown = Transaction::where('budget_id', $budget->uuid)
            ->join('categories', 'transactions.category_id', '=', 'categories.uuid')
            ->selectRaw('categories.name, categories.color, SUM(transactions.amount) as total')
            ->groupBy('categories.name', 'categories.color')
            ->get();

        return view('budgets.show', compact(
            'budget',
            'transactions',
            'spentAmount',
            'remainingAmount',
            'percentageUsed',
            'daysRemaining',
            'dailyRecommendedSpending',
            'categoryBreakdown',
            'setting'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:monthly,weekly,daily,trip,event,project,custom'],
            'total_amount' => ['required', 'numeric', 'min:1'],
            'start_date' => ['required', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'active';

        $budget = Budget::create($validated);

        return redirect()->route('budgets.show', $budget->uuid)->with('success', 'New budget created! 👛');
    }

    public function destroy(Budget $budget): RedirectResponse
    {
        if ($budget->user_id !== Auth::id()) {
            abort(403);
        }

        $budget->delete();

        return redirect()->route('budgets.index')->with('success', 'Budget deleted.');
    }
}
