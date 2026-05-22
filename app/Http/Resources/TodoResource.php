<?php

namespace App\Http\Resources;

use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TodoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'blog_id' => $this->blog_id,
            'platform_id' => $this->platform_id,
            'title' => $this->title,
            'content_markdown' => $this->content_markdown,
            'content_html' => app(MarkdownService::class)->toHtml($this->content_markdown),
            'status' => $this->status,
            'position' => $this->position,
            'due_at' => $this->due_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'blog' => BlogResource::make($this->whenLoaded('blog')),
            'platform' => PlatformResource::make($this->whenLoaded('platform')),
            'creator' => AuthorResource::make($this->whenLoaded('creator')),
        ];
    }
}
