<?php

namespace App\Observers;

use App\Jobs\GenerateSocialTodosJob;
use App\Models\Blog;
use Illuminate\Support\Str;

class BlogObserver
{
    public function creating(Blog $blog): void
    {
        $blog->slug = $this->uniqueSlug($blog, $blog->title);
    }

    public function saving(Blog $blog): void
    {
        if (empty($blog->slug)) {
            $blog->slug = $this->uniqueSlug($blog, $blog->title);
        }
    }

    public function updated(Blog $blog): void
    {
        if (! $blog->wasChanged(['status', 'publish_at'])) {
            return;
        }

        if ($blog->status !== 'published' || blank($blog->publish_at)) {
            return;
        }

        GenerateSocialTodosJob::dispatch($blog);
    }

    private function uniqueSlug(Blog $blog, string $title): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i = 1;

        while (Blog::where('slug', $slug)->where('id', '!=', $blog->id ?? '')->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }
}
