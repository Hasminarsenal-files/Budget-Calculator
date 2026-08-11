<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- PWA Manifest & Colors -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#FAF7F2">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="PURRSE">

    <title>{{ $title ?? 'Dashboard' }} — PURRSE</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN, Alpine JS & Chart.js -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- IndexedDB Offline Storage & Service Worker Sync Engine -->
    <script src="/js/offline.js" defer></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'Plus Jakarta Sans', 'sans-serif'],
                    },
                    colors: {
                        cream: {
                            50: '#FAF7F2',
                            100: '#F4ECE1',
                            200: '#E8DBC8',
                            300: '#DCCBAF',
                        },
                        peach: {
                            50: '#FFF0F3',
                            100: '#FFE5EC',
                            200: '#FFC8DD',
                            300: '#FFAAA6',
                            400: '#FF85A1',
                        },
                        sage: {
                            50: '#F2F7F2',
                            100: '#E8F0E6',
                            200: '#C3E0C1',
                            300: '#A3C9A8',
                            400: '#7A9A7B',
                        },
                        charcoal: '#2D2723',
                        softbrown: '#8D7B68',
                    }
                }
            }
        }
    </script>

    <style>
        body {
            background-color: #FAF7F2;
            color: #2D2723;
            font-family: 'Outfit', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom Scrollbars */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #FAF7F2;
        }
        ::-webkit-scrollbar-thumb {
            background: #E8DBC8;
            border-radius: 9999px;
        }
    </style>
