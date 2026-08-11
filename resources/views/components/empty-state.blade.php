@props([
    'type' => 'transactions', // budgets, transactions, savings, generic
    'title' => null,
    'message' => null,
    'actionLabel' => null,
    'actionClick' => null,
    'actionUrl' => null
])

@php
    if (!$title) {
        switch ($type) {
            case 'budgets':
                $title = "No Budgets Yet";
                $message = "Let's give your money a little plan.";
                $actionLabel = "Create your first budget 👛";
                break;
            case 'transactions':
                $title = "Your Wallet is Still Quiet";
                $message = "Log your expenses to let Purrse keep track of your money.";
                $actionLabel = "Add expense 💸";
                break;
            case 'savings':
                $title = "No Savings Goals Set";
                $message = "Where should we save our next coins?";
                $actionLabel = "Set savings goal 🐷";
                break;
            default:
                $title = "Nothing Here Yet";
                $message = "Start adding items to view them here.";
                $actionLabel = "+ Add Item";
                break;
        }
    }
@endphp

<div class="bg-white p-8 md:p-12 rounded-3xl border border-cream-200 shadow-sm text-center space-y-4 max-w-md mx-auto">
    <div class="w-20 h-20 mx-auto rounded-3xl bg-cream-100 border border-cream-200 flex items-center justify-center text-4xl shadow-sm">
        @if($type === 'budgets') 👛 @elseif($type === 'savings') 🐷 @elseif($type === 'transactions') 💸 @else 🐱 @endif
    </div>

    <div class="space-y-1">
        <h4 class="text-lg font-bold text-charcoal">{{ $title }}</h4>
        <p class="text-xs text-softbrown max-w-xs mx-auto leading-relaxed">{{ $message }}</p>
    </div>

    @if($actionLabel)
        <div class="pt-2">
            @if($actionUrl)
                <a href="{{ $actionUrl }}" class="inline-block px-5 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-xs shadow-sm hover:bg-peach-400 transition-all">
                    {{ $actionLabel }}
                </a>
            @elseif($actionClick)
                <button @click="{{ $actionClick }}" class="px-5 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-xs shadow-sm hover:bg-peach-400 transition-all">
                    {{ $actionLabel }}
                </button>
            @else
                <button @click="quickAddOpen = true" class="px-5 py-2.5 rounded-2xl bg-peach-300 text-charcoal font-bold text-xs shadow-sm hover:bg-peach-400 transition-all">
                    {{ $actionLabel }}
                </button>
            @endif
        </div>
    @endif
</div>
