<x-app-layout>
    <x-slot name="title">Savings Goals</x-slot>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-charcoal">Savings Goals 🐷</h2>
            <p class="text-xs text-softbrown mt-0.5">Emergency Fund, New Phone, Travel, Tuition & Holiday Funds.</p>
        </div>
        <button @click="quickAddOpen = true; quickAddTab = 'savings'" 
                class="px-4 py-2.5 rounded-2xl bg-purple-300 text-charcoal font-bold text-sm shadow-sm hover:bg-purple-400 transition-all">
            + New Contribution
        </button>
    </div>

    <!-- Savings Goals Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($savingsGoals as $goal)
            <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm hover:shadow-md transition-all space-y-4 relative overflow-hidden">
                @if($goal->is_completed)
                    <div class="absolute top-0 right-0 bg-emerald-400 text-emerald-950 font-extrabold text-[10px] uppercase px-3 py-1 rounded-bl-2xl shadow-sm">
                        Goal Completed! 🎉
                    </div>
                @endif

                <div class="flex items-center justify-between pt-1">
                    <h3 class="text-lg font-bold text-charcoal">{{ $goal->name }}</h3>
                    <form method="POST" action="{{ route('savings-goals.destroy', $goal->uuid) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this savings goal?')" class="text-stone-300 hover:text-rose-500 font-bold text-sm">
                            🗑️
                        </button>
                    </form>
                </div>

                @if($goal->description)
                    <p class="text-xs text-stone-500 line-clamp-2 leading-relaxed">{{ $goal->description }}</p>
                @endif

                <div class="space-y-1.5">
                    <div class="flex justify-between items-baseline">
                        <span class="text-2xl font-extrabold text-charcoal">{{ $setting->currency_symbol }}{{ number_format($goal->current_amount, 2) }}</span>
                        <span class="text-xs text-stone-400 font-medium">Target: {{ $setting->currency_symbol }}{{ number_format($goal->target_amount, 2) }}</span>
                    </div>

                    <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $goal->is_completed ? 'bg-emerald-400' : 'bg-gradient-to-r from-purple-300 to-pink-300' }}"
                             style="width: {{ $goal->progress_percentage }}%"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-stone-100 text-xs">
                    <span class="text-stone-500 font-medium">{{ $goal->progress_percentage }}% saved</span>
                    @if($goal->target_date)
                        <span class="text-stone-400 text-[11px]">Target: {{ \Carbon\Carbon::parse($goal->target_date)->format('M d, Y') }}</span>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state type="savings" />
            </div>
        @endforelse
    </div>
</x-app-layout>
