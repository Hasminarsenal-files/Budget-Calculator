<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class IncomeController extends Controller
{
    public function index(): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $incomes = Income::where('user_id', $userId)
            ->orderBy('date', 'desc')
            ->get();

        $totalIncome = (float) $incomes->sum('amount');

        return view('incomes.index', compact('incomes', 'totalIncome', 'setting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'source' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = Auth::id();

        Income::create($validated);

        return redirect()->back()->with('success', 'Income record logged! 💰');
    }

    public function destroy(Income $income): RedirectResponse
    {
        if ($income->user_id !== Auth::id()) {
            abort(403);
        }

        $income->delete();

        return redirect()->back()->with('success', 'Income record deleted.');
    }
}
