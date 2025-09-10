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
            'name'         => ['required','string','max:255'],
            'email'        => ['required','email','unique:users,email'],
            'password'     => ['required','string','min:8','confirmed'],
        ]);

        $user = User::create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => Hash::make($data['password']),
        ]);

        $token = $user->createToken('default')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'user'    => $user,
            'token'   => $token,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email'       => ['required','email'],
            'password'    => ['required','string'],
            'device_name' => ['required','string'],
        ]);

        if (! Auth::attempt($request->only('email','password'))) {
            throw ValidationException::withMessages(['email' => 'Credentials are invalid.']);
        }

        $user = $request->user();

        // Revoke token lama untuk device yang sama (opsional)
        $user->tokens()->where('name', $validated['device_name'])->delete();

        $abilities = ['attendance:create', 'attendance:view', 'profile:read'];
        $token = $user->createToken($validated['device_name'], $abilities)->plainTextToken;

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
