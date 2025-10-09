<?php
// app/Providers/RouteServiceProvider.php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Path to the "home" route after login (if you ever use web auth).
     * For API-only projects, this value is rarely used.
     */
    public const HOME = '/home';

    /**
     * Register routes & rate limiting.
     */
    public function boot(): void
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Centralized rate limiters used by the app.
     *
     * Names referenced by middleware: throttle:api, throttle:auth, throttle:password, throttle:upload
     */
    protected function configureRateLimiting(): void
    {
        // Generic API limiter (per user or per IP)
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(
                $request->user()?->id ?? $request->ip()
            );
        });

        // Auth limiter (login, change password while authenticated, etc.)
        // Keyed by email+IP to protect a single account and reduce global IP lockouts
        RateLimiter::for('auth', function (Request $request) {
            $email = strtolower((string) $request->input('email', 'guest'));
            $key   = $email . '|' . $request->ip();

            return Limit::perMinute(10)->by($key);
            // ->response(fn() => response()->json(['message' => 'Too many attempts.'], 429))
            // ->decayMinutes(1);
        });

        // Password endpoints (forgot/reset) – usually stricter
        RateLimiter::for('password', function (Request $request) {
            $email = strtolower((string) $request->input('email', 'guest'));
            $key   = $email . '|' . $request->ip();

            return Limit::perMinute(5)->by($key);
        });

        // Example upload limiter (optional)
        RateLimiter::for('upload', function (Request $request) {
            return Limit::perMinute(20)->by(
                $request->user()?->id ?? $request->ip()
            );
        });
    }
}
