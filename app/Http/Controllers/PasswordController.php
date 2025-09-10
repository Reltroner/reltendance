<?php
// app/Http/Controllers/PasswordController.php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;

class PasswordController extends Controller
{
    /**
     * Kirim reset link ke email (token akan tersimpan di password_reset_tokens).
     * Response selalu generic agar tidak membocorkan apakah email terdaftar.
     */
    public function forgot(Request $request)
    {
        $data = $request->validate([
            'email' => ['required','email'],
        ]);

        // Kirim link reset (menggunakan notifikasi bawaan Laravel)
        $status = Password::sendResetLink(['email' => $data['email']]);

        // Selalu balas generic demi keamanan
        return response()->json([
            'message' => __($status) === __(\Illuminate\Auth\Passwords\PasswordBroker::RESET_LINK_SENT)
                ? 'If your email exists, a reset link has been sent.'
                : 'If your email exists, a reset link has been sent.',
        ]);
    }

    /**
     * Reset password menggunakan token (dari email).
     * Client (web/mobile) mengirim: email, token, password, password_confirmation
     */
    public function reset(Request $request)
    {
        $data = $request->validate([
            'email'                 => ['required','email'],
            'token'                 => ['required','string'],
            'password'              => ['required', 'confirmed', PasswordRule::min(8)
                                            ->mixedCase()
                                            ->numbers()
                                            ->symbols()
                                            ->uncompromised()],
        ]);

        $status = Password::reset(
            $data,
            function (User $user, string $password) {
                $user->forceFill([
                    'password'       => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));

                // Opsional: revoke semua token Sanctum aktif setelah reset
                $user->tokens()->delete();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => 'Password has been reset.'], 200);
        }

        // Token invalid/expired atau email tidak cocok
        return response()->json([
            'message' => __($status),
        ], 422);
    }

    /**
     * Ganti password untuk user yang sudah login (tanpa token).
     */
    public function change(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'current_password'      => ['required','string'],
            'password'              => ['required', 'confirmed', PasswordRule::min(8)
                                            ->mixedCase()
                                            ->numbers()
                                            ->symbols()
                                            ->uncompromised()],
        ]);

        if (! Hash::check($data['current_password'], $user->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 422);
        }

        $user->password = Hash::make($data['password']);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Opsional: logout dari semua device (revoke semua token)
        $user->tokens()->delete();

        return response()->json(['message' => 'Password changed successfully.'], 200);
    }
}
