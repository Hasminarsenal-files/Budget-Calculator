<x-app-layout>
    <x-slot name="title">Categories</x-slot>

    <div>
        <h2 class="text-2xl font-extrabold text-charcoal">Categories 🏷️</h2>
        <p class="text-xs text-softbrown mt-0.5">Custom expense & income category tags with pastel color codes.</p>
    </div>

    <!-- Create Custom Category Form -->
    <div class="bg-white p-6 rounded-3xl border border-cream-200 shadow-sm max-w-lg">
        <h3 class="text-sm font-bold text-charcoal mb-4">Add Custom Category</h3>
        <form method="POST" action="{{ route('categories.store') }}" class="space-y-3">
            @csrf
            <div>
                <label class="block text-xs font-bold text-softbrown uppercase mb-1">Category Name</label>
                <input type="text" name="name" required placeholder="e.g. Pet Care, Gaming" class="w-full px-4 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-sm">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Type</label>
                    <select name="type" required class="w-full px-3 py-2.5 rounded-xl bg-cream-50 border border-stone-200 text-xs font-medium">
                        <option value="expense">Expense</option>
                        <option value="income">Income</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-softbrown uppercase mb-1">Pastel Color</label>
                    <input type="color" name="color" value="#FFDFD3" class="w-full h-10 rounded-xl cursor-pointer bg-cream-50 border border-stone-200 p-1">
                </div>
            </div>
            <button type="submit" class="w-full py-3 rounded-2xl bg-peach-300 text-charcoal font-bold text-sm hover:bg-peach-400 transition-all">
                Save Category 🏷️
            </button>
        </form>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($categories as $cat)
            <div class="p-4 rounded-2xl bg-white border border-cream-200 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg shadow-sm" style="background-color: {{ $cat->color }}">
                    🏷️
                </div>
                <div>
                    <p class="text-sm font-bold text-charcoal leading-tight">{{ $cat->name }}</p>
                    <span class="text-[10px] font-extrabold uppercase text-stone-400">{{ $cat->type }}</span>
                </div>
            </div>
        @endforeach
    </div>
</x-app-layout>
