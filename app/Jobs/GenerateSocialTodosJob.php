<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\CustomerTodoTemplate;
use App\Models\Todo;
use Carbon\CarbonInterval;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateSocialTodosJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Blog $blog) {}

    public function handle(): void
    {
        $blog = $this->blog;
        $customer = $blog->customer;

        if (! $customer->automation_enabled) {
            return;
        }

        $templates = CustomerTodoTemplate::query()
            ->whereBelongsTo($customer)
            ->where('is_active', true)
            ->get();

        foreach ($templates as $template) {
            $this->createTodoFromTemplate($blog, $template);
        }
    }

    private function createTodoFromTemplate(Blog $blog, CustomerTodoTemplate $template): void
    {
        $existing = Todo::query()
            ->whereBelongsTo($blog)
            ->whereBelongsTo($blog->customer)
            ->where('generated_from_template_id', $template->id)
            ->exists();

        if ($existing) {
            return;
        }

        try {
            $dueAt = $this->resolveDueDate($blog, $template);
        } catch (\Throwable $e) {
            Log::error('GenerateSocialTodosJob: failed to compute due date', [
                'blog_id' => $blog->id,
                'template_id' => $template->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);

            return;
        }

        $todo = new Todo([
            'title' => $template->title_template,
            'content_markdown' => $template->body_template,
            'status' => $template->default_status,
            'position' => '0',
            'due_at' => $dueAt,
        ]);

        $todo->blog()->associate($blog);
        $todo->customer()->associate($blog->customer);
        $todo->platform()->associate($template->platform);
        $todo->generatedFromTemplate()->associate($template);
        $todo->save();
    }

    private function resolveDueDate(Blog $blog, CustomerTodoTemplate $template): \DateTimeInterface
    {
        $offset = $template->due_offset_iso8601;

        if (empty($offset)) {
            throw new \RuntimeException("Template {$template->id} has no due_offset_iso8601.");
        }

        $interval = CarbonInterval::make($offset);

        if ($interval === null) {
            throw new \RuntimeException("Template {$template->id} has an unparseable due_offset_iso8601: {$offset}");
        }

        $base = $blog->publish_at ?? $blog->created_at;

        return $base->copy()->add($interval);
    }
}
