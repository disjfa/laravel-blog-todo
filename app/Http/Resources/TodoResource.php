<?php

namespace App\Http\Resources;

use App\Models\Todo;
use App\Services\MarkdownService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Todo $todo */
        $todo = $this->resource;

        return [
            'id' => $todo->id,
            'customer_id' => $todo->customer_id,
            'blog_id' => $todo->blog_id,
            'platform_id' => $todo->platform_id,
            'title' => $todo->title,
            'content_markdown' => $todo->content_markdown,
            'content_html' => app(MarkdownService::class)->toHtml($todo->content_markdown),
            'status' => $todo->status,
            'position' => $todo->position,
            'due_at' => filled($todo->due_at)
                ? Carbon::parse((string) $todo->due_at)->toIso8601String()
                : null,
            'created_by' => $todo->created_by,
            'updated_by' => $todo->updated_by,
            'created_at' => $todo->created_at?->toIso8601String(),
            'updated_at' => $todo->updated_at?->toIso8601String(),
            'blog' => BlogResource::make($this->whenLoaded('blog')),
            'platform' => PlatformResource::make($this->whenLoaded('platform')),
            'creator' => AuthorResource::make($this->whenLoaded('creator')),
        ];
    }
}
