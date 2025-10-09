<?php
// database/seeders/UserSeeder.php
namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan file penampung token ada
        $tokensFile = 'seed_tokens.json';
        $tokensData = [];

        // ===== Admin akun utama =====
        $admin = User::updateOrCreate(
            ['email' => 'admin@reltroner.com'],
            [
                'name'              => 'Admin Reltroner',
                'password'          => Hash::make(env('SEED_ADMIN_PASSWORD', 'Password!234')),
                'is_admin'          => true,
                'phone_number'      => '081200000001',
                'photo'             => null,
                'email_verified_at' => now(),
                'last_login_at'     => null,
                'last_login_ip'     => null,
                'remember_token'    => Str::random(60),
            ]
        );

        // Buat token admin (abilities lengkap, termasuk admin)
        // NOTE: Jangan seed token di production kalau tidak perlu.
        if (app()->environment(['local', 'development'])) {
            // hapus token lama dengan nama sama agar idempotent
            $admin->tokens()->where('name', 'admin-web')->delete();

            $adminToken = $admin->createToken('admin-web', [
                'attendance:view', 'attendance:create', 'attendance:admin', 'profile:read'
            ])->plainTextToken;

            $tokensData['admin'] = [
                'email' => $admin->email,
                'token_name' => 'admin-web',
                'token' => $adminToken,
            ];
        }

        // ===== User demo utama =====
        $user = User::updateOrCreate(
            ['email' => 'user@reltroner.com'],
            [
                'name'              => 'Demo User',
                'password'          => Hash::make(env('SEED_USER_PASSWORD', 'Password!234')),
                'is_admin'          => false,
                'phone_number'      => '081200000002',
                'photo'             => null,
                'email_verified_at' => now(),
                'last_login_at'     => null,
                'last_login_ip'     => null,
                'remember_token'    => Str::random(60),
            ]
        );

        if (app()->environment(['local', 'development'])) {
            $user->tokens()->where('name', 'android-demo')->delete();

            $userToken = $user->createToken('android-demo', [
                'attendance:view', 'attendance:create', 'profile:read'
            ])->plainTextToken;

            $tokensData['user'] = [
                'email' => $user->email,
                'token_name' => 'android-demo',
                'token' => $userToken,
            ];
        }

        // ===== Tambahan: generate beberapa user dummy (non-admin) =====
        // aman di-rerun: gunakan unique email dari factory
        if (app()->environment(['local', 'development'])) {
            \App\Models\User::factory()
                ->count(10)
                ->create([
                    'is_admin' => false,
                ]);
        }

        // Simpan token seed ke storage/app/seed_tokens.json (LOCAL/DEV saja)
        if (!empty($tokensData)) {
            Storage::put($tokensFile, json_encode($tokensData, JSON_PRETTY_PRINT));
            $this->command?->info("Seed tokens saved to storage/app/{$tokensFile}");
        }
    }
}
