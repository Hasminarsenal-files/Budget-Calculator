<x-app-layout>
    <x-slot name="title">{{ $budget->name }}</x-slot>

    <!-- Header Breadcrumb & Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs text-softbrown mb-1">
                <a href="{{ route('budgets.index') }}" class="hover:underline">Budgets</a>
                <span>/</span>
                <span class="font-bold text-charcoal">{{ $budget->name }}</span>
            </div>
            <h2 class="text-2xl font-extrabold text-charcoal flex items-center gap-2">
                <span>{{ $budget->type === 'trip' ? '✈️' : '👛' }}</span>
                <span>{{ $budget->name }}</span>
                @if($budget->type === 'trip')
                    <span class="bg-sky-100 text-sky-900 font-extrabold text-[10px] uppercase px-2.5 py-0.5 rounded-full border border-sky-200">Trip Budget Mode</span>
                @endif
            </h2>
        </div>

        <button @click="quickAddOpen = true; quickAddTab = 'expense'" class="px-4 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
            + Log Expense for this Budget
        </button>
    </div>

    <!-- Budget Overview Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Budget Amount -->
        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Total Allocated</span>
            <div class="text-2xl font-extrabold text-charcoal mt-1">
                {{ $setting->currency_symbol }}{{ number_format($budget->total_amount, 2) }}
            </div>
        </div>

        <!-- Spent -->
        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Total Spent</span>
            <div class="text-2xl font-extrabold text-rose-600 mt-1">
                {{ $setting->currency_symbol }}{{ number_format($spentAmount, 2) }}
            </div>
        </div>

        <!-- Remaining -->
        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Remaining</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1">
                {{ $setting->currency_symbol }}{{ number_format($remainingAmount, 2) }}
            </div>
        </div>

        <!-- Daily Recommended Spending -->
        <div class="bg-gradient-to-br from-amber-50 to-orange-100/60 p-5 rounded-3xl border border-amber-200 shadow-sm">
            <span class="text-xs font-extrabold uppercase text-amber-900">Recommended Daily</span>
            <div class="text-2xl font-extrabold text-amber-950 mt-1">
                {{ $setting->currency_symbol }}{{ number_format($dailyRecommendedSpending, 2) }}<span class="text-xs font-normal">/day</span>
            </div>
            <span class="text-[10px] text-amber-800 font-medium">{{ $daysRemaining }} day(s) remaining</span>
        </div>
    </div>

    <!-- Progress Bar Section -->
    <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-3">
        <div class="flex justify-between items-center text-sm font-bold">
            <span class="text-charcoal">Budget Consumption</span>
            <span class="{{ $percentageUsed > 90 ? 'text-rose-600' : 'text-emerald-600' }}">{{ $percentageUsed }}% Used</span>
        </div>
        <div class="w-full bg-stone-100 rounded-full h-4 overflow-hidden p-0.5 border border-stone-200">
            <div class="h-full rounded-full transition-all duration-500 {{ $percentageUsed > 90 ? 'bg-rose-500' : ($percentageUsed > 70 ? 'bg-amber-400' : 'bg-emerald-400') }}"
                 style="width: {{ $percentageUsed }}%"></div>
        </div>
    </div>

    <!-- Trip Category Breakdown & Recent Expenses Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Category Breakdown -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>🏷️</span> Spending Breakdown by Category
            </h3>
            <div class="space-y-3">
                @forelse($categoryBreakdown as $cat)
                    <div class="flex items-center justify-between p-3 rounded-2xl bg-cream-50/60 border border-stone-100">
                        <div class="flex items-center gap-3">
                            <div class="w-3.5 h-3.5 rounded-full" style="background-color: {{ $cat->color }}"></div>
                            <span class="text-sm font-bold text-charcoal">{{ $cat->name }}</span>
                        </div>
                        <span class="text-sm font-extrabold text-charcoal">{{ $setting->currency_symbol }}{{ number_format($cat->total, 2) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 text-center py-6">No expenses logged under this budget yet.</p>
                @endforelse
            </div>
        </div>

        <!-- Recent Expenses Under this Budget -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>💸</span> Expenses Linked to this Budget
            </h3>
            <div class="space-y-3 divide-y divide-stone-100">
                @forelse($transactions as $tx)
                    <div class="pt-3 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-bold text-charcoal">{{ $tx->description ?: 'Expense' }}</p>
                            <span class="text-[11px] text-stone-400 font-medium">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y') }} • {{ $tx->payment_method }}</span>
                        </div>
                        <span class="text-sm font-extrabold text-rose-600">-{{ $setting->currency_symbol }}{{ number_format($tx->amount, 2) }}</span>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 text-center py-6">No recent expenses logged for this budget.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
