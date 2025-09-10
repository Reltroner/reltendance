<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// ✅ Tambahan penting:
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;

// Sudah benar:
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Kustom URL reset password untuk SPA / mobile
        ResetPassword::createUrlUsing(function ($user, string $token) {
            $base = config('app.frontend_reset_url', env('FRONTEND_RESET_URL', ''));
            if ($base) {
                return rtrim($base, '/')
                    . '?token=' . $token
                    . '&email=' . urlencode($user->getEmailForPasswordReset());
            }

            return url('/reset-password/'.$token.'?email='
                . urlencode($user->getEmailForPasswordReset()));
        });

        // Kustom email verifikasi supaya link diarahkan ke SPA
        VerifyEmail::toMailUsing(function ($notifiable, string $url) {
            $base = config('app.frontend_verify_url', env('FRONTEND_VERIFY_URL', ''));
            if ($base) {
                $qs = parse_url($url, PHP_URL_QUERY);
                $spaUrl = rtrim($base, '/').($qs ? ('?'.$qs) : '');
                return (new MailMessage)
                    ->subject('Verify Email Address')
                    ->line('Click the button below to verify your email address.')
                    ->action('Verify Email', $spaUrl);
            }

            return (new MailMessage)
                ->subject('Verify Email Address')
                ->line('Click the button below to verify your email address.')
                ->action('Verify Email', $url);
        });

        // 🔒 Named limiter untuk login
        RateLimiter::for('auth', function (Request $request) {
            $key = strtolower($request->input('email', 'guest')).'|'.$request->ip();
            return [ Limit::perMinute(10)->by($key) ];
        });

        // 🔒 Named limiter untuk password endpoints (forgot/reset)
        RateLimiter::for('password', function (Request $request) {
            $key = strtolower($request->input('email', 'guest')).'|'.$request->ip();
            return [ Limit::perMinute(5)->by($key) ];
        });
    }
}
