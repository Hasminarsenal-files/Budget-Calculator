<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'PURRSE') }} — Your Cute Money Companion</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN & Alpine JS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
                        },
                        peach: {
                            100: '#FFE5EC',
                            200: '#FFC8DD',
                            300: '#FFAAA6',
                        },
                        sage: {
                            100: '#E8F0E6',
                            200: '#C3E0C1',
                            300: '#95C693',
                        },
                        charcoal: '#2D2723',
                        softbrown: '#8D7B68',
                    }
                }
            }
        }
    </script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #FAF7F2;
            color: #2D2723;
            font-family: 'Outfit', sans-serif;
        }
        /* Sleek scrollbar if needed on extra short screens */
        ::-webkit-scrollbar {
            width: 5px;
        }
        ::-webkit-scrollbar-thumb {
            background: #E8DBC8;
            border-radius: 10px;
        }
    </style>
</head>
<body class="min-h-screen min-h-dvh flex flex-col justify-center items-center p-3 sm:p-6 bg-cream-50 text-charcoal antialiased overflow-y-auto">

    <div class="w-full max-w-md my-auto space-y-3 sm:space-y-4 py-2">
        <!-- Logo & Brand Header -->
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-peach-100 border border-peach-200 shadow-sm mb-1.5 sm:mb-2">
                <span class="text-2xl sm:text-3xl">🐾</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-charcoal">PURRSE</h1>
            <p class="mt-0.5 text-xs sm:text-sm text-softbrown font-medium">Your cute little money companion.</p>
        </div>

        {{ $slot }}

        <!-- Footer -->
        <div class="text-center text-[11px] sm:text-xs text-softbrown/70 pt-1">
            &copy; {{ date('Y') }} PURRSE — Offline-First Universal Budget Companion
        </div>
    </div>

</body>
</html>

