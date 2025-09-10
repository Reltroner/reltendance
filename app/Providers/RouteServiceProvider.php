<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Path ke "home" route setelah login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Daftarkan service provider.
     */
    public function boot(): void
    {
        // konfigurasi rate limiting
        $this->configureRateLimiting();

        // definisi route
        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Rate limiter untuk API dan auth.
     */
    protected function configureRateLimiting(): void
    {
        RateLimiter::for('api', function (Request $request) {
        return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
    });

    RateLimiter::for('auth', function (Request $request) {
        return Limit::perMinute(10)->by($request->ip());
    });

    // Tambahan untuk permintaan forgot/reset
    RateLimiter::for('password', function (Request $request) {
        return Limit::perMinute(5)->by($request->ip());
    });

    // contoh upload limiter (opsional)
    RateLimiter::for('upload', function (Request $request) {
        return Limit::perMinute(20)->by($request->user()?->id ?: $request->ip());
    });
    }
}
