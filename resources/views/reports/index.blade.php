<x-app-layout>
    <x-slot name="title">Reports & Analytics</x-slot>

    <div>
        <h2 class="text-2xl font-extrabold text-charcoal">Reports & Financial Insights 📈</h2>
        <p class="text-xs text-softbrown mt-0.5">Friendly visual explanations of where your money went.</p>
    </div>

    <!-- Humanized Insights Card -->
    <div class="bg-gradient-to-br from-peach-100 via-amber-50 to-cream-100 p-6 rounded-3xl border border-peach-200 shadow-sm space-y-3">
        <div class="flex items-center gap-2 text-xs font-extrabold uppercase text-peach-900 tracking-wider">
            <span>💡</span> Friendly Purrse Insights
        </div>
        <p class="text-lg font-bold text-charcoal leading-snug">
            "{{ $varianceText }}"
        </p>
        <p class="text-sm font-semibold text-softbrown">
            {{ $topCategoryText }}
        </p>
    </div>

    <!-- Financial Summary Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Total Income</span>
            <div class="text-2xl font-extrabold text-emerald-600 mt-1">
                +{{ $setting->currency_symbol }}{{ number_format($totalIncome, 2) }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Total Expenses</span>
            <div class="text-2xl font-extrabold text-rose-600 mt-1">
                -{{ $setting->currency_symbol }}{{ number_format($totalExpenses, 2) }}
            </div>
        </div>

        <div class="bg-white p-5 rounded-3xl border border-cream-200 shadow-sm">
            <span class="text-xs font-bold text-softbrown uppercase">Net Cash Savings</span>
            <div class="text-2xl font-extrabold {{ $netSavings >= 0 ? 'text-emerald-600' : 'text-rose-600' }} mt-1">
                {{ $setting->currency_symbol }}{{ number_format($netSavings, 2) }}
            </div>
        </div>
    </div>

    <!-- Charts Grid (2x2) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- 1. Category Breakdown Doughnut Chart -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>🏷️</span> Category Expense Breakdown
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="categoryDoughnutChart"></canvas>
            </div>
        </div>

        <!-- 2. Income vs Expenses Bar Chart -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>📊</span> Income vs Expenses (6 Months)
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="incomeVsExpenseBarChart"></canvas>
            </div>
        </div>

        <!-- 3. Spending Trends Line Chart -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>📈</span> Monthly Expense Trends
            </h3>
            <div class="h-64 flex items-center justify-center">
                <canvas id="spendingTrendLineChart"></canvas>
            </div>
        </div>

        <!-- 4. Budget Performance Table -->
        <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm space-y-4">
            <h3 class="text-base font-bold text-charcoal flex items-center gap-2">
                <span>👛</span> Budget Performance
            </h3>
            <div class="space-y-3">
                @forelse($budgets as $b)
                    <div class="p-3 rounded-2xl bg-cream-50/60 border border-stone-100 space-y-2">
                        <div class="flex justify-between items-center text-xs font-bold">
                            <span class="text-charcoal">{{ $b->name }}</span>
                            <span class="{{ $b->remaining_amount < 0 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $setting->currency_symbol }}{{ number_format($b->remaining_amount, 2) }} left
                            </span>
                        </div>
                        <div class="w-full bg-stone-200/60 rounded-full h-2.5 overflow-hidden">
                            <div class="h-full rounded-full {{ $b->remaining_amount < 0 ? 'bg-rose-500' : 'bg-peach-300' }}"
                                 style="width: {{ $b->remaining_percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-stone-400 text-center py-6">No budget data available.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Chart.js Setup Engine -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Category Doughnut Chart
        const doughnutCtx = document.getElementById('categoryDoughnutChart')?.getContext('2d');
        if (doughnutCtx) {
            new Chart(doughnutCtx, {
                type: 'doughnut',
                data: {
                    labels: {!! json_encode($categoryTotals->pluck('name')) !!},
                    datasets: [{
                        data: {!! json_encode($categoryTotals->pluck('total')) !!},
                        backgroundColor: {!! json_encode($categoryTotals->pluck('color')->map(fn($c) => $c ?: '#FFDFD3')) !!},
                        borderWidth: 2,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 11 } } } }
                }
            });
        }

        // 2. Income vs Expense Bar Chart
        const barCtx = document.getElementById('incomeVsExpenseBarChart')?.getContext('2d');
        if (barCtx) {
            new Chart(barCtx, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($cashFlowLabels) !!},
                    datasets: [
                        { label: 'Income', data: {!! json_encode($incomeSeries) !!}, backgroundColor: '#A3C9A8', borderRadius: 8 },
                        { label: 'Expenses', data: {!! json_encode($expenseSeries) !!}, backgroundColor: '#FFAAA6', borderRadius: 8 }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 11 } } } }
                }
            });
        }

        // 3. Spending Trend Line Chart
        const lineCtx = document.getElementById('spendingTrendLineChart')?.getContext('2d');
        if (lineCtx) {
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($cashFlowLabels) !!},
                    datasets: [{
                        label: 'Expenses Trend',
                        data: {!! json_encode($expenseSeries) !!},
                        borderColor: '#FF85A1',
                        backgroundColor: 'rgba(255, 133, 161, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 11 } } } }
                }
            });
        }
    });
    </script>
</x-app-layout>
