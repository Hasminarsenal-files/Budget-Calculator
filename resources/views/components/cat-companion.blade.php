@props([
    'state' => 'HAPPY', // HAPPY, NORMAL, WORRIED, SAD, OVERSPENT, SAVING, GOAL_COMPLETED, OFFLINE, SYNCING
    'message' => null,
    'remainingAmount' => null,
    'totalBudget' => null,
    'currency' => '₱',
    'compact' => false
])

@php
    // Calculate state if not explicitly passed
    if ($remainingAmount !== null && $totalBudget !== null && $totalBudget > 0) {
        $ratio = $remainingAmount / $totalBudget;
        if ($remainingAmount < 0) {
            $state = 'OVERSPENT';
        } elseif ($ratio < 0.10) {
            $state = 'SAD';
        } elseif ($ratio < 0.30) {
            $state = 'WORRIED';
        } elseif ($ratio < 0.50) {
            $state = 'NORMAL';
        } else {
            $state = 'HAPPY';
        }
    }

    // Default humanized messages
    if (!$message) {
        switch ($state) {
            case 'HAPPY':
                $message = "You're doing great! Your money is safe and happy! 🎉";
                break;
            case 'NORMAL':
                $message = "Budget looks balanced! Keep keeping track! ✨";
                break;
            case 'WORRIED':
                $message = "Careful! You've used over 70% of your budget!";
                break;
            case 'SAD':
                $message = "Oh dear... under 10% remaining. Let's slow down on spending!";
                break;
            case 'OVERSPENT':
                $message = "Oops! You've overspent your planned budget. Time to adjust!";
                break;
            case 'SAVING':
                $message = "Yay! Clink clink! Added to your savings pot! 🪙";
                break;
            case 'GOAL_COMPLETED':
                $message = "HOORAY! Savings goal 100% completed! Party time! 👑🎉";
                break;
            case 'OFFLINE':
                $message = "No signal? That's okay! I'll keep your expenses safe.";
                break;
            case 'SYNCING':
                $message = "Let's send your expenses to the cloud! ☁️";
                break;
        }
    }

    $bgGradients = [
        'HAPPY' => 'from-emerald-50 to-teal-100/60 border-emerald-200 text-emerald-900',
        'NORMAL' => 'from-amber-50 to-orange-100/50 border-amber-200 text-amber-900',
        'WORRIED' => 'from-orange-50 to-amber-100 border-orange-300 text-orange-950',
        'SAD' => 'from-rose-50 to-red-100 border-rose-300 text-rose-950',
        'OVERSPENT' => 'from-red-100 to-rose-200 border-red-400 text-red-950 animate-pulse',
        'SAVING' => 'from-emerald-100 to-sky-100 border-emerald-300 text-emerald-950',
        'GOAL_COMPLETED' => 'from-pink-100 via-purple-100 to-indigo-100 border-pink-300 text-purple-950',
        'OFFLINE' => 'from-amber-100 via-orange-50 to-amber-50 border-amber-300 text-amber-950',
        'SYNCING' => 'from-sky-100 via-teal-50 to-emerald-100 border-sky-300 text-sky-950',
    ];
@endphp

