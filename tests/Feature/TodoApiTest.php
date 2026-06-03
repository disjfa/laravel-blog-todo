<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Platform;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoApiTest extends TestCase
{
    use RefreshDatabase;

    private function member(): array
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();
        $user->customers()->attach($customer);

        return [$user, $customer];
    }

    private function todoPayload(Customer $customer): array
    {
        $platform = Platform::first();

        return [
            'platform_id' => $platform->id,
            'title' => 'Test Todo',
            'status' => 'todo',
            'due_at' => now()->addDay()->format('Y-m-d H:i:s'),
        ];
    }

    public function test_unauthenticated_cannot_list_todos(): void
    {
        $customer = Customer::factory()->create();

        $this->getJson("/api/v1/customers/{$customer->id}/todos")
            ->assertUnauthorized();
    }

    public function test_member_can_list_todos(): void
    {
        [$user, $customer] = $this->member();
        Todo::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/todos")
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_non_member_cannot_list_todos(): void
    {
        $customer = Customer::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/todos")
            ->assertForbidden();
    }

    public function test_member_can_create_todo(): void
    {
        [$user, $customer] = $this->member();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customers/{$customer->id}/todos", $this->todoPayload($customer))
            ->assertCreated()
            ->assertJsonPath('data.title', 'Test Todo');

        $this->assertDatabaseHas('todos', ['title' => 'Test Todo', 'customer_id' => $customer->id]);
    }

    public function test_non_member_cannot_create_todo(): void
    {
        $customer = Customer::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other, 'sanctum')
            ->postJson("/api/v1/customers/{$customer->id}/todos", $this->todoPayload($customer))
            ->assertForbidden();
    }

    public function test_member_can_show_todo(): void
    {
        [$user, $customer] = $this->member();
        $todo = Todo::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/todos/{$todo->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $todo->id);
    }

    public function test_non_member_cannot_show_todo(): void
    {
        [$user, $customer] = $this->member();
        $other = User::factory()->create();
        $todo = Todo::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/todos/{$todo->id}")
            ->assertForbidden();
    }

    public function test_member_can_update_todo(): void
    {
        [$user, $customer] = $this->member();
        $todo = Todo::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/customers/{$customer->id}/todos/{$todo->id}", [
                'title' => 'Updated Todo',
                'status' => 'in_progress',
                'due_at' => now()->addDays(2)->format('Y-m-d H:i:s'),
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Todo');
    }

    public function test_member_can_delete_todo(): void
    {
        [$user, $customer] = $this->member();
        $todo = Todo::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}/todos/{$todo->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('todos', ['id' => $todo->id]);
    }

    public function test_member_can_move_todo(): void
    {
        [$user, $customer] = $this->member();
        $todo = Todo::factory()->create(['customer_id' => $customer->id, 'status' => 'todo']);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/customers/{$customer->id}/todos/{$todo->id}/move", [
                'status' => 'done',
                'position' => 'a',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'done');
    }

    public function test_admin_can_access_any_customers_todos(): void
    {
        $customer = Customer::factory()->create();
        Todo::factory()->count(2)->create(['customer_id' => $customer->id]);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/todos")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
