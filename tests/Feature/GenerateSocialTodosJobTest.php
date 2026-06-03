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
        ]);

        return [$customer, $template, $blog];
    }

    public function test_job_creates_todos_from_active_templates(): void
    {
        // Blog observer dispatches the job synchronously (QUEUE_CONNECTION=sync in tests)
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $this->assertDatabaseHas('todos', [
            'blog_id' => $blog->id,
            'customer_id' => $customer->id,
            'generated_from_template_id' => $template->id,
        ]);
    }

    public function test_job_is_idempotent_on_retry(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        // Run the job a second time manually
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
        // Create customer with automation disabled; blog observer still dispatches, but job should no-op
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

        $blog = Blog::factory()->create(['customer_id' => $customer->id, 'created_by' => $user->id]);

        $this->assertDatabaseMissing('todos', ['blog_id' => $blog->id]);
    }

    public function test_todo_has_non_null_due_date(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

        $todo = Todo::where('blog_id', $blog->id)->firstOrFail();
        $this->assertNotNull($todo->due_at);
    }

    public function test_job_skips_when_generated_todo_already_exists(): void
    {
        [$customer, $template, $blog] = $this->makeCustomerWithTemplate();

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

        $blog = Blog::factory()->create(['customer_id' => $customer->id, 'created_by' => $user->id]);

        $this->assertDatabaseMissing('todos', ['blog_id' => $blog->id]);
    }
}
