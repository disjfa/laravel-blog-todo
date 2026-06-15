<?php

namespace Tests\Feature;

use App\Jobs\GenerateSocialTodosJob;
use App\Models\Blog;
use App\Models\Customer;
use App\Models\CustomerTodoTemplate;
use App\Models\Platform;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class GenerateSocialTodosJobTest extends TestCase
{
    use RefreshDatabase;

    private function makeCustomerWithTemplate(bool $automationEnabled = true): array
    {
        $customer = Customer::factory()->create(['automation_enabled' => $automationEnabled]);
        $platform = Platform::first();
        $user = User::factory()->create();

        $template = CustomerTodoTemplate::create([
            'customer_id' => $customer->id,
            'platform_id' => $platform->id,
            'title_template' => 'Post on {{ platform }}',
            'default_status' => 'todo',
            'due_offset_iso8601' => 'P1D',
            'is_active' => true,
        ]);

        $blog = Blog::factory()->create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'status' => 'draft',
            'publish_at' => null,
        ]);

        return [$customer, $template, $blog];
    }

    public function test_job_creates_todos_from_active_templates(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        // Observer now dispatches only when a blog is updated to published with publish_at set.
        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $this->assertDatabaseHas('todos', [
            'blog_id' => $blog->id,
            'customer_id' => $customer->id,
            'generated_from_template_id' => $template->id,
        ]);
    }

    public function test_job_is_idempotent_on_retry(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        // Run the job a second time manually.
        (new GenerateSocialTodosJob($blog->fresh()))->handle();

        $this->assertSame(
            1,
            Todo::where('blog_id', $blog->id)
                ->where('generated_from_template_id', $template->id)
                ->count()
        );
    }

    public function test_job_skips_when_automation_disabled(): void
    {
        // Create customer with automation disabled.
        $customer = Customer::factory()->create(['automation_enabled' => false]);
        $platform = Platform::first();
        $user = User::factory()->create();

        CustomerTodoTemplate::create([
            'customer_id' => $customer->id,
            'platform_id' => $platform->id,
            'title_template' => 'Post',
            'default_status' => 'todo',
            'due_offset_iso8601' => 'P1D',
            'is_active' => true,
        ]);

        $blog = Blog::factory()->create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'status' => 'draft',
            'publish_at' => null,
        ]);

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $this->assertDatabaseMissing('todos', ['blog_id' => $blog->id]);
    }

    public function test_todo_has_non_null_due_date(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $todo = Todo::where('blog_id', $blog->id)->firstOrFail();
        $this->assertNotNull($todo->due_at);
    }

    public function test_job_skips_when_generated_todo_already_exists(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $existing = Todo::query()
            ->where('blog_id', $blog->id)
            ->where('generated_from_template_id', $template->id)
            ->firstOrFail();

        $originalTitle = $existing->title;

        (new GenerateSocialTodosJob($blog->fresh()))->handle();

        $this->assertSame(1, Todo::query()
            ->where('blog_id', $blog->id)
            ->where('customer_id', $customer->id)
            ->where('generated_from_template_id', $template->id)
            ->count());

        $this->assertSame($originalTitle, $existing->fresh()->title);
    }

    public function test_job_creates_todo_when_missing_for_template(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        Todo::query()
            ->where('blog_id', $blog->id)
            ->where('generated_from_template_id', $template->id)
            ->delete();

        (new GenerateSocialTodosJob($blog->fresh()))->handle();

        $this->assertDatabaseHas('todos', [
            'blog_id' => $blog->id,
            'customer_id' => $customer->id,
            'generated_from_template_id' => $template->id,
            'platform_id' => $template->platform_id,
        ]);
    }

    public function test_inactive_template_does_not_create_todo(): void
    {
        $customer = Customer::factory()->create(['automation_enabled' => true]);
        $platform = Platform::first();
        $user = User::factory()->create();

        CustomerTodoTemplate::create([
            'customer_id' => $customer->id,
            'platform_id' => $platform->id,
            'title_template' => 'Inactive post',
            'default_status' => 'todo',
            'due_offset_iso8601' => 'P1D',
            'is_active' => false,
        ]);

        $blog = Blog::factory()->create([
            'customer_id' => $customer->id,
            'created_by' => $user->id,
            'status' => 'draft',
            'publish_at' => null,
        ]);

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $this->assertDatabaseMissing('todos', ['blog_id' => $blog->id]);
    }

    public function test_job_skips_when_blog_publish_date_is_older_than_one_week(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $blog->update([
            'status' => 'published',
            'publish_at' => Carbon::now()->subDays(8),
        ]);

        $this->assertDatabaseMissing('todos', [
            'blog_id' => $blog->id,
            'generated_from_template_id' => $template->id,
        ]);
    }

    public function test_job_skips_when_any_todo_is_already_connected_to_blog(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        Todo::create([
            'customer_id' => $customer->id,
            'blog_id' => $blog->id,
            'title' => 'Existing todo',
            'content_markdown' => 'Already connected',
            'status' => 'todo',
            'position' => 0,
            'due_at' => now()->addDay(),
            'created_by' => $blog->created_by,
        ]);

        $blog->update([
            'status' => 'published',
            'publish_at' => now(),
        ]);

        $this->assertSame(1, Todo::query()->where('blog_id', $blog->id)->count());
        $this->assertDatabaseMissing('todos', [
            'blog_id' => $blog->id,
            'generated_from_template_id' => $template->id,
        ]);
    }
}
