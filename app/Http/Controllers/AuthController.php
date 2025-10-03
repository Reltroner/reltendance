<?php
// app/Http/Controllers/AuthController.php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required','string','max:255'],
            'email'    => ['required','email','unique:users,email'],
            'password' => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $token = $user->createToken('default')->plainTextToken;

        // API response (tetap dipakai untuk endpoint /api/auth/register)
        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        // device_name dibuat OPSIONAL (default: 'web')
        $validated = $request->validate([
            'email'       => ['required','email'],
            'password'    => ['required','string'],
            'device_name' => ['nullable','string'],
        ]);

        if (! Auth::attempt($request->only('email','password'))) {
            // Untuk request web: otomatis redirect back dengan errors
            throw ValidationException::withMessages([
                'email' => 'Credentials are invalid.',
            ]);
        }

        /** @var \App\Models\User $user */
        $user = $request->user();

        // default device name untuk form web
        $deviceName = $validated['device_name'] ?? 'web';

        // Revoke token lama untuk device yang sama (opsional)
        $user->tokens()->where('name', $deviceName)->delete();

        $abilities = ['attendance:create', 'attendance:view', 'profile:read'];
        $token = $user->createToken($deviceName, $abilities)->plainTextToken;

        // Jika ini request API (mis. dari /api/auth/login), balas JSON
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in_minutes' => (int) config('sanctum.expiration'),
                'user' => $user,
            ]);
        }

        // Jika ini request dari form web, redirect dengan cookie token (HttpOnly)
        return redirect()
            ->intended('/')
            ->with('status', 'Logged in')
            ->cookie(
                // simpan token di cookie agar JS front-end bisa konsumsi API bila perlu
                cookie(
                    name: 'api_token',
                    value: $token,
                    minutes: 60 * 24 * 30,          // 30 hari
                    path: '/',
                    domain: null,
                    secure: app()->environment('production'),
                    httpOnly: true,
                    raw: false,
                    sameSite: 'lax'
                )
            );
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()?->delete();

        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Logged out successfully.'], 200);
        }

        // Hapus cookie di web
        return redirect('/')
            ->with('status', 'Logged out')
            ->withCookie(cookie('api_token', '', -1));
    }

    public function me(Request $request)
    {
        return $request->user();
    }
}
