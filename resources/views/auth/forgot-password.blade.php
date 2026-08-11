<x-guest-layout>
    <div class="bg-white/90 backdrop-blur-sm rounded-3xl p-6 sm:p-8 shadow-sm border border-amber-100/60">
        <h2 class="text-xl font-bold text-charcoal mb-2 text-center">Reset Password 🔑</h2>
        <p class="text-xs text-softbrown text-center mb-6">Enter your registered email address or phone number to receive a reset link.</p>

        <form method="POST" action="#" class="space-y-4">
            @csrf

            <div>
                <label for="login" class="block text-xs font-bold text-softbrown uppercase tracking-wider mb-1">Email or Phone Number</label>
                <input id="login" type="text" name="login" required autofocus 
                       placeholder="demo@purrse.app"
                       class="w-full px-4 py-3 rounded-2xl bg-cream-50/70 border border-stone-200 focus:border-peach-300 focus:ring-2 focus:ring-peach-200 outline-none text-sm transition-all">
            </div>

            <button type="submit" 
                    class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-peach-200 via-peach-300 to-amber-200 text-charcoal font-bold text-sm shadow-sm hover:shadow-md transition-all">
                Send Reset Link 📩
            </button>
        </form>

        <div class="mt-6 text-center text-xs text-softbrown">
            Remembered your password? 
            <a href="{{ route('login') }}" class="font-bold text-charcoal underline hover:text-peach-300">Back to Login</a>
        </div>
    </div>
</x-guest-layout>
