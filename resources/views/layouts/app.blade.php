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

    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />

    {{-- Vite --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="icon" href="{{ asset('favicon.ico') }}">

    @stack('head')

    <style>
        :root { color-scheme: light dark; }
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Noto Sans, Ubuntu, Cantarell, Helvetica Neue, Arial, "Apple Color Emoji", "Segoe UI Emoji"; }
        .gradient {
            background:
                radial-gradient(1000px 500px at 10% 0%, rgba(99,102,241,.12), transparent 70%),
                radial-gradient(800px 400px at 90% 10%, rgba(16,185,129,.10), transparent 70%);
        }
        .nav-item > a.active { font-weight: 600; }
    </style>
</head>

<body class="min-h-screen bg-neutral-50 text-neutral-900 dark:bg-neutral-950 dark:text-neutral-100 gradient">
<div id="app" class="min-h-screen d-flex">

    {{-- Sidebar --}}
    <aside class="border-end border-neutral-200/70 dark:border-neutral-800 p-3" style="width: 260px;">
        <a href="{{ route('home') }}" class="d-flex align-items-center gap-2 mb-3 text-decoration-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="text-indigo-600 dark:text-indigo-400" width="26" height="26" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0 7-7 7 7M5 10v10a2 2 0 0 0 2 2h3m10-12 2 2m-2-2v10a2 2 0 0 1-2 2h-3"/>
            </svg>
            <span class="fs-5 fw-bold">{{ config('app.name', 'AttendanceLive') }}</span>
        </a>

        <div class="small text-muted mb-2">Main</div>
        <ul class="nav flex-column mb-4">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
        </ul>

        <div class="small text-muted mb-2">Pages</div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('attendance.index') ? 'active' : '' }}"
                   href="{{ route('attendance.index') }}">
                    <i class="bi bi-clipboard-check me-2"></i> Attendance
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}"
                   href="{{ route('users.index') }}">
                    <i class="bi bi-people me-2"></i> Users
                </a>
            </li>
        </ul>

        <hr class="my-4">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-secondary w-100">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </aside>

    {{-- Main content --}}
    <div class="flex-fill d-flex flex-column" style="min-width:0;">
        {{-- Top bar --}}
        <header class="w-100 py-3 px-3 border-bottom border-neutral-200/70 dark:border-neutral-800 d-flex justify-content-between align-items-center">
            <div class="fw-semibold">@yield('title', 'Dashboard')</div>
            <div class="small text-muted">
                @php $user = auth()->user(); @endphp
                Hi, <strong>{{ $user?->name ?? 'Guest' }}</strong>
            </div>
        </header>

        {{-- Page content --}}
        <main class="flex-fill">
            @yield('content')
        </main>

        <footer class="py-3 px-3 border-top border-neutral-200/70 dark:border-neutral-800 text-center text-xs text-neutral-500 dark:text-neutral-400">
            © {{ now()->year }} {{ config('app.name', 'AttendanceLive') }}. All rights reserved.
        </footer>
    </div>
</div>

@stack('scripts')

<noscript>
    <div style="position:fixed;bottom:0;left:0;right:0;padding:10px;background:#fee2e2;color:#7f1d1d;text-align:center;font-family:system-ui,-apple-system;">
        JavaScript is disabled. Some features may not work properly.
    </div>
</noscript>
</body>
</html>
