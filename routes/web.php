<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\VerifyCsrfToken;

Route::view('/', 'welcome');
Route::view('/login', 'auth.login')->name('login');

// POST /login untuk form web (bukan /api/…)
Route::post('/login', [AuthController::class, 'login'])
    ->withoutMiddleware([VerifyCsrfToken::class]) // jika kamu belum set CSRF di form
    ->middleware(['throttle:auth'])               // rate limiter khusus login
    ->name('login.post');
