<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('role', 'admin')->exists()) { // 幂等
            return;
        }

        User::create([
            'name' => env('ADMIN_NAME', 'Admin'),
            'email' => env('ADMIN_EMAIL', 'admin@myblog.test'),
            'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123456')),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);
    }
}