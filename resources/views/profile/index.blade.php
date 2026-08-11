<x-app-layout>
    <x-slot name="title">Settings</x-slot>

    <div>
        <h2 class="text-2xl font-extrabold text-charcoal">Profile & Application Settings 👤</h2>
        <p class="text-xs text-softbrown mt-0.5">Customize your currency, theme, notifications & offline storage preferences.</p>
    </div>

    <!-- Settings Navigation Tabs -->
    <div x-data="{ activeTab: 'profile' }" class="space-y-6">
        <div class="flex bg-white p-1.5 rounded-2xl border border-cream-200 shadow-xs text-xs font-bold text-stone-500 overflow-x-auto">
            <button @click="activeTab = 'profile'" :class="activeTab === 'profile' ? 'bg-peach-100 text-charcoal border border-peach-200' : ''" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap">Profile & Security</button>
            <button @click="activeTab = 'currency'" :class="activeTab === 'currency' ? 'bg-peach-100 text-charcoal border border-peach-200' : ''" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap">Currency & Region</button>
            <button @click="activeTab = 'theme'" :class="activeTab === 'theme' ? 'bg-peach-100 text-charcoal border border-peach-200' : ''" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap">Theme & Appearance</button>
            <button @click="activeTab = 'sync'" :class="activeTab === 'sync' ? 'bg-peach-100 text-charcoal border border-peach-200' : ''" class="px-4 py-2.5 rounded-xl transition-all whitespace-nowrap">Data Sync & Offline</button>
        </div>

        <!-- 1. Profile & Security Tab -->
        <div x-show="activeTab === 'profile'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-charcoal">Personal Details</h3>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Full Name</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Email Address</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <input type="hidden" name="currency_symbol" value="{{ $setting->currency_symbol }}">
                    <input type="hidden" name="theme" value="{{ $setting->theme }}">
                    <button type="submit" class="w-full py-3 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm hover:bg-peach-400 transition-all">
                        Save Profile Details 🐾
                    </button>
                </form>
            </div>

            <!-- Password Change Form -->
            <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
                <h3 class="text-base font-bold text-charcoal">Security & Password</h3>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Current Password</label>
                        <input type="password" name="current_password" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">New Password</label>
                        <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Confirm New Password</label>
                        <input type="password" name="password_confirmation" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                    </div>
                    <button type="submit" class="w-full py-3 rounded-2xl bg-stone-800 text-white font-bold text-sm hover:bg-stone-900 transition-all">
                        Update Password 🔒
                    </button>
                </form>
            </div>
        </div>

        <!-- 2. Currency & Region Tab -->
        <div x-show="activeTab === 'currency'" class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4 max-w-xl" style="display:none;">
            <h3 class="text-base font-bold text-charcoal">Currency Settings</h3>
            <p class="text-xs text-softbrown">All calculations, budget totals, and reports will adapt to your selected currency symbol.</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="phone_number" value="{{ $user->phone_number }}">
                <input type="hidden" name="theme" value="{{ $setting->theme }}">

                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Select Currency Symbol</label>
                    <select name="currency_symbol" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-medium">
                        <option value="₱" {{ $setting->currency_symbol == '₱' ? 'selected' : '' }}>₱ — Philippine Peso (PHP)</option>
                        <option value="$" {{ $setting->currency_symbol == '$' ? 'selected' : '' }}>$ — US Dollar (USD)</option>
                        <option value="€" {{ $setting->currency_symbol == '€' ? 'selected' : '' }}>€ — Euro (EUR)</option>
                        <option value="£" {{ $setting->currency_symbol == '£' ? 'selected' : '' }}>£ — British Pound (GBP)</option>
                        <option value="¥" {{ $setting->currency_symbol == '¥' ? 'selected' : '' }}>¥ — Japanese Yen / Yuan (JPY/CNY)</option>
                        <option value="₩" {{ $setting->currency_symbol == '₩' ? 'selected' : '' }}>₩ — Korean Won (KRW)</option>
                        <option value="₹" {{ $setting->currency_symbol == '₹' ? 'selected' : '' }}>₹ — Indian Rupee (INR)</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
                    Update Currency 💱
                </button>
            </form>
        </div>

        <!-- 3. Theme & Appearance Tab -->
        <div x-show="activeTab === 'theme'" class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4 max-w-xl" style="display:none;">
            <h3 class="text-base font-bold text-charcoal">Theme Preference</h3>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PUT')
                <input type="hidden" name="name" value="{{ $user->name }}">
                <input type="hidden" name="email" value="{{ $user->email }}">
                <input type="hidden" name="phone_number" value="{{ $user->phone_number }}">
                <input type="hidden" name="currency_symbol" value="{{ $setting->currency_symbol }}">

                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Theme Mode</label>
                    <select name="theme" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-medium">
                        <option value="pastel" {{ $setting->theme == 'pastel' ? 'selected' : '' }}>Warm Pastel (Default Signature)</option>
                        <option value="light" {{ $setting->theme == 'light' ? 'selected' : '' }}>Clean Light Mode</option>
                        <option value="dark" {{ $setting->theme == 'dark' ? 'selected' : '' }}>Dark Mode</option>
                        <option value="system" {{ $setting->theme == 'system' ? 'selected' : '' }}>Match System Preference</option>
                    </select>
                </div>

                <button type="submit" class="w-full py-3 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
                    Save Theme Choice 🎨
                </button>
            </form>
        </div>

        <!-- 4. Data Sync & Offline Storage Tab -->
        <div x-show="activeTab === 'sync'" class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4 max-w-xl" style="display:none;">
            <h3 class="text-base font-bold text-charcoal">IndexedDB Storage & Cloud Sync</h3>
            
            <div class="p-4 rounded-2xl bg-cream-50 border border-cream-200 space-y-2 text-xs">
                <div class="flex justify-between font-bold text-charcoal">
                    <span>Local Database:</span>
                    <span>purrse-offline-db (v2)</span>
                </div>
                <div class="flex justify-between font-bold text-charcoal">
                    <span>Network Status:</span>
                    <span class="text-emerald-600">● Online / Auto-Sync Active</span>
                </div>
            </div>

            <button onclick="if(window.PurrseSyncEngine){ window.PurrseSyncEngine.processSyncQueue(); }" 
                    class="w-full py-3 rounded-2xl bg-sky-100 text-sky-900 font-bold text-sm border border-sky-200 hover:bg-sky-200 transition-all">
                ↻ Trigger Force Sync Now
            </button>
        </div>
    </div>
</x-app-layout>
