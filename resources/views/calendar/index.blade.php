<x-app-layout>
    <x-slot name="title">Financial Calendar</x-slot>

    <!-- Header Navigation -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-charcoal">Financial Calendar 📅</h2>
            <p class="text-xs text-softbrown mt-0.5">Track daily income, expenses, budget deadlines & savings target dates.</p>
        </div>

        <div class="flex items-center gap-2 bg-white px-4 py-2 rounded-2xl border border-cream-200 shadow-sm">
            <a href="{{ route('calendar.index', ['year' => $currentMonth->copy()->subMonth()->year, 'month' => $currentMonth->copy()->subMonth()->month]) }}" 
               class="p-1.5 text-stone-400 hover:text-charcoal font-bold text-sm">
                ←
            </a>
            <span class="text-sm font-extrabold text-charcoal min-w-[120px] text-center">
                {{ $currentMonth->format('F Y') }}
            </span>
            <a href="{{ route('calendar.index', ['year' => $currentMonth->copy()->addMonth()->year, 'month' => $currentMonth->copy()->addMonth()->month]) }}" 
               class="p-1.5 text-stone-400 hover:text-charcoal font-bold text-sm">
                →
            </a>
        </div>
    </div>

    <!-- Calendar Grid Card -->
    <div class="bg-white rounded-3xl border border-cream-200 shadow-sm overflow-hidden p-4 sm:p-6"
         x-data="{ selectedDate: null, selectedEvents: [] }">
        
        <!-- Day Names Header -->
        <div class="grid grid-cols-7 text-center font-bold text-xs text-softbrown mb-3 uppercase tracking-wider">
            <div>Sun</div>
            <div>Mon</div>
            <div>Tue</div>
            <div>Wed</div>
            <div>Thu</div>
            <div>Fri</div>
            <div>Sat</div>
        </div>

        <!-- Days Grid -->
        <div class="grid grid-cols-7 gap-1.5 sm:gap-2">
            @php
                $startOfWeek = $currentMonth->copy()->startOfMonth()->startOfWeek(Carbon\Carbon::SUNDAY);
                $endOfWeek = $currentMonth->copy()->endOfMonth()->endOfWeek(Carbon\Carbon::SATURDAY);
                $dateCursor = $startOfWeek->copy();
            @endphp

            @while($dateCursor->lte($endOfWeek))
                @php
                    $dateStr = $dateCursor->toDateString();
                    $isCurrentMonth = $dateCursor->month === $currentMonth->month;
                    $isToday = $dateCursor->isToday();

                    $dayTx = $transactions->where('transaction_date', $dateStr);
                    $dayInc = $incomes->where('date', $dateStr);
                    $dayBud = $budgetDeadlines->where('end_date', $dateStr);
                    $daySav = $savingsDeadlines->where('target_date', $dateStr);

                    $txTotal = $dayTx->sum('amount');
                    $incTotal = $dayInc->sum('amount');
                    $hasEvents = $dayTx->count() || $dayInc->count() || $dayBud->count() || $daySav->count();
                @endphp

                <div @click="selectedDate = '{{ $dateStr }}'; selectedEvents = {{ json_encode([
                        'expenses' => $dayTx->values(),
                        'incomes' => $dayInc->values(),
                        'budgets' => $dayBud->values(),
                        'savings' => $daySav->values()
                     ]) }}"
                     class="min-h-[70px] sm:min-h-[90px] p-2 rounded-2xl border transition-all cursor-pointer flex flex-col justify-between
                        {{ $isCurrentMonth ? 'bg-cream-50/50 hover:bg-peach-50 border-stone-200/60' : 'bg-stone-50/40 opacity-40 border-transparent' }}
                        {{ $isToday ? 'ring-2 ring-peach-300 bg-peach-50/70 font-bold' : '' }}">
                    
                    <div class="flex justify-between items-center text-xs">
                        <span class="font-bold {{ $isToday ? 'text-peach-500' : 'text-stone-700' }}">{{ $dateCursor->day }}</span>
                        @if($hasEvents)
                            <span class="w-2 h-2 rounded-full bg-peach-400"></span>
                        @endif
                    </div>

                    <!-- Small Badges -->
                    <div class="space-y-1 mt-1">
                        @if($incTotal > 0)
                            <div class="bg-emerald-100 text-emerald-900 text-[10px] font-bold px-1.5 py-0.5 rounded-lg truncate">
                                +{{ $setting->currency_symbol }}{{ number_format($incTotal) }}
                            </div>
                        @endif
                        @if($txTotal > 0)
                            <div class="bg-rose-100 text-rose-900 text-[10px] font-bold px-1.5 py-0.5 rounded-lg truncate">
                                -{{ $setting->currency_symbol }}{{ number_format($txTotal) }}
                            </div>
                        @endif
                        @if($dayBud->count())
                            <div class="bg-amber-100 text-amber-900 text-[9px] font-extrabold px-1 rounded truncate">
                                👛 Deadline
                            </div>
                        @endif
                    </div>
                </div>

                @php $dateCursor->addDay(); @endphp
            @endwhile
        </div>

        <!-- Selected Date Details Modal / Drawer -->
        <div x-show="selectedDate" 
             x-transition 
             class="mt-6 p-5 rounded-3xl bg-peach-50 border border-peach-200 space-y-3"
             style="display:none;">
            <div class="flex justify-between items-center">
                <h4 class="text-sm font-bold text-charcoal flex items-center gap-2">
                    <span>📌</span> Details for <span x-text="selectedDate"></span>
                </h4>
                <button @click="selectedDate = null" class="text-stone-400 font-bold text-lg">&times;</button>
            </div>

            <!-- Incomes -->
            <template x-if="selectedEvents.incomes && selectedEvents.incomes.length > 0">
                <div class="space-y-2">
                    <span class="text-[11px] font-extrabold uppercase text-emerald-800 tracking-wider">Income Received</span>
                    <template x-for="inc in selectedEvents.incomes">
                        <div class="flex justify-between items-center p-2.5 rounded-xl bg-white text-xs border border-emerald-100">
                            <span class="font-bold text-charcoal" x-text="inc.source"></span>
                            <span class="font-extrabold text-emerald-600">+{{ $setting->currency_symbol }}<span x-text="parseFloat(inc.amount).toFixed(2)"></span></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Expenses -->
            <template x-if="selectedEvents.expenses && selectedEvents.expenses.length > 0">
                <div class="space-y-2">
                    <span class="text-[11px] font-extrabold uppercase text-rose-800 tracking-wider">Expenses Logged</span>
                    <template x-for="exp in selectedEvents.expenses">
                        <div class="flex justify-between items-center p-2.5 rounded-xl bg-white text-xs border border-rose-100">
                            <span class="font-bold text-charcoal" x-text="exp.description || 'Expense'"></span>
                            <span class="font-extrabold text-rose-600">-{{ $setting->currency_symbol }}<span x-text="parseFloat(exp.amount).toFixed(2)"></span></span>
                        </div>
                    </template>
                </div>
            </template>

            <template x-if="(!selectedEvents.expenses || selectedEvents.expenses.length === 0) && (!selectedEvents.incomes || selectedEvents.incomes.length === 0)">
                <p class="text-xs text-stone-400 italic">No transactions recorded for this day.</p>
            </template>
        </div>
    </div>
</x-app-layout>
