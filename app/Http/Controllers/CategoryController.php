<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::where(function ($query) {
            $query->whereNull('user_id')
                ->orWhere('user_id', Auth::id());
        })->get();

        return view('categories.index', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'color' => ['required', 'string'],
            'type' => ['required', 'in:expense,income'],
        ]);

        $validated['uuid'] = (string) Str::uuid();
        $validated['user_id'] = Auth::id();
        $validated['icon'] = 'tag';

        Category::create($validated);

        return redirect()->back()->with('success', 'Custom category created! 🏷️');
    }
}
