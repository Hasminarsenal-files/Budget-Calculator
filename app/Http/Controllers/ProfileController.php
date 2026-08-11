<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $setting = UserSetting::firstOrCreate(['user_id' => $user->id]);

        return view('profile.index', compact('user', 'setting'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'phone_number' => ['nullable', 'string', 'max:20', 'unique:users,phone_number,' . $user->id],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'theme' => ['required', 'in:light,dark,system,pastel'],
            'notify_overbudget' => ['nullable', 'boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'],
        ]);

        UserSetting::updateOrCreate(
            ['user_id' => $user->id],
            [
                'currency_symbol' => $validated['currency_symbol'],
                'theme' => $validated['theme'],
                'notify_overbudget' => $request->has('notify_overbudget'),
            ]
        );

        return redirect()->back()->with('success', 'Profile & Preferences saved! 🐾');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->back()->with('success', 'Password updated securely. 🔒');
    }
}