<div x-data="catCompanionComponent('{{ $state }}', '{{ addslashes($message) }}')" 
     x-init="init()"
     class="relative rounded-3xl p-5 md:p-6 bg-gradient-to-br {{ $bgGradients[$state] ?? $bgGradients['HAPPY'] }} border shadow-sm transition-all duration-300">
    <div class="flex flex-col sm:flex-row items-center gap-4">
        <!-- SVG Animated Cat Companion -->
        <div class="relative w-28 h-28 sm:w-32 sm:h-32 flex-shrink-0 flex items-center justify-center">
            
            <!-- Confetti Background Effect for GOAL_COMPLETED -->
            <template x-if="state === 'GOAL_COMPLETED'">
                <div class="absolute inset-0 pointer-events-none flex items-center justify-center animate-bounce">
                    <span class="absolute -top-2 -left-2 text-xl">✨</span>
                    <span class="absolute -top-4 right-0 text-xl">🎉</span>
                    <span class="absolute bottom-0 -left-3 text-xl">🌟</span>
                </div>
            </template>

            <!-- Heart Effect for HAPPY -->
            <template x-if="state === 'HAPPY' || state === 'SAVING'">
                <div class="absolute -top-2 right-2 text-pink-400 text-lg animate-bounce duration-1000">💖</div>
            </template>

            <!-- Wi-Fi / Phone Icon for OFFLINE -->
            <template x-if="state === 'OFFLINE'">
                <div class="absolute -top-2 -right-1 bg-amber-200 border border-amber-400 px-2 py-0.5 rounded-full text-xs font-bold shadow-xs animate-bounce">
                    📱 Offline
                </div>
            </template>

            <!-- Spinning Coin for SYNCING -->
            <template x-if="state === 'SYNCING'">
                <div class="absolute -top-2 -right-1 text-xl animate-spin">
                    🪙
                </div>
            </template>

            <svg viewBox="0 0 200 200" class="w-full h-full drop-shadow-md select-none transform transition-transform hover:scale-105">
                <!-- Cat Body Base -->
                <path d="M 50 160 C 40 100, 160 100, 150 160 C 150 185, 50 185, 50 160 Z" fill="#FFFDF9" stroke="#5D4037" stroke-width="6" />

                <!-- Paws -->
                <ellipse cx="75" cy="168" rx="14" ry="10" fill="#FFF8F0" stroke="#5D4037" stroke-width="5" />
                <ellipse cx="125" cy="168" rx="14" ry="10" fill="#FFF8F0" stroke="#5D4037" stroke-width="5" />

                <!-- Left Ear -->
                <path d="M 55 75 Q 35 25, 80 50 Z" fill="#FFD0D6" stroke="#5D4037" stroke-width="6" stroke-linejoin="round" class="origin-bottom transform transition-transform" :class="state === 'WORRIED' || state === 'SAD' ? 'rotate-[-12deg]' : 'animate-pulse'" />
                <!-- Right Ear -->
                <path d="M 145 75 Q 165 25, 120 50 Z" fill="#FFD0D6" stroke="#5D4037" stroke-width="6" stroke-linejoin="round" class="origin-bottom transform transition-transform" :class="state === 'WORRIED' || state === 'SAD' ? 'rotate-[12deg]' : 'animate-pulse'" />

                <!-- Cat Head Base -->
                <ellipse cx="100" cy="95" rx="55" ry="45" fill="#FFFDF9" stroke="#5D4037" stroke-width="6" />

                <!-- Cheeks Blush -->
                <ellipse cx="65" cy="105" rx="10" ry="6" fill="#FFAAA6" opacity="0.6" />
                <ellipse cx="135" cy="105" rx="10" ry="6" fill="#FFAAA6" opacity="0.6" />

                <!-- Nose -->
                <polygon points="96,102 104,102 100,107" fill="#5D4037" />

                <!-- EYES & EXPRESSIONS ACCORDING TO STATE -->

                <!-- HAPPY, SAVING & SYNCING Eyes -->
                <g x-show="state === 'HAPPY' || state === 'SAVING' || state === 'SYNCING'">
                    <path d="M 72 90 Q 80 80, 88 90" fill="none" stroke="#5D4037" stroke-width="6" stroke-linecap="round" />
                    <path d="M 112 90 Q 120 80, 128 90" fill="none" stroke="#5D4037" stroke-width="6" stroke-linecap="round" />
                    <path d="M 94 110 Q 100 116, 106 110" fill="none" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                </g>

                <!-- NORMAL & OFFLINE Eyes -->
                <g x-show="state === 'NORMAL' || state === 'OFFLINE'">
                    <circle cx="80" cy="88" r="6" fill="#5D4037" />
                    <circle cx="120" cy="88" r="6" fill="#5D4037" />
                    <circle cx="82" cy="86" r="2" fill="#FFF" />
                    <circle cx="122" cy="86" r="2" fill="#FFF" />
                    <path d="M 94 108 Q 100 114, 106 108" fill="none" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                </g>

                <!-- WORRIED Eyes & Sweat Drop -->
                <g x-show="state === 'WORRIED'">
                    <circle cx="80" cy="90" r="7" fill="#5D4037" />
                    <circle cx="120" cy="90" r="7" fill="#5D4037" />
                    <path d="M 72 80 L 88 84" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                    <path d="M 128 80 L 112 84" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                    <ellipse cx="100" cy="112" rx="5" ry="7" fill="#5D4037" />
                    <path d="M 142 75 C 142 70, 148 70, 148 75 C 148 80, 142 82, 142 75 Z" fill="#70D6FF" opacity="0.8" />
                </g>

                <!-- SAD Eyes -->
                <g x-show="state === 'SAD'">
                    <path d="M 72 88 Q 80 96, 88 88" fill="none" stroke="#5D4037" stroke-width="6" stroke-linecap="round" />
                    <path d="M 112 88 Q 120 96, 128 88" fill="none" stroke="#5D4037" stroke-width="6" stroke-linecap="round" />
                    <path d="M 94 114 Q 100 106, 106 114" fill="none" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                    <ellipse cx="74" cy="98" rx="3" ry="5" fill="#70D6FF" />
                    <ellipse cx="126" cy="98" rx="3" ry="5" fill="#70D6FF" />
                </g>

                <!-- OVERSPENT Eyes -->
                <g x-show="state === 'OVERSPENT'">
                    <text x="70" y="95" font-size="20" font-weight="bold" fill="#5D4037" text-anchor="middle">@</text>
                    <text x="130" y="95" font-size="20" font-weight="bold" fill="#5D4037" text-anchor="middle">@</text>
                    <path d="M 92 112 Q 96 108, 100 112 T 108 112" fill="none" stroke="#5D4037" stroke-width="5" stroke-linecap="round" />
                </g>

                <!-- GOAL_COMPLETED Crown & Star Eyes -->
                <g x-show="state === 'GOAL_COMPLETED'">
                    <polygon points="80,50 90,30 100,45 110,30 120,50" fill="#FFD166" stroke="#5D4037" stroke-width="4" />
                    <text x="80" y="93" font-size="18" fill="#FFB703" text-anchor="middle">★</text>
                    <text x="120" y="93" font-size="18" fill="#FFB703" text-anchor="middle">★</text>
                    <path d="M 92 108 Q 100 120, 108 108 Z" fill="#E63946" stroke="#5D4037" stroke-width="4" />
                </g>

                <!-- Gold Coin in Paw for SAVING state -->
                <g x-show="state === 'SAVING'">
                    <circle cx="100" cy="148" r="16" fill="#FFD166" stroke="#5D4037" stroke-width="4" />
                    <text x="100" y="154" font-size="16" font-weight="bold" fill="#5D4037" text-anchor="middle">₱</text>
                </g>

                <!-- Whiskers -->
                <line x1="30" y1="95" x2="52" y2="98" stroke="#5D4037" stroke-width="4" stroke-linecap="round" />
                <line x1="28" y1="105" x2="52" y2="104" stroke="#5D4037" stroke-width="4" stroke-linecap="round" />
                <line x1="170" y1="95" x2="148" y2="98" stroke="#5D4037" stroke-width="4" stroke-linecap="round" />
                <line x1="172" y1="105" x2="148" y2="104" stroke="#5D4037" stroke-width="4" stroke-linecap="round" />
            </svg>
        </div>

        <!-- Speech Bubble Text -->
        <div class="flex-1 text-center sm:text-left">
            <div class="inline-block bg-white/90 backdrop-blur-md px-4 py-2.5 rounded-2xl shadow-sm border border-stone-200/60 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-stone-500 block mb-0.5">Purrse Companion</span>
                <p class="text-sm md:text-base font-semibold leading-relaxed" x-text="message">
                    {{ $message }}
                </p>
            </div>

            @if($remainingAmount !== null)
                <div class="mt-1 flex items-center justify-center sm:justify-start gap-2 text-xs font-medium opacity-90">
                    <span>Remaining: <strong class="font-bold text-sm">{{ $currency }}{{ number_format($remainingAmount, 2) }}</strong></span>
                    @if($totalBudget)
                        <span class="text-stone-400">•</span>
                        <span>Total: {{ $currency }}{{ number_format($totalBudget, 2) }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function catCompanionComponent(initialState, initialMessage) {
    return {
        state: initialState,
        message: initialMessage,
        defaultState: initialState,
        defaultMessage: initialMessage,
        init() {
            if (!navigator.onLine) {
                this.setOffline();
            }

            window.addEventListener('offline', () => {
                this.setOffline();
            });

            window.addEventListener('online', () => {
                this.state = 'SYNCING';
                this.message = "Let's send your expenses to the cloud! ☁️";
            });

            window.addEventListener('purrse:sync-success', () => {
                this.state = 'HAPPY';
                this.message = "All expenses are synced and safe in the cloud! 🎉";
                setTimeout(() => {
                    this.state = this.defaultState;
                    this.message = this.defaultMessage;
                }, 4000);
            });
        },
        setOffline() {
            this.state = 'OFFLINE';
            this.message = "No signal? That's okay! I'll keep your expenses safe.";
        }
    }
}
</script>
