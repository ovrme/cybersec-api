<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Creates (or repairs) the admin account from .env values.
     * Safe to run repeatedly — it never duplicates and never
     * downgrades an existing admin.
     */
    public function run(): void
    {
        $email    = env('ADMIN_EMAIL', 'admin@skillshield.app');
        $password = env('ADMIN_PASSWORD');

        if (blank($password)) {
            $this->command->error('Set ADMIN_PASSWORD in .env before seeding the admin user.');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => env('ADMIN_NAME', 'Administrator'),
                'password'          => Hash::make($password),
                'role'              => 'admin',
                'email_verified_at' => now(),   // admin routes require 'verified'
            ],
        );

        $this->command->info("✓ Admin ready: {$email}");
    }
}
