<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Category;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function index(Request $request): View
    {
        $userId = Auth::id();
        $setting = UserSetting::firstOrCreate(['user_id' => $userId]);

        $query = Transaction::with(['category', 'budget'])
            ->where('user_id', $userId);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('description', 'like', "%{$search}%");
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->input('category_id'));
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->input('payment_method'));
        }

        $transactions = $query->orderBy('transaction_date', 'desc')->paginate(15);

        $categories = Category::where('type', 'expense')->get();
        $paymentMethods = PaymentMethod::all();

        return view('transactions.index', compact('transactions', 'categories', 'paymentMethods', 'setting'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01'],
            'category_id' => ['required', 'exists:categories,uuid'],
            'budget_id' => ['nullable', 'exists:budgets,uuid'],
            'payment_method' => ['required', 'string'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $validated['user_id'] = Auth::id();

        Transaction::create($validated);

        return redirect()->back()->with('success', 'Expense logged successfully! 💸');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->user_id !== Auth::id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->back()->with('success', 'Expense record deleted.');
    }
}
