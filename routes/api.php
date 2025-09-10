<?php
// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\PasswordController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login',  [AuthController::class, 'login'])->middleware('throttle:auth');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('me',      [AuthController::class, 'me'])->middleware('auth:sanctum');

    // Password endpoints
    Route::post('password/forgot', [PasswordController::class, 'forgot'])
        ->middleware('throttle:password'); // limit permintaan
    Route::post('password/reset',  [PasswordController::class, 'reset'])
        ->middleware('throttle:password');

    // Change password untuk user login
    Route::post('password/change', [PasswordController::class, 'change'])
        ->middleware(['auth:sanctum','throttle:auth']);
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('attendance/history',    [AttendanceController::class, 'history']);
    Route::post('attendance/check-in',  [AttendanceController::class, 'checkIn']);
    Route::post('attendance/check-out', [AttendanceController::class, 'checkOut']);
    Route::get('attendance/{id}',       [AttendanceController::class, 'show']);
});
