<?php

namespace App\Http\Resources;

use App\Services\MarkdownService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content_markdown' => $this->content_markdown,
            'content_html' => app(MarkdownService::class)->toHtml($this->content_markdown),
            'status' => $this->status,
            'publish_at' => $this->publish_at?->toIso8601String(),
            'created_by' => $this->created_by,
            'updated_by' => $this->updated_by,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author' => AuthorResource::make($this->whenLoaded('author')),
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
        ];
    }
}
