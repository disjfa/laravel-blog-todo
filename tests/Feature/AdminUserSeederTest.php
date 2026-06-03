<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\AdminUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_user_gets_admin_and_customer_roles(): void
    {
        $this->seed(AdminUserSeeder::class);

        $adminEmail = (string) config('app.admin_email', env('ADMIN_EMAIL', 'admin@example.com'));

        $user = User::query()->where('email', $adminEmail)->firstOrFail();

        $this->assertTrue($user->hasRole('admin'));
        $this->assertTrue($user->hasRole('customer'));
    }
}
