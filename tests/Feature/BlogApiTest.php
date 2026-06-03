<?php

namespace Tests\Feature;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogApiTest extends TestCase
{
    use RefreshDatabase;

    private function member(): array
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();
        $user->customers()->attach($customer);

        return [$user, $customer];
    }

    public function test_unauthenticated_cannot_list_blogs(): void
    {
        $customer = Customer::factory()->create();

        $this->getJson("/api/v1/customers/{$customer->id}/blogs")
            ->assertUnauthorized();
    }

    public function test_member_can_list_blogs(): void
    {
        [$user, $customer] = $this->member();
        Blog::factory()->count(3)->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/blogs")
            ->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_non_member_cannot_list_blogs(): void
    {
        $customer = Customer::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/blogs")
            ->assertForbidden();
    }

    public function test_member_can_create_blog(): void
    {
        [$user, $customer] = $this->member();

        $this->actingAs($user, 'sanctum')
            ->postJson("/api/v1/customers/{$customer->id}/blogs", [
                'title' => 'Test Blog',
                'excerpt' => 'Short excerpt here',
                'content_markdown' => '## Hello',
                'status' => 'draft',
            ])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Test Blog');

        $this->assertDatabaseHas('blogs', ['title' => 'Test Blog', 'customer_id' => $customer->id]);
    }

    public function test_non_member_cannot_create_blog(): void
    {
        $customer = Customer::factory()->create();
        $other = User::factory()->create();

        $this->actingAs($other, 'sanctum')
            ->postJson("/api/v1/customers/{$customer->id}/blogs", [
                'title' => 'Test Blog',
                'excerpt' => 'Short excerpt here',
                'content_markdown' => '## Hello',
                'status' => 'draft',
            ])
            ->assertForbidden();
    }

    public function test_member_can_show_blog(): void
    {
        [$user, $customer] = $this->member();
        $blog = Blog::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/blogs/{$blog->id}")
            ->assertOk()
            ->assertJsonPath('data.id', $blog->id);
    }

    public function test_non_member_cannot_show_blog(): void
    {
        [$user, $customer] = $this->member();
        $other = User::factory()->create();
        $blog = Blog::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($other, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/blogs/{$blog->id}")
            ->assertForbidden();
    }

    public function test_member_can_update_blog(): void
    {
        [$user, $customer] = $this->member();
        $blog = Blog::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->patchJson("/api/v1/customers/{$customer->id}/blogs/{$blog->id}", [
                'title' => 'Updated Title',
                'content_markdown' => '## Updated',
                'status' => 'published',
            ])
            ->assertOk()
            ->assertJsonPath('data.title', 'Updated Title');
    }

    public function test_member_can_delete_blog(): void
    {
        [$user, $customer] = $this->member();
        $blog = Blog::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($user, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}/blogs/{$blog->id}")
            ->assertNoContent();

        $this->assertDatabaseMissing('blogs', ['id' => $blog->id]);
    }

    public function test_non_member_cannot_delete_blog(): void
    {
        [$user, $customer] = $this->member();
        $other = User::factory()->create();
        $blog = Blog::factory()->create(['customer_id' => $customer->id]);

        $this->actingAs($other, 'sanctum')
            ->deleteJson("/api/v1/customers/{$customer->id}/blogs/{$blog->id}")
            ->assertForbidden();
    }

    public function test_admin_can_access_any_customers_blogs(): void
    {
        $customer = Customer::factory()->create();
        Blog::factory()->count(2)->create(['customer_id' => $customer->id]);
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin, 'sanctum')
            ->getJson("/api/v1/customers/{$customer->id}/blogs")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
