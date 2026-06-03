<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_plain_request_to_me_still_returns_unauthorized(): void
    {
        $this->get('/api/v1/me')
            ->assertUnauthorized()
            ->assertJson(['message' => 'Unauthenticated.']);
    }

    public function test_unauthenticated_cannot_access_me(): void
    {
        $this->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_me_returns_user_with_customers(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $user->customers()->attach($customer);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('id', $user->id)
            ->assertJsonPath('email', $user->email)
            ->assertJsonCount(1, 'customers');
    }

    public function test_unauthenticated_cannot_list_customers(): void
    {
        $this->getJson('/api/v1/customers')->assertUnauthorized();
    }

    public function test_customers_returns_only_users_own_customers(): void
    {
        $user = User::factory()->create();
        $mine = Customer::factory()->count(2)->create();
        $other = Customer::factory()->create();
        $user->customers()->attach($mine);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/customers')
            ->assertOk();

        $ids = collect($response->json())->pluck('id');
        $this->assertCount(2, $ids);
        $this->assertFalse($ids->contains($other->id));
    }
}
