<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#6366f1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>
            @hasSection('title')
                @yield('title') —
            @endif
            {{ config('app.name', 'Licorería') }}
        </title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800|figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        @endif
    </head>
    <body class="h-full font-sans antialiased text-slate-900">
        <div class="relative flex min-h-[100dvh] flex-col bg-slate-50">
            <div class="pointer-events-none fixed inset-0 -z-10 overflow-hidden">
                <div class="absolute -left-40 top-0 h-[28rem] w-[28rem] rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute right-[-10%] top-[10%] h-[22rem] w-[22rem] rounded-full bg-violet-400/15 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/3 h-64 w-96 rounded-full bg-cyan-300/10 blur-3xl"></div>
                <div class="absolute inset-0 bg-[linear-gradient(to_bottom,transparent,rgb(248_250_252)_38%,rgb(248_250_252))]"></div>
            </div>

            @include('layouts.navigation')

            @isset($header)
                <header class="border-b border-white/40 bg-white/55 shadow-[0_1px_0_0_rgb(15_23_42/0.04)] backdrop-blur-xl supports-[backdrop-filter]:bg-white/45">
                    <div class="mx-auto min-w-0 max-w-7xl px-4 py-4 sm:px-6 sm:py-5 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="w-full min-w-0 flex-1 pb-[calc(5.5rem+env(safe-area-inset-bottom))] lg:pb-0">
                {{ $slot }}
            </main>

            <footer class="mt-auto hidden border-t border-white/50 bg-white/40 py-6 text-center text-xs font-medium text-slate-500 backdrop-blur-md lg:block">
                <p class="tracking-wide">{{ config('app.name', 'Licorería') }} · {{ now()->year }}</p>
            </footer>
        </div>
    </body>
</html>
