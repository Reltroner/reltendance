<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi ini memastikan API bisa diakses oleh aplikasi mobile (Kotlin),
    | web admin (domain tertentu), dan tetap aman di production.
    |
    */

    'paths' => [
        'api/*',
        'login',
        'logout',
        'sanctum/csrf-cookie',
    ],

    // izinkan semua method utama
    'allowed_methods' => ['*'],

    // izinkan origin tertentu (atur via .env)
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    // cache preflight (OPTIONS) 1 jam
    'max_age' => 3600,

    // untuk mobile (Bearer token) => false
    // kalau pakai cookie (SPA web admin) => true
    'supports_credentials' => false,

];
