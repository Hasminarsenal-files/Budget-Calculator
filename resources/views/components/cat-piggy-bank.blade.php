@props([
    'currentSavings' => 0,
    'targetSavings' => 10000,
    'goalName' => 'Savings Goal',
    'currency' => '₱'
])

@php
    $progress = $targetSavings > 0 ? min(100, max(0, round(($currentSavings / $targetSavings) * 100, 1))) : 100;
    
    // Coin count based on progress tier
    if ($progress >= 100) {
        $coinCount = 15;
    } elseif ($progress >= 80) {
        $coinCount = 12;
    } elseif ($progress >= 50) {
        $coinCount = 8;
    } elseif ($progress >= 20) {
        $coinCount = 5;
    } else {
        $coinCount = 2;
    }
@endphp

<div class="bg-gradient-to-br from-purple-100 via-pink-50 to-amber-50 p-6 rounded-3xl border border-purple-200 shadow-sm relative overflow-hidden flex flex-col justify-between">
    <!-- Header -->
    <div class="flex items-center justify-between z-10">
        <div>
            <span class="text-xs font-extrabold uppercase text-purple-800 tracking-wider flex items-center gap-1.5">
                <span>🐷</span> Cat Piggy Bank
            </span>
            <h3 class="text-xl font-extrabold text-charcoal mt-1">{{ $goalName }}</h3>
        </div>
        <div class="text-right">
            <span class="text-2xl font-extrabold text-purple-950">{{ $currency }}{{ number_format($currentSavings, 2) }}</span>
            <span class="block text-xs font-medium text-purple-700">Target: {{ $currency }}{{ number_format($targetSavings, 2) }}</span>
        </div>
    </div>

    <!-- Visual Cat Container with Floating Coins -->
    <div class="relative my-6 py-4 flex items-center justify-center min-h-[140px]">
        
        <!-- Render Floating Coins Around Piggy Cat -->
        <div class="absolute inset-0 pointer-events-none flex items-center justify-center">
            @for ($i = 0; $i < $coinCount; $i++)
                @php
                    $angle = ($i / max(1, $coinCount)) * 360;
                    $radius = rand(50, 85);
                    $x = cos(deg2rad($angle)) * $radius;
                    $y = sin(deg2rad($angle)) * $radius;
                    $delay = ($i * 0.2);
                @endphp
                <span class="absolute text-xl animate-bounce" 
                      style="transform: translate({{ $x }}px, {{ $y }}px); animation-delay: {{ $delay }}s;">
                    🪙
                </span>
            @endfor
        </div>

        <!-- Central Cat Piggy Icon -->
        <div class="relative w-28 h-28 bg-white/90 backdrop-blur-md rounded-full border-4 border-purple-200 flex items-center justify-center text-5xl shadow-md z-10 transform hover:scale-110 transition-transform">
            🐱
            @if($progress >= 100)
                <span class="absolute -top-3 -right-2 text-2xl animate-spin">👑</span>
            @endif
        </div>
    </div>

    <!-- Progress Bar & Percentage -->
    <div class="space-y-2 z-10">
        <div class="flex justify-between items-center text-xs font-bold">
            <span class="text-purple-900">{{ $progress }}% Saved</span>
            <span class="text-purple-700">{{ $currency }}{{ number_format(max(0, $targetSavings - $currentSavings), 2) }} Remaining</span>
        </div>

        <div class="w-full bg-purple-200/60 rounded-full h-3.5 overflow-hidden p-0.5 border border-purple-300/50">
            <div class="h-full rounded-full transition-all duration-700 shadow-sm {{ $progress >= 100 ? 'bg-gradient-to-r from-emerald-400 to-teal-400' : 'bg-gradient-to-r from-purple-400 via-pink-400 to-amber-300' }}"
                 style="width: {{ $progress }}%"></div>
        </div>
    </div>
</div>
