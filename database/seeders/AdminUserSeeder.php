<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = (string) config('app.admin_email', env('ADMIN_EMAIL', 'admin@example.com'));
        $name = (string) config('app.admin_name', env('ADMIN_NAME', 'Admin User'));
        $password = (string) env('ADMIN_PASSWORD', 'password');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $user->assignRole('admin');
        $user->assignRole('customer');
    }
}
