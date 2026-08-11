<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Income;
use App\Models\SavingsGoal;
use App\Models\Transaction;
use App\Models\UserSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CalendarController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $year = (int) $request->input('year', now()->year);
        $month = (int) $request->input('month', now()->month);

        $currentMonth = Carbon::createFromDate($year, $month, 1);
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();

        // Fetch Transactions for Month
        $transactions = Transaction::where('user_id', $userId)
            ->whereBetween('transaction_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        // Fetch Incomes for Month
        $incomes = Income::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        // Fetch Budget Deadlines for Month
        $budgetDeadlines = Budget::where('user_id', $userId)
            ->whereNotNull('end_date')
            ->whereBetween('end_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        // Fetch Savings Goal Deadlines for Month
        $savingsDeadlines = SavingsGoal::where('user_id', $userId)
            ->whereNotNull('target_date')
            ->whereBetween('target_date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->get();

        return view('calendar.index', compact(
            'currentMonth',
            'transactions',
            'incomes',
            'budgetDeadlines',
            'savingsDeadlines',
            'setting'
        ));
    }
}
