{{-- resources/views/auth/login.blade.php --}}
@extends('auth.layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-neutral-50 dark:bg-neutral-950 p-6">
    <div class="w-full max-w-md">
        {{-- Branding --}}
        <div class="text-center mb-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a2 2 0 002 2h3m10-12l2 2m-2-2v10a2 2 0 01-2 2h-3"/>
                </svg>
                <span class="text-2xl font-extrabold tracking-tight text-neutral-900 dark:text-neutral-100">
                    {{ config('app.name', 'AttendanceLive') }}
                </span>
            </a>
            <p class="mt-2 text-sm text-neutral-600 dark:text-neutral-400">
                Sign in to start your session
            </p>
        </div>

        {{-- Card --}}
        <div class="rounded-2xl border border-neutral-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 shadow-sm p-6">
            {{-- Session status (mis. setelah reset password) --}}
            @if (session('status'))
                <div class="mb-4 rounded-lg border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800 dark:border-emerald-900 dark:bg-emerald-900/30 dark:text-emerald-200">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Global errors --}}
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800 dark:border-rose-900 dark:bg-rose-900/30 dark:text-rose-200">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                {{-- Email --}}
                <label for="email" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">Email</label>
                <div class="mt-1">
                    <input
                        id="email"
                        name="email"
                        type="email"
                        inputmode="email"
                        autocomplete="email"
                        required
                        value="{{ old('email') }}"
                        class="block w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-950 px-3 py-2 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="you@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Password --}}
                <div class="mt-4">
                    <div class="flex items-center justify-between">
                        <label for="password" class="block text-sm font-medium text-neutral-700 dark:text-neutral-300">
                            Password
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                                Forgot password?
                            </a>
                        @endif
                    </div>

                    <div class="mt-1 relative">
                        <input
                            type="password"
                            id="password"
                            name="password"
                            autocomplete="current-password"
                            required
                            class="block w-full rounded-xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-950 px-3 py-2 pr-12 text-neutral-900 dark:text-neutral-100 placeholder-neutral-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            placeholder="••••••••"
                            aria-describedby="password-visibility-hint"
                        >

                        <button
                            type="button"
                            id="togglePassword"
                            class="absolute inset-y-0 right-0 px-3 inline-flex items-center justify-center text-neutral-500 hover:text-neutral-700 dark:text-neutral-400 dark:hover:text-neutral-200 focus:outline-none"
                            aria-label="Show password"
                            aria-pressed="false"
                            data-state="hidden"
                        >
                            {{-- eye (show) --}}
                            <svg class="h-5 w-5" data-icon="eye" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M2.036 12.322a1.012 1.012 0 010-.644C3.423 7.51 7.36 5 12 5c4.64 0 8.577 2.51 9.964 6.678.07.206.07.438 0 .644C20.577 16.49 16.64 19 12 19c-4.64 0-8.577-2.51-9.964-6.678z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            {{-- eye-off (hide) --}}
                            <svg class="h-5 w-5 hidden" data-icon="eye-off" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M3.98 8.223A10.477 10.477 0 001.934 12C3.28 16.09 7.27 19 12 19c.87 0 1.712-.11 2.507-.316M6.228 6.228A10.45 10.45 0 0112 5c4.73 0 8.72 2.91 10.066 7-.37 1.13-1.01 2.16-1.86 3.03M3 3l18 18"/>
                            </svg>
                        </button>
                    </div>

                    <p id="password-visibility-hint" class="sr-only">Use the button to show or hide your password.</p>

                    @error('password')
                        <p class="mt-1 text-sm text-rose-600 dark:text-rose-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Remember me --}}
                <div class="mt-4 flex items-center">
                    <input id="remember" name="remember" type="checkbox"
                        class="h-4 w-4 rounded border-neutral-300 text-indigo-600 focus:ring-indigo-500"
                        {{ old('remember') ? 'checked' : '' }}>
                    <label for="remember" class="ml-2 block text-sm text-neutral-700 dark:text-neutral-300">
                        Remember me
                    </label>
                </div>

                <div class="mt-6">
                    <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Login
                    </button>
                </div>

                {{-- Submit --}}
                <div class="mt-6">
                    <button type="submit"
                            :class="submitting ? 'opacity-70 cursor-not-allowed' : ''"
                            :disabled="submitting"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white font-medium hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <span x-show="!submitting">Login</span>
                        {{-- <span x-show="submitting" class="inline-flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor"
                                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"/>
                            </svg>
                            Processing...
                        </span> --}}
                    </button>
                </div>

                {{-- Register link --}}
                @if (Route::has('register'))
                    <p class="mt-4 text-center text-sm text-neutral-600 dark:text-neutral-400">
                        Don’t have an account?
                        <a href="{{ route('register') }}" class="font-medium text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">
                            Create one
                        </a>
                    </p>
                @endif
            </form>
        </div>

        {{-- Footer --}}
        <p class="mt-6 text-center text-xs text-neutral-500 dark:text-neutral-400">
            © {{ now()->year }} {{ config('app.name', 'AttendanceLive') }}. All rights reserved.
        </p>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input  = document.getElementById('password');
    const toggle = document.getElementById('togglePassword');
    if (!input || !toggle) return;

    const eye    = toggle.querySelector('[data-icon="eye"]');
    const eyeOff = toggle.querySelector('[data-icon="eye-off"]');

    function setState(show) {
        input.type = show ? 'text' : 'password';
        toggle.setAttribute('aria-pressed', String(show));
        toggle.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        toggle.dataset.state = show ? 'visible' : 'hidden';
        // swap icons
        eye.classList.toggle('hidden', show);
        eyeOff.classList.toggle('hidden', !show);
    }

    // click toggle
    toggle.addEventListener('click', function () {
        const show = toggle.dataset.state !== 'visible';
        setState(show);
    });

    // keyboard: Ctrl+Shift+P untuk toggle (opsional, akses cepat)
    input.addEventListener('keydown', function (e) {
        if ((e.ctrlKey || e.metaKey) && e.shiftKey && (e.key === 'P' || e.key === 'p')) {
            e.preventDefault();
            const show = toggle.dataset.state !== 'visible';
            setState(show);
        }
    });

    // keamanan kecil: kembalikan ke hidden saat form submit
    const form = input.closest('form');
    if (form) {
        form.addEventListener('submit', () => setState(false));
    }
});
</script>
@endpush
@endsection
