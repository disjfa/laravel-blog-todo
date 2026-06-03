<?php

namespace App\Http\Resources;

use App\Models\Blog;
use App\Services\MarkdownService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BlogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Blog $blog */
        $blog = $this->resource;

        return [
            'id' => $blog->id,
            'customer_id' => $blog->customer_id,
            'title' => $blog->title,
            'slug' => $blog->slug,
            'excerpt' => $blog->excerpt,
            'content_markdown' => $blog->content_markdown,
            'content_html' => app(MarkdownService::class)->toHtml($blog->content_markdown),
            'status' => $blog->status,
            'publish_at' => filled($blog->publish_at)
                ? Carbon::parse((string) $blog->publish_at)->toIso8601String()
                : null,
            'created_by' => $blog->created_by,
            'updated_by' => $blog->updated_by,
            'created_at' => $blog->created_at?->toIso8601String(),
            'updated_at' => $blog->updated_at?->toIso8601String(),
            'author' => AuthorResource::make($this->whenLoaded('author')),
            'assets' => AssetResource::collection($this->whenLoaded('assets')),
        ];
    }
}
