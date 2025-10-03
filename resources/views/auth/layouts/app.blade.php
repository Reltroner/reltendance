{{-- resources/views/auth/layouts/app.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'Codepolitan Attendance'))</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#111827">

    {{-- Fonts (opsional, feel modern) --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    {{-- Vite assets (Laravel 12) --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Favicon (opsional) --}}
    <link rel="icon" href="{{ asset('favicon.ico') }}">

    {{-- Slot tambahan untuk head (mis. reCAPTCHA) --}}
    @stack('head')

    <style>
        :root { color-scheme: light dark; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Noto Sans, Ubuntu, Cantarell, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
        .gradient {
            background:
                radial-gradient(1000px 500px at 10% 0%, rgba(99,102,241,.12), transparent 70%),
                radial-gradient(800px 400px at 90% 10%, rgba(16,185,129,.10), transparent 70%);
        }
    </style>
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100 gradient">
    <div id="app" class="min-h-screen flex flex-col">
        {{-- Header brand kecil (opsional) --}}
        {{-- <header class="w-full py-6">
            <div class="mx-auto max-w-7xl px-6 text-center">
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0 7-7 7 7M5 10v10a2 2 0 0 0 2 2h3m10-12 2 2m-2-2v10a2 2 0 0 1-2 2h-3"/>
                    </svg>
                    <span class="text-xl font-extrabold tracking-tight">
                        {{ config('app.name', 'AttendanceLive') }}
                    </span>
                </a>
            </div>
        </header> --}}

        {{-- Konten halaman auth (login/register/forgot) --}}
        <main class="flex-1">
            @yield('content')
        </main>

        {{-- Footer kecil --}}
        <footer class="py-8 border-t border-neutral-200/70 dark:border-neutral-800">
            <div class="mx-auto max-w-7xl px-6 text-center text-xs text-neutral-500 dark:text-neutral-400">
                © {{ now()->year }} {{ config('app.name', 'AttendanceLive') }}. All rights reserved.
            </div>
        </footer>
    </div>

    {{-- Stack script per halaman (mis. captcha/analytics) --}}
    @stack('scripts')

    <noscript>
        <div style="position:fixed;bottom:0;left:0;right:0;padding:10px;background:#fee2e2;color:#7f1d1d;text-align:center;font-family:system-ui,-apple-system;">
            JavaScript is disabled. Some features may not work properly.
        </div>
    </noscript>
</body>
</html>
