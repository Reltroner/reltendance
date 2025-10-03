<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Codepolitan Attendance') }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>

    {{-- Vite (Laravel 12) – pastikan sudah jalan: npm install && npm run dev/build --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Inter font (opsional, feel modern) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        html, body { height: 100%; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Noto Sans, Ubuntu, Cantarell, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
        .gradient { background: radial-gradient(1200px 600px at 10% 10%, rgba(99,102,241,.12), transparent 70%), radial-gradient(800px 400px at 90% 20%, rgba(16,185,129,.12), transparent 70%); }
    </style>
</head>
<body class="antialiased bg-white dark:bg-neutral-950 text-neutral-800 dark:text-neutral-100 gradient">

    <div class="min-h-screen flex flex-col">

        {{-- Top bar --}}
        <header class="w-full">
            <div class="mx-auto max-w-7xl px-6 py-4 flex items-center justify-between">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 font-semibold tracking-tight">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h3m10-12l2 2m-2-2v10a2 2 0 01-2 2h-3"/>
                    </svg>
                    <span>{{ config('app.name', 'AttendanceLive') }}</span>
                </a>

                <nav class="flex items-center gap-3">
                    @auth
                        <a href="{{ url('/home') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-xl border border-neutral-300/70 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition">
                            Login
                        </a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                Register
                            </a>
                        @endif
                    @endauth
                </nav>
            </div>
        </header>

        {{-- Hero --}}
        <main class="flex-1">
            <section class="mx-auto max-w-7xl px-6 py-16 lg:py-24">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-4xl lg:text-5xl font-extrabold leading-tight tracking-tight">
                            Online Presence for <span class="text-indigo-600 dark:text-indigo-400">Modern Team</span>
                        </h1>
                        <p class="mt-4 text-lg text-neutral-600 dark:text-neutral-300">
                            Laravel 12 + Sanctum (stateless) backend, ready to connect to Android (Kotlin) and Web Admin.
                            Features: check-in photos, GPS tracking, history, and a real-time admin panel.
                        </p>
                        <div class="mt-6 flex flex-wrap gap-3">
                            @auth
                                <a href="{{ url('/home') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                    Enter Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-5 py-3 rounded-xl bg-indigo-600 text-white hover:bg-indigo-700 transition">
                                    Start Login
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="inline-flex items-center px-5 py-3 rounded-xl border border-neutral-300/70 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition">
                                        Register Account
                                    </a>
                                @endif
                            @endauth
                        </div>

                        <div class="mt-8 grid grid-cols-2 gap-4 max-w-xl">
                            <div class="rounded-xl border border-neutral-200/70 dark:border-neutral-800 p-4">
                                <div class="font-semibold">Sanctum Auth</div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Bearer token, abilities, rate-limit, ready for production.
                                </div>
                            </div>
                            <div class="rounded-xl border border-neutral-200/70 dark:border-neutral-800 p-4">
                                <div class="font-semibold">Mobile-Ready API</div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Neat REST endpoints for Kotlin (Retrofit/OkHttp).
                                </div>
                            </div>
                            <div class="rounded-xl border border-neutral-200/70 dark:border-neutral-800 p-4">
                                <div class="font-semibold">Presence + GPS</div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Save photos, locations, & notes per event.
                                </div>
                            </div>
                            <div class="rounded-xl border border-neutral-200/70 dark:border-neutral-800 p-4">
                                <div class="font-semibold">Scalable</div>
                                <div class="text-sm text-neutral-600 dark:text-neutral-400 mt-1">
                                    Modular structure + limit + logging.
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Kartu preview --}}
                    <div class="relative">
                        <div class="rounded-2xl border border-neutral-200/70 dark:border-neutral-800 bg-white/60 dark:bg-neutral-900/60 backdrop-blur p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-4">
                                <div class="font-semibold">Today's Summary</div>
                                <span class="text-xs text-neutral-500">Demo</span>
                            </div>
                            <div class="grid grid-cols-3 gap-4">
                                <div class="rounded-xl bg-indigo-50 dark:bg-indigo-900/30 p-4">
                                    <div class="text-xs text-neutral-500">Present</div>
                                    <div class="text-2xl font-bold text-indigo-700 dark:text-indigo-300">12</div>
                                </div>
                                <div class="rounded-xl bg-emerald-50 dark:bg-emerald-900/30 p-4">
                                    <div class="text-xs text-neutral-500">On Time</div>
                                    <div class="text-2xl font-bold text-emerald-700 dark:text-emerald-300">9</div>
                                </div>
                                <div class="rounded-xl bg-rose-50 dark:bg-rose-900/30 p-4">
                                    <div class="text-xs text-neutral-500">Late</div>
                                    <div class="text-2xl font-bold text-rose-700 dark:text-rose-300">3</div>
                                </div>
                            </div>

                            <div class="mt-6">
                                <div class="text-sm text-neutral-600 dark:text-neutral-400">Recent Activity</div>
                                <ul class="mt-2 space-y-2 text-sm">
                                    <li class="flex items-center justify-between">
                                        <span>Rei — Check-in</span>
                                        <span class="text-neutral-500">08:58</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span>Raina — Check-in</span>
                                        <span class="text-neutral-500">09:02</span>
                                    </li>
                                    <li class="flex items-center justify-between">
                                        <span>Wayne — Check-out</span>
                                        <span class="text-neutral-500">17:10</span>
                                    </li>
                                </ul>
                            </div>

                            <div class="mt-6 flex gap-3">
                                @auth
                                    <a href="{{ url('/home') }}" class="inline-flex px-4 py-2 rounded-lg bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 hover:opacity-90 transition">
                                        Open Dashboard
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" class="inline-flex px-4 py-2 rounded-lg bg-neutral-900 text-white dark:bg-neutral-100 dark:text-neutral-900 hover:opacity-90 transition">
                                        Login
                                    </a>
                                    @if (Route::has('register'))
                                        <a href="{{ route('register') }}" class="inline-flex px-4 py-2 rounded-lg border border-neutral-300/70 dark:border-neutral-700 hover:bg-neutral-100 dark:hover:bg-neutral-900 transition">
                                            Register
                                        </a>
                                    @endif
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        {{-- Footer --}}
        <footer class="py-8 border-t border-neutral-200/70 dark:border-neutral-800">
            <div class="mx-auto max-w-7xl px-6 text-center text-sm text-neutral-600 dark:text-neutral-400">
                © {{ now()->year }} {{ config('app.name', 'AttendanceLive') }}. All rights reserved.
            </div>
        </footer>
    </div>
</body>
</html>
