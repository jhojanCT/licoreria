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
    <body class="min-h-[100dvh] font-sans antialiased text-slate-900">
        <div class="relative flex min-h-[100dvh] flex-col lg:flex-row" style="padding: max(0px, env(safe-area-inset-top)) max(0px, env(safe-area-inset-right)) max(0px, env(safe-area-inset-bottom)) max(0px, env(safe-area-inset-left))">
            <div class="pointer-events-none fixed inset-0 -z-10 bg-slate-50 lg:hidden">
                <div class="absolute left-1/4 top-0 h-72 w-72 -translate-x-1/2 rounded-full bg-indigo-400/25 blur-3xl"></div>
                <div class="absolute bottom-20 right-0 h-64 w-64 rounded-full bg-violet-400/20 blur-3xl"></div>
            </div>
            <div class="relative hidden overflow-hidden lg:flex lg:w-[42%] lg:min-h-screen lg:flex-col lg:justify-between bg-gradient-to-br from-indigo-950 via-violet-950 to-slate-950 px-10 py-12 text-white">
                <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'0.03\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E')] opacity-90"></div>
                <div class="relative z-10">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-3 text-white/95 transition hover:text-white">
                        <x-application-logo class="h-10 w-auto shrink-0 fill-current text-indigo-300" />
                        <span class="text-lg font-semibold tracking-tight">{{ config('app.name', 'Licorería') }}</span>
                    </a>
                    <p class="mt-10 max-w-sm text-sm leading-relaxed text-slate-300">
                        Gestión de inventario, ventas, compras y cierre de caja en un solo lugar.
                    </p>
                </div>
                <p class="relative z-10 text-xs text-slate-500">© {{ now()->year }}</p>
            </div>

            <div class="flex flex-1 flex-col justify-center px-4 py-10 sm:px-6 lg:px-12 lg:py-16">
                <div class="mx-auto w-full max-w-md">
                    <div class="mb-8 flex justify-center lg:hidden">
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-800">
                            <x-application-logo class="h-10 w-auto fill-current text-indigo-600" />
                            <span class="font-semibold tracking-tight">{{ config('app.name', 'Licorería') }}</span>
                        </a>
                    </div>

                    <div class="rounded-3xl border border-white/80 bg-white/85 p-6 shadow-soft-lg shadow-slate-900/10 ring-1 ring-slate-900/5 backdrop-blur-xl sm:p-8">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
