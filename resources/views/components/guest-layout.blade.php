<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-cream-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#FAF7F2">
    <title>{{ $title ?? 'Welcome' }} — PURRSE</title>
    <link rel="manifest" href="/manifest.json">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-cream-50 font-sans text-charcoal flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
        <!-- Logo & Brand Header -->
        <a href="/" class="inline-flex flex-col items-center group">
            <div class="w-16 h-16 rounded-3xl bg-peach-200 border-2 border-peach-300 flex items-center justify-center text-3xl shadow-sm group-hover:scale-105 transition-transform">
                🐾
            </div>
            <h1 class="mt-3 text-3xl font-extrabold tracking-tight text-charcoal">PURRSE</h1>
            <p class="text-xs font-semibold text-softbrown mt-0.5">Your cute little money companion</p>
        </a>
    </div>

    <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
        <div class="bg-white py-8 px-6 shadow-sm rounded-3xl border border-cream-200 sm:px-10">
            {{ $slot }}
        </div>
    </div>
</body>
</html>
