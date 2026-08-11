<x-app-layout>
    <x-slot name="title">Budgets</x-slot>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-charcoal">Budgets & Targets 👛</h2>
            <p class="text-xs text-softbrown mt-0.5">Manage personal monthly budgets, trip budgets, event budgets & project funds.</p>
        </div>
        <button @click="quickAddOpen = true; quickAddTab = 'budget'" 
                class="px-4 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
            + New Budget
        </button>
    </div>

    <!-- Budgets List Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($budgets as $budget)
            <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm hover:shadow-md transition-all space-y-4 relative group">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-xs font-extrabold uppercase tracking-wider px-2.5 py-1 rounded-full bg-peach-100 text-peach-900 border border-peach-200">
                            {{ $budget->type === 'trip' ? '✈️ Trip Budget' : $budget->type }}
                        </span>
                        <h3 class="text-lg font-bold text-charcoal mt-2 group-hover:text-peach-400 transition-colors">
                            <a href="{{ route('budgets.show', $budget->uuid) }}" class="focus:outline-none">
                                {{ $budget->name }}
                            </a>
                        </h3>
                    </div>
                    <form method="POST" action="{{ route('budgets.destroy', $budget->uuid) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Archive this budget?')" class="text-stone-300 hover:text-rose-500 font-bold text-sm p-1">
                            🗑️
                        </button>
                    </form>
                </div>

                @if($budget->description)
                    <p class="text-xs text-stone-500 line-clamp-2 leading-relaxed">{{ $budget->description }}</p>
                @endif

                <div class="space-y-1">
                    <div class="flex justify-between text-xs font-medium text-stone-400">
                        <span>Spent: {{ $setting->currency_symbol }}{{ number_format($budget->spent_amount, 2) }}</span>
                        <span>Total: {{ $setting->currency_symbol }}{{ number_format($budget->total_amount, 2) }}</span>
                    </div>
                    <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $budget->remaining_amount < 0 ? 'bg-rose-500' : 'bg-peach-300' }}"
                             style="width: {{ min(100, round(($budget->spent_amount / max(1, $budget->total_amount)) * 100)) }}%"></div>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-stone-100 text-xs">
                    <div>
                        <span class="text-stone-400 block text-[10px] uppercase font-bold">Remaining</span>
                        <strong class="text-sm font-extrabold {{ $budget->remaining_amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $setting->currency_symbol }}{{ number_format($budget->remaining_amount, 2) }}
                        </strong>
                    </div>
                    <div class="text-right">
                        <a href="{{ route('budgets.show', $budget->uuid) }}" class="inline-flex items-center gap-1 text-xs font-bold text-peach-500 hover:underline">
                            View Details →
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full">
                <x-empty-state type="budgets" />
            </div>
        @endforelse
    </div>
</x-app-layout>
