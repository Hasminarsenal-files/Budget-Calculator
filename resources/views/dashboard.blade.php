<x-app-layout>
    <x-slot name="title">Dashboard</x-slot>

    <!-- 1. Cat Companion & Cat Piggy Bank Card Header Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Cat Companion Component -->
        <x-cat-companion :remainingAmount="$remainingBudget" :totalBudget="$totalBudget" :currency="$setting->currency_symbol" />

        <!-- Cat Piggy Bank Card -->
        <x-cat-piggy-bank 
            :currentSavings="$savingsGoals->sum('current_amount')" 
            :targetSavings="$savingsGoals->sum('target_amount') ?: 10000" 
            :goalName="$primaryGoal ? $primaryGoal->name : 'General Savings'"
            :currency="$setting->currency_symbol" />
    </div>

    <!-- Friendly Insights Banner -->
    <div class="bg-gradient-to-r from-peach-100 via-amber-50 to-cream-100 p-5 rounded-3xl border border-peach-200 shadow-sm flex items-center justify-between">
        <div class="flex items-center gap-3">
            <span class="text-2xl">💡</span>
            <div>
                <p class="text-sm font-extrabold text-charcoal leading-snug">{{ $varianceText }}</p>
                <p class="text-xs text-softbrown font-medium mt-0.5">
                    @if($topCategoryName !== 'N/A')
                        "{{ $topCategoryName }}" is currently your biggest expense category.
                    @else
                        {{ $savingsText }}
                    @endif
                </p>
            </div>
        </div>
        <a href="{{ route('reports.index') }}" class="hidden sm:inline-block px-4 py-2 rounded-2xl bg-white text-charcoal font-bold text-xs shadow-xs border border-peach-200 hover:bg-peach-50 transition-all">
            Full Report →
        </a>
    </div>

    <!-- 2. Financial Overview Cards Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-softbrown uppercase">Total Income</span>
            <div class="text-2xl font-extrabold text-emerald-600">
                +{{ $setting->currency_symbol }}{{ number_format($totalIncome, 2) }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-softbrown uppercase">Total Expenses</span>
            <div class="text-2xl font-extrabold text-rose-600">
                -{{ $setting->currency_symbol }}{{ number_format($totalExpenses, 2) }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-softbrown uppercase">Active Budgets</span>
            <div class="text-2xl font-extrabold text-charcoal">
                {{ $setting->currency_symbol }}{{ number_format($totalBudget, 2) }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm space-y-1">
            <span class="text-xs font-bold text-softbrown uppercase">Remaining Safe</span>
            <div class="text-2xl font-extrabold text-emerald-600">
                {{ $setting->currency_symbol }}{{ number_format($remainingBudget, 2) }}
            </div>
        </div>
    </div>

    <!-- 3. Current Budgets Progress Grid -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-bold text-charcoal flex items-center gap-2">
                <span>👛</span> Active Budgets Overview
            </h3>
            <a href="{{ route('budgets.index') }}" class="text-xs font-bold text-peach-500 hover:underline">View All →</a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($budgets as $b)
                <a href="{{ route('budgets.show', $b->uuid) }}" class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm hover:shadow-md transition-all space-y-3 block">
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-extrabold uppercase px-2 py-0.5 rounded-full bg-peach-100 text-peach-900 border border-peach-200">
                            {{ $b->type }}
                        </span>
                        <span class="text-xs font-bold text-stone-400">{{ $setting->currency_symbol }}{{ number_format($b->remaining_amount, 2) }} remaining</span>
                    </div>

                    <h4 class="text-base font-bold text-charcoal">{{ $b->name }}</h4>

                    <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden">
                        <div class="h-3 rounded-full transition-all duration-500 {{ $b->remaining_amount < 0 ? 'bg-rose-500' : 'bg-peach-300' }}"
                             style="width: {{ $b->remaining_percentage }}%"></div>
                    </div>
                </a>
            @empty
                <div class="col-span-full">
                    <x-empty-state type="budgets" />
                </div>
            @endforelse
        </div>
    </div>

    <!-- 4. Visual Charts & Recent Transactions Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Category Spending Breakdown Chart -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>🏷️</span> Spending Breakdown
            </h3>
            @if($categoryBreakdown->count() > 0)
                <div class="h-64 flex items-center justify-center">
                    <canvas id="categoryChart"></canvas>
                </div>
            @else
                <x-empty-state type="transactions" />
            @endif
        </div>

        <!-- Recent Transactions -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                    <span>💸</span> Recent Expenses
                </h3>
                <a href="{{ route('transactions.index') }}" class="text-xs font-bold text-peach-500 hover:underline">View All →</a>
            </div>

            <div class="divide-y divide-stone-100">
                @forelse($recentTransactions as $tx)
                    <div class="py-3 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shadow-xs" style="background-color: {{ $tx->category->color ?? '#FFDFD3' }}">
                                💸
                            </div>
                            <div>
                                <p class="text-sm font-bold text-charcoal leading-tight">{{ $tx->description ?: 'Expense' }}</p>
                                <span class="text-[11px] text-stone-400 font-medium">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y') }} • {{ $tx->payment_method }}</span>
                            </div>
                        </div>
                        <span class="text-sm font-extrabold text-rose-600">-{{ $setting->currency_symbol }}{{ number_format($tx->amount, 2) }}</span>
                    </div>
                @empty
                    <x-empty-state type="transactions" />
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js Engine Script -->
    @if($categoryBreakdown->count() > 0)
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('categoryChart')?.getContext('2d');
            if (ctx) {
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: {!! json_encode($categoryBreakdown->pluck('name')) !!},
                        datasets: [{
                            data: {!! json_encode($categoryBreakdown->pluck('total')) !!},
                            backgroundColor: {!! json_encode($categoryBreakdown->pluck('color')->map(fn($c) => $c ?: '#FFDFD3')) !!},
                            borderWidth: 2,
                            borderColor: '#FFFFFF'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 11 } } }
                        }
                    }
                });
            }
        });
        </script>
    @endif
</x-app-layout>
