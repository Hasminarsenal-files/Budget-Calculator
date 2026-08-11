<x-app-layout>
    <x-slot name="title">Income</x-slot>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-charcoal">Income & Earnings 💰</h2>
            <p class="text-xs text-softbrown mt-0.5">Track salary, side hustles, freelance projects & allowances.</p>
        </div>
        <button @click="quickAddOpen = true; quickAddTab = 'income'" 
                class="px-4 py-2.5 rounded-2xl bg-emerald-300 text-charcoal font-bold text-sm shadow-sm hover:bg-emerald-400 transition-all">
            + Add Income
        </button>
    </div>

    <!-- Income Summary Header Card -->
    <div class="bg-gradient-to-r from-emerald-100 via-teal-50 to-sage-100 p-6 rounded-3xl border border-emerald-200 shadow-sm flex items-center justify-between">
        <div>
            <span class="text-xs font-extrabold uppercase text-emerald-800 tracking-wider">Total Recorded Earnings</span>
            <div class="text-3xl font-extrabold text-emerald-950 mt-1">
                +{{ $setting->currency_symbol }}{{ number_format($totalIncome, 2) }}
            </div>
        </div>
        <div class="w-14 h-14 rounded-2xl bg-emerald-200/80 flex items-center justify-center text-3xl">
            💵
        </div>
    </div>

    <!-- Incomes List -->
    <div class="bg-white rounded-3xl border border-cream-200 shadow-sm overflow-hidden">
        <div class="divide-y divide-stone-100">
            @forelse($incomes as $inc)
                <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-cream-50/50 transition-colors">
                    <div class="flex items-center gap-3.5">
                        <div class="w-11 h-11 rounded-2xl bg-emerald-100 border border-emerald-200 flex items-center justify-center text-xl shadow-sm flex-shrink-0">
                            💰
                        </div>
                        <div>
                            <p class="text-sm font-bold text-charcoal leading-tight">{{ $inc->source }}</p>
                            @if($inc->description)
                                <p class="text-xs text-stone-400 mt-0.5">{{ $inc->description }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="text-right flex items-center gap-4">
                        <div>
                            <span class="text-base font-extrabold text-emerald-600">+{{ $setting->currency_symbol }}{{ number_format($inc->amount, 2) }}</span>
                            <span class="block text-[11px] text-stone-400 font-medium">{{ \Carbon\Carbon::parse($inc->date)->format('M d, Y') }}</span>
                        </div>
                        <form method="POST" action="{{ route('incomes.destroy', $inc->uuid) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" onclick="return confirm('Delete this income record?')" class="text-stone-300 hover:text-rose-500 font-bold text-sm p-1">
                                &times;
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-8">
                    <x-empty-state type="generic" title="No Income Logged Yet" message="Tap '+ Add Income' to record your salary, freelance earnings, or allowance." actionLabel="+ Add Income 💰" actionClick="quickAddOpen = true; quickAddTab = 'income'" />
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
