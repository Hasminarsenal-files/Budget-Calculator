<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-5 sm:p-7 shadow-sm border border-amber-100/60">
        <h2 class="text-lg sm:text-xl font-bold text-charcoal mb-4 text-center">Welcome back! 🐱</h2>

        @if (session('status'))
            <div class="mb-3 text-xs font-semibold text-emerald-600 bg-emerald-50 py-2 px-3 rounded-xl border border-emerald-100">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-3 sm:space-y-3.5">
            @csrf

            <!-- Email or Phone Number -->
            <div>
                <label for="login" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider mb-1">Email or Phone Number</label>
                <input id="login" type="text" name="login" value="{{ old('login') }}" required autofocus 
                       placeholder="demo@purrse.app or 09171234567"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('login') border-rose-400 @enderror">
                @error('login')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1">
                    <label for="password" class="block text-[11px] font-bold text-softbrown uppercase tracking-wider">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs text-peach-300 hover:text-rose-400 font-semibold transition-colors">Forgot Password?</a>
                </div>
                <input id="password" type="password" name="password" required 
                       placeholder="••••••••"
                       class="w-full px-3.5 py-2.5 rounded-xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all @error('password') border-rose-400 @enderror">
                @error('password')
                    <p class="mt-1 text-xs text-rose-500 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between pt-0.5">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="remember" class="w-4 h-4 rounded border-stone-300 text-peach-300 focus:ring-peach-200">
                    <span class="text-xs font-medium text-softbrown">Remember me</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit" 
                    class="w-full py-3 px-4 rounded-xl bg-gradient-to-r from-peach-200 via-peach-300 to-amber-200 text-charcoal font-bold text-sm shadow-sm hover:shadow-md hover:opacity-95 transition-all transform active:scale-[0.98]">
                Sign In to PURRSE 🐾
            </button>
        </form>

        <!-- Quick Demo Credentials Hint -->
        <div class="mt-3.5 p-2.5 bg-cream-50 rounded-xl border border-stone-200/60 text-[11px] sm:text-xs text-center text-softbrown">
            <span class="font-bold">Demo Login:</span> demo@purrse.app &nbsp;|&nbsp; <span class="font-bold">Password:</span> password
        </div>

        <div class="mt-3.5 text-center text-xs text-softbrown">
            Don't have an account yet? 
            <a href="{{ route('register') }}" class="font-bold text-charcoal underline hover:text-peach-300">Create Account</a>
        </div>
    </div>
</x-guest-layout>

