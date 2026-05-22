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
            ->where('customer_id', $customer->id)
            ->where('is_active', true)
            ->get();

        foreach ($templates as $template) {
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

            Todo::firstOrCreate(
                [
                    'blog_id' => $blog->id,
                    'customer_id' => $customer->id,
                    'generated_from_template_id' => $template->id,
                ],
                [
                    'platform_id' => $template->platform_id,
                    'title' => $template->title_template,
                    'content_markdown' => $template->body_template,
                    'status' => $template->default_status,
                    'position' => '0',
                    'due_at' => $dueAt,
                ]
            );
        }
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
