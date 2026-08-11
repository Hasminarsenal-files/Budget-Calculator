<?php

namespace App\Http\Controllers;

use App\Models\SavingsContribution;
use App\Models\SavingsGoal;
use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SavingsGoalController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $savingsGoals = SavingsGoal::with('contributions')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('savings-goals.index', compact('savingsGoals', 'setting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'target_amount' => ['required', 'numeric', 'min:1'],
            'current_amount' => ['nullable', 'numeric', 'min:0'],
            'target_date' => ['nullable', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = Auth::id();
        $validated['current_amount'] = $validated['current_amount'] ?? 0;
        $validated['status'] = $validated['current_amount'] >= $validated['target_amount'] ? 'completed' : 'active';

        SavingsGoal::create($validated);

        return redirect()->back()->with('success', 'New savings goal set up! 🐷');
    }

    public function contribute(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'savings_goal_id' => ['required', 'exists:savings_goals,uuid'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'contribution_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $goal = SavingsGoal::where('uuid', $validated['savings_goal_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $validated['user_id'] = Auth::id();
        SavingsContribution::create($validated);

        // Update Goal Current Amount
        $goal->current_amount += $validated['amount'];
        if ($goal->current_amount >= $goal->target_amount) {
            $goal->status = 'completed';
        }
        $goal->save();

        $msg = $goal->status === 'completed'
            ? 'HOORAY! Savings Goal 100% Completed! 🎉'
            : 'Another coin added to ' . $goal->name . '! 🪙';

        return redirect()->back()->with('success', $msg);
    }

    public function destroy(SavingsGoal $savingsGoal): RedirectResponse
    {
        if ($savingsGoal->user_id !== Auth::id()) {
            abort(403);
        }

        $savingsGoal->delete();

        return redirect()->back()->with('success', 'Savings goal removed.');
    }
}
