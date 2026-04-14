<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
        <meta name="theme-color" content="#4f46e5">

        <title>{{ config('app.name', 'Licorería') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=plus-jakarta-sans:400,500,600,700,800&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <script src="https://cdn.tailwindcss.com"></script>
        @endif
    </head>
    <body class="min-h-[100dvh] bg-slate-50 font-sans text-slate-900 antialiased">
        <div class="relative isolate flex min-h-[100dvh] items-center justify-center overflow-hidden px-4 py-8 sm:px-6 lg:px-8">
            <div class="pointer-events-none absolute inset-0 -z-10">
                <div class="absolute -left-28 top-0 h-72 w-72 rounded-full bg-indigo-400/20 blur-3xl"></div>
                <div class="absolute right-0 top-1/3 h-80 w-80 rounded-full bg-violet-400/15 blur-3xl"></div>
                <div class="absolute bottom-0 left-1/2 h-64 w-96 -translate-x-1/2 rounded-full bg-cyan-300/10 blur-3xl"></div>
            </div>

            <main class="w-full max-w-3xl overflow-hidden rounded-3xl border border-white/70 bg-white/85 shadow-soft-lg ring-1 ring-slate-900/5 backdrop-blur-xl">
                <section class="bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-700 px-6 py-10 text-white sm:px-10">
                    <p class="text-xs font-bold uppercase tracking-[0.2em] text-indigo-100/90">Sistema</p>
                    <h1 class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ config('app.name', 'Licorería') }}</h1>
                    <p class="mt-3 max-w-2xl text-sm text-indigo-100/90 sm:text-base">
                        Gestión moderna de inventario, compras, ventas y caja en una sola plataforma.
                    </p>
                </section>

                <section class="space-y-6 px-6 py-8 sm:px-10 sm:py-10">
                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Inventario</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">Control de stock y alertas</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Ventas</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">Registro rapido de operaciones</p>
                        </div>
                        <div class="rounded-2xl border border-slate-200/80 bg-white/80 p-4">
                            <p class="text-xs font-bold uppercase tracking-wider text-slate-500">Caja</p>
                            <p class="mt-1 text-sm font-semibold text-slate-800">Cierres y auditoria diaria</p>
                        </div>
                    </div>

            @if (Route::has('login'))
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    @auth
                                <a href="{{ url('/dashboard') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-soft transition hover:brightness-110">
                                    Ir al panel
                        </a>
                    @else
                                <div class="flex flex-col gap-3 sm:flex-row">
                                    <a href="{{ route('login') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 py-2.5 text-sm font-bold text-white shadow-soft transition hover:brightness-110">
                                        Iniciar sesion
                                    </a>
                        @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex min-h-[44px] items-center justify-center rounded-2xl border border-slate-200/90 bg-white px-6 py-2.5 text-sm font-semibold text-slate-800 shadow-soft transition hover:bg-slate-50">
                                            Crear cuenta
                            </a>
                        @endif
                                </div>
                    @endauth

                            <p class="text-xs font-medium text-slate-500">© {{ now()->year }} {{ config('app.name', 'Licorería') }}</p>
                </div>
                    @endif
                </section>
            </main>
        </div>
    </body>
</html>
