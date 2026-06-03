<?php

namespace Tests\Unit;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\Todo;
use App\Models\User;
use App\Queries\TodoQuery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_for_blog_scopes_to_blog(): void
    {
        $customer = Customer::factory()->create();
        $user = User::factory()->create();
        $blog = Blog::factory()->create(['customer_id' => $customer->id, 'created_by' => $user->id]);
        $otherBlog = Blog::factory()->create(['customer_id' => $customer->id, 'created_by' => $user->id]);

        Todo::factory()->count(2)->create(['customer_id' => $customer->id, 'blog_id' => $blog->id]);
        Todo::factory()->create(['customer_id' => $customer->id, 'blog_id' => $otherBlog->id]);

        $results = (new TodoQuery)->forBlog($blog)->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->every(fn ($t) => $t->blog_id === $blog->id));
    }

    public function test_for_customer_scopes_to_customer(): void
    {
        $c1 = Customer::factory()->create();
        $c2 = Customer::factory()->create();

        Todo::factory()->count(2)->create(['customer_id' => $c1->id]);
        Todo::factory()->create(['customer_id' => $c2->id]);

        $results = (new TodoQuery)->forCustomer($c1->id)->get();

        $this->assertCount(2, $results);
        $this->assertTrue($results->every(fn ($t) => $t->customer_id === $c1->id));
    }
}
