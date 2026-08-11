<x-app-layout>
    <x-slot name="title">Transactions</x-slot>

    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-extrabold text-charcoal">Transactions & Expenses 💸</h2>
            <p class="text-xs text-softbrown mt-0.5">Filter, search, and manage your daily spending records.</p>
        </div>
        <button @click="quickAddOpen = true; quickAddTab = 'expense'" 
                class="px-4 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
            + Log Expense
        </button>
    </div>

    <!-- Search & Filters Toolbar -->
    <form method="GET" action="{{ route('transactions.index') }}" class="bg-white p-4 rounded-3xl border border-cream-200 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-3">
        <div>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..." class="w-full px-4 py-2.5 rounded-2xl bg-cream-50 border border-stone-200 text-xs font-medium">
        </div>
        <div>
            <select name="category_id" class="w-full px-4 py-2.5 rounded-2xl bg-cream-50 border border-stone-200 text-xs font-medium">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->uuid }}" {{ request('category_id') == $c->uuid ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <select name="payment_method" class="w-full px-4 py-2.5 rounded-2xl bg-cream-50 border border-stone-200 text-xs font-medium">
                <option value="">All Payment Methods</option>
                <option value="Cash" {{ request('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                <option value="GCash" {{ request('payment_method') == 'GCash' ? 'selected' : '' }}>GCash</option>
                <option value="Bank Transfer" {{ request('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="Debit Card" {{ request('payment_method') == 'Debit Card' ? 'selected' : '' }}>Debit Card</option>
                <option value="Credit Card" {{ request('payment_method') == 'Credit Card' ? 'selected' : '' }}>Credit Card</option>
            </select>
            <button type="submit" class="px-4 py-2.5 rounded-2xl bg-stone-800 text-white font-bold text-xs hover:bg-stone-900 transition-all">
                Filter
            </button>
        </div>
    </form>

    <!-- Transactions List -->
    <div class="bg-white rounded-3xl border border-cream-200 shadow-sm overflow-hidden divide-y divide-stone-100">
        @forelse($transactions as $tx)
            <div class="p-4 sm:p-5 flex items-center justify-between hover:bg-cream-50/50 transition-colors">
                <div class="flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl flex items-center justify-center text-xl shadow-xs flex-shrink-0" style="background-color: {{ $tx->category->color ?? '#FFDFD3' }}">
                        💸
                    </div>
                    <div>
                        <p class="text-sm font-bold text-charcoal leading-tight">{{ $tx->description ?: 'Expense' }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded-full bg-cream-100 text-stone-600">
                                {{ $tx->category->name ?? 'Uncategorized' }}
                            </span>
                            @if($tx->budget)
                                <span class="text-[10px] font-bold text-softbrown">👛 {{ $tx->budget->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="text-right flex items-center gap-4">
                    <div>
                        <span class="text-base font-extrabold text-rose-600">-{{ $setting->currency_symbol }}{{ number_format($tx->amount, 2) }}</span>
                        <span class="block text-[11px] text-stone-400 font-medium">{{ \Carbon\Carbon::parse($tx->transaction_date)->format('M d, Y') }} • {{ $tx->payment_method }}</span>
                    </div>
                    <form method="POST" action="{{ route('transactions.destroy', $tx->uuid) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this transaction?')" class="text-stone-300 hover:text-rose-500 font-bold text-sm p-1">
                            &times;
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="p-8">
                <x-empty-state type="transactions" />
            </div>
        @endforelse
    </div>

    <!-- Pagination Links -->
    <div class="pt-2">
        {{ $transactions->links() }}
    </div>
</x-app-layout>
