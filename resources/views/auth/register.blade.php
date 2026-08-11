<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-5 sm:p-7 shadow-sm border border-amber-100/60">
        <h2 class="text-lg sm:text-xl font-bold text-charcoal mb-3 text-center">Join PURRSE Today! 🐾</h2>

        <form method="POST" action="{{ route('register') }}" class="space-y-2.5 sm:space-y-3">
            @csrf

            <!-- Full Name -->
            <div>
                <label for="name" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-0.5">Full Name</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus 
                       placeholder="e.g. Hasmin Dela Cruz"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('name') border-rose-400 @enderror">
                @error('name')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-0.5">Email Address</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required 
                       placeholder="hasmin@example.com"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('email') border-rose-400 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone_number" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-0.5">Phone Number (Optional)</label>
                <input id="phone_number" type="text" name="phone_number" value="{{ old('phone_number') }}" 
                       placeholder="09171234567"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('phone_number') border-rose-400 @enderror">
                @error('phone_number')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <label for="password" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-0.5">Password</label>
                <input id="password" type="password" name="password" required 
                       placeholder="Minimum 8 characters"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('password') border-rose-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div>
                <label for="password_confirmation" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-0.5">Confirm Password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required 
                       placeholder="Re-enter password"
                       class="w-full px-3.5 py-2 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all">
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-2.5 px-4 rounded-xl bg-gradient-to-r from-peach-200 via-peach-300 to-amber-200 text-charcoal font-bold text-sm shadow-sm hover:shadow-md hover:opacity-95 transition-all transform active:scale-[0.98] mt-1">
                Create Free Account 🐱
            </button>
        </form>

        <div class="mt-3.5 text-center text-xs text-softbrown">
            Already have an account? 
            <a href="{{ route('login') }}" class="font-bold text-charcoal underline hover:text-peach-300">Sign In</a>
        </div>
    </div>
</x-guest-layout>

