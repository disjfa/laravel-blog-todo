<?php

namespace App\Providers;

use App\Models\Blog;
use App\Observers\BlogObserver;
use App\Services\MarkdownService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MarkdownService::class);
    }

    public function boot(): void
    {
        Blog::observe(BlogObserver::class);
    }
}
