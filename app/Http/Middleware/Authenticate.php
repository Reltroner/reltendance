<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Symfony\Component\HttpFoundation\Response;

class Authenticate extends Middleware
{
    protected function redirectTo($request): ?string
    {
        // Untuk API, jangan redirect—biarkan 401 JSON saja
        if ($request->expectsJson() || $request->is('api/*')) {
            return null;
        }

        // Kalau kamu TIDAK punya halaman login web, kembalikan null juga:
        // return null;

        // Atau, kalau kamu PUNYA halaman login SPA:
        // return url('/login');
        return route('login'); // ganti sesuai kebutuhanmu
    }
}
