<?php
// routes/web.php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Halaman publik
Route::view('/', 'welcome')->name('welcome');

// Halaman login (guest saja)
Route::middleware('guest')->group(function () {
    Route::view('/login', 'auth.login')->name('login');
    Route::post('/login', [AuthController::class, 'loginWeb'])
        ->middleware('throttle:auth')
        ->name('login.post');
});

// Logout + halaman private
Route::middleware('auth')->group(function () {
    // Home dashboard (view: resources/views/home.blade.php)
    Route::get('/home', \App\Http\Controllers\HomeController::class)->name('home');

    Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');
});
