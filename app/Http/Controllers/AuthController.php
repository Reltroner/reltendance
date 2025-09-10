<?php
// app/Http/Controllers/AuthController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'       => ['required','email'],
            'password'    => ['required','string'],
            'device_name' => ['required','string'],
        ]);

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages(['email' => 'Credentials are invalid.']);
        }

        $user = $request->user();

        // Revoke token lama dari device yang sama (opsional best-practice)
        $user->tokens()->where('name', $credentials['device_name'])->delete();

        // Abilities granular untuk mobile
        $abilities = ['attendance:create', 'attendance:view', 'profile:read'];

        $token = $user->createToken($credentials['device_name'], $abilities)->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in_minutes' => (int) config('sanctum.expiration'),
            'user' => $user,
        ]);
    }

    public function logout(Request $request)
    {
        // Hapus token yang dipakai saat ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