</head>
<body class="h-full flex flex-col antialiased selection:bg-peach-200 selection:text-charcoal" 
      x-data="{ 
          mobileNavOpen: false, 
          quickAddOpen: false,
          quickAddTab: 'expense',
          isOffline: !navigator.onLine 
      }"
      x-init="
          window.addEventListener('online', () => isOffline = false);
          window.addEventListener('offline', () => isOffline = true);
      ">

    <!-- Offline Status Banner -->
    <div x-show="isOffline" 
         x-transition
         class="bg-amber-400 text-amber-950 text-xs font-bold py-2 px-4 text-center flex items-center justify-center gap-2 shadow-sm z-50 sticky top-0">
        <span>📡 You're offline. Your changes are saved on this device and will sync automatically!</span>
    </div>

    <!-- Session Flash Notifications -->
    @if(session('success'))
        <div x-data="{ show: true }" 
             x-show="show" 
             x-init="setTimeout(() => show = false, 5000)"
             class="fixed top-5 right-5 z-50 bg-emerald-800 text-emerald-50 px-5 py-3 rounded-2xl shadow-lg flex items-center gap-3 border border-emerald-700 animate-bounce">
            <span>🐱</span>
            <span class="text-sm font-semibold">{{ session('success') }}</span>
            <button @click="show = false" class="text-emerald-300 hover:text-white font-bold ml-2">&times;</button>
        </div>
    @endif

    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden md:flex flex-col w-60 lg:w-64 bg-white/80 backdrop-blur-md border-r border-cream-200/80 p-3.5 lg:p-5 flex-shrink-0 sticky top-0 h-screen overflow-y-auto z-30">
            <!-- Brand Logo & Cute Network Status -->
            <div class="flex flex-col gap-1.5 mb-3 lg:mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 lg:w-10 lg:h-10 rounded-2xl bg-peach-100 border border-peach-200 flex items-center justify-center text-xl lg:text-2xl shadow-sm">
                        🐾
                    </div>
                    <div>
                        <h1 class="text-lg lg:text-xl font-extrabold tracking-tight text-charcoal leading-none">PURRSE</h1>
                        <span class="text-[10px] lg:text-[11px] font-medium text-softbrown tracking-wide">Cute Money Companion</span>
                    </div>
                </div>

                <!-- Network Status Component Badge -->
                <div class="mt-0.5">
                    <x-network-status />
                </div>
            </div>

            <!-- Nav Links -->
            <nav class="space-y-1 lg:space-y-1.5 flex-1">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('dashboard') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">📊</span> Dashboard
                </a>
                <a href="{{ route('budgets.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('budgets.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">👛</span> Budgets
                </a>
                <a href="{{ route('calendar.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('calendar.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">📅</span> Calendar
                </a>
                <a href="{{ route('transactions.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('transactions.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">💸</span> Transactions
                </a>
                <a href="{{ route('incomes.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('incomes.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">💰</span> Income
                </a>
                <a href="{{ route('savings-goals.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('savings-goals.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">🐷</span> Savings Goals
                </a>
                <a href="{{ route('reports.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('reports.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">📈</span> Reports
                </a>
                <a href="{{ route('categories.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('categories.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">🏷️</span> Categories
                </a>
                <a href="{{ route('profile.index') }}" class="flex items-center gap-2.5 px-3 py-2 lg:py-2.5 rounded-xl text-xs lg:text-sm font-bold transition-all {{ request()->routeIs('profile.*') ? 'bg-peach-100/80 text-charcoal shadow-sm border border-peach-200/60' : 'text-softbrown hover:bg-cream-100/60 hover:text-charcoal' }}">
                    <span class="text-base lg:text-lg">👤</span> Profile & Settings
                </a>
            </nav>

            <!-- Quick Add Button (Desktop) -->
            <div class="pt-3 border-t border-cream-200 mt-2">
                <button @click="quickAddOpen = true" 
                        class="w-full py-2.5 px-3 rounded-xl bg-gradient-to-r from-peach-200 via-peach-300 to-amber-200 text-charcoal font-bold text-xs lg:text-sm shadow-sm hover:shadow-md transition-all flex items-center justify-center gap-1.5">
                    <span class="text-base">+</span> Quick Add Item
                </button>

                <!-- User Account Info & Logout -->
                <div class="mt-2.5 flex items-center justify-between pt-2 border-t border-stone-100">
                    <div class="truncate">
                        <p class="text-xs font-bold text-charcoal truncate">{{ auth()->user()->name ?? 'User' }}</p>
                        <p class="text-[10px] text-softbrown truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-1.5 text-stone-400 hover:text-rose-500 rounded-lg hover:bg-rose-50 transition-colors">
                            🚪
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Workspace Area -->
        <main class="flex-1 flex flex-col min-w-0 pb-24 md:pb-10">

            <!-- Mobile Top Header Bar -->
            <header class="md:hidden flex items-center justify-between px-5 py-4 bg-white/90 backdrop-blur-md border-b border-cream-200 sticky top-0 z-30">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-peach-100 border border-peach-200 flex items-center justify-center text-xl shadow-sm">
                        🐾
                    </div>
                    <div>
                        <span class="text-lg font-extrabold tracking-tight text-charcoal">PURRSE</span>
                        <div class="text-[10px]">
                            <x-network-status />
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button @click="quickAddOpen = true" class="w-9 h-9 rounded-full bg-peach-200 flex items-center justify-center font-bold text-lg text-charcoal shadow-sm">
                        +
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-2 text-stone-400 text-sm">🚪</button>
                    </form>
                </div>
            </header>

            <!-- Main Content Container -->
            <div class="p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto space-y-6">
                {{ $slot }}
            </div>
        </main>

        <!-- Mobile Bottom Navigation Bar -->
        <nav class="md:hidden fixed bottom-3 left-3 right-3 bg-white/95 backdrop-blur-lg border border-stone-200/70 rounded-3xl shadow-xl z-40 px-3 py-2 flex items-center justify-around">
            <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-0.5 text-xs font-bold {{ request()->routeIs('dashboard') ? 'text-peach-400' : 'text-stone-400' }}">
                <span class="text-xl">🏠</span>
                <span>Home</span>
            </a>
            <a href="{{ route('budgets.index') }}" class="flex flex-col items-center gap-0.5 text-xs font-bold {{ request()->routeIs('budgets.*') ? 'text-peach-400' : 'text-stone-400' }}">
                <span class="text-xl">👛</span>
                <span>Budgets</span>
            </a>

            <!-- Central Prominent Add (+) Button -->
            <button @click="quickAddOpen = true" 
                    class="-mt-6 w-14 h-14 rounded-full bg-gradient-to-tr from-peach-300 via-peach-200 to-amber-200 text-charcoal flex items-center justify-center text-3xl font-extrabold shadow-lg border-4 border-white transform active:scale-95 transition-all">
                +
            </button>

            <a href="{{ route('transactions.index') }}" class="flex flex-col items-center gap-0.5 text-xs font-bold {{ request()->routeIs('transactions.*') ? 'text-peach-400' : 'text-stone-400' }}">
                <span class="text-xl">💸</span>
                <span>Expenses</span>
            </a>
            <a href="{{ route('profile.index') }}" class="flex flex-col items-center gap-0.5 text-xs font-bold {{ request()->routeIs('profile.*') ? 'text-peach-400' : 'text-stone-400' }}">
                <span class="text-xl">👤</span>
                <span>Profile</span>
            </a>
        </nav>
    </div>

    <!-- Quick Add Modal -->
    <div x-show="quickAddOpen" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-charcoal/40 backdrop-blur-sm"
         style="display: none;">
        
        <div @click.away="quickAddOpen = false" class="bg-white rounded-3xl p-6 w-full max-w-md shadow-2xl border border-cream-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-stone-100">
                <h3 class="text-lg font-bold text-charcoal flex items-center gap-2">
                    <span>✨</span> Quick Add Entry
                </h3>
                <button @click="quickAddOpen = false" class="text-stone-400 hover:text-stone-700 font-bold text-xl">&times;</button>
            </div>

            <!-- Tab Selectors -->
            <div class="flex bg-cream-50 p-1 rounded-2xl text-xs font-bold text-stone-500">
                <button @click="quickAddTab = 'expense'" :class="quickAddTab === 'expense' ? 'bg-white text-charcoal shadow-sm' : ''" class="flex-1 py-2 rounded-xl transition-all">Expense</button>
                <button @click="quickAddTab = 'income'" :class="quickAddTab === 'income' ? 'bg-white text-charcoal shadow-sm' : ''" class="flex-1 py-2 rounded-xl transition-all">Income</button>
                <button @click="quickAddTab = 'savings'" :class="quickAddTab === 'savings' ? 'bg-white text-charcoal shadow-sm' : ''" class="flex-1 py-2 rounded-xl transition-all">Savings</button>
                <button @click="quickAddTab = 'budget'" :class="quickAddTab === 'budget' ? 'bg-white text-charcoal shadow-sm' : ''" class="flex-1 py-2 rounded-xl transition-all">Budget</button>
            </div>

            <!-- 1. Quick Expense Form with Offline Interception -->
            <form id="quickExpenseForm" x-show="quickAddTab === 'expense'" method="POST" action="{{ route('transactions.store') }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Amount (₱)</label>
                    <input type="number" step="0.01" id="exp_amount" name="amount" required placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-lg font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Category</label>
                    <select id="exp_category" name="category_id" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-medium">
                        @foreach(App\Models\Category::where('type', 'expense')->get() as $cat)
                            <option value="{{ $cat->uuid }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Budget (Optional)</label>
                    <select id="exp_budget" name="budget_id" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-medium">
                        <option value="">No Budget Linked</option>
                        @foreach(App\Models\Budget::where('user_id', auth()->id())->where('status', 'active')->get() as $b)
                            <option value="{{ $b->uuid }}">{{ $b->name }} ({{ $b->type }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Payment Method</label>
                        <select id="exp_payment" name="payment_method" class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs font-medium">
                            <option value="Cash">Cash</option>
                            <option value="GCash">GCash</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Debit Card">Debit Card</option>
                            <option value="Credit Card">Credit Card</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Date</label>
                        <input type="date" id="exp_date" name="transaction_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs font-medium">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Description</label>
                    <input type="text" id="exp_desc" name="description" placeholder="e.g. Lunch at Cafe" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                </div>
                <button type="submit" class="w-full py-3 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm shadow-sm hover:bg-peach-400 transition-all">
                    Log Expense 💸
                </button>
            </form>

            <!-- 2. Quick Income Form -->
            <form x-show="quickAddTab === 'income'" method="POST" action="{{ route('incomes.store') }}" class="space-y-3" style="display:none;">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Income Amount (₱)</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-lg font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Source</label>
                    <input type="text" name="source" required placeholder="e.g. Salary, Freelance, Gift" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Date Received</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                </div>
                <button type="submit" class="w-full py-3 rounded-2xl bg-emerald-300 text-charcoal font-bold text-sm shadow-sm hover:bg-emerald-400 transition-all">
                    Log Income 💰
                </button>
            </form>

            <!-- 3. Quick Savings Form -->
            <form x-show="quickAddTab === 'savings'" method="POST" action="{{ route('savings-goals.contribute') }}" class="space-y-3" style="display:none;">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Savings Goal</label>
                    <select name="savings_goal_id" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-medium">
                        @foreach(App\Models\SavingsGoal::where('user_id', auth()->id())->get() as $goal)
                            <option value="{{ $goal->uuid }}">{{ $goal->name }} (Target: ₱{{ number_format($goal->target_amount) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Contribution Amount (₱)</label>
                    <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-lg font-bold">
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Date</label>
                    <input type="date" name="contribution_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                </div>
                <button type="submit" class="w-full py-3 rounded-2xl bg-purple-300 text-charcoal font-bold text-sm shadow-sm hover:bg-purple-400 transition-all">
                    Add Contribution 🐷
                </button>
            </form>

            <!-- 4. Quick Budget Form -->
            <form x-show="quickAddTab === 'budget'" method="POST" action="{{ route('budgets.store') }}" class="space-y-3" style="display:none;">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Budget Name</label>
                    <input type="text" name="name" required placeholder="e.g. Vacation Trip, Monthly Allowance" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Type</label>
                        <select name="type" required class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs font-medium">
                            <option value="monthly">Monthly</option>
                            <option value="weekly">Weekly</option>
                            <option value="daily">Daily</option>
                            <option value="trip">Trip</option>
                            <option value="event">Event</option>
                            <option value="project">Project</option>
                            <option value="custom">Custom</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Total Amount (₱)</label>
                        <input type="number" step="0.01" name="total_amount" required placeholder="0.00" class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm font-bold">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">Start Date</label>
                        <input type="date" name="start_date" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-softbrown uppercase mb-1">End Date</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs">
                    </div>
                </div>
                <button type="submit" class="w-full py-3 rounded-2xl bg-amber-300 text-charcoal font-bold text-sm shadow-sm hover:bg-amber-400 transition-all">
                    Create Budget 👛
                </button>
            </form>
        </div>
    </div>

    <!-- Quick Expense Offline Interception Script -->
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.getElementById('quickExpenseForm');
        if (form) {
            form.addEventListener('submit', async (e) => {
                if (!navigator.onLine || window.PurrseSyncEngine) {
                    e.preventDefault();
                    const payload = {
                        uuid: crypto.randomUUID(),
                        user_id: {{ auth()->id() ?? 0 }},
                        amount: parseFloat(document.getElementById('exp_amount')?.value || 0),
                        category_id: document.getElementById('exp_category')?.value || '',
                        budget_id: document.getElementById('exp_budget')?.value || null,
                        payment_method: document.getElementById('exp_payment')?.value || 'Cash',
                        transaction_date: document.getElementById('exp_date')?.value || new Date().toISOString().split('T')[0],
                        description: document.getElementById('exp_desc')?.value || 'Expense'
                    };

                    if (window.PurrseSyncEngine) {
                        await window.PurrseSyncEngine.saveOfflineTransaction(payload);
                    }
                    
                    if (!navigator.onLine) {
                        // Close modal and reset form
                        document.querySelector('[x-data]').__x.$data.quickAddOpen = false;
                        form.reset();
                    } else {
                        // Submit standard form if online
                        form.submit();
                    }
                }
            });
        }
    });
    </script>

</body>
</html>
