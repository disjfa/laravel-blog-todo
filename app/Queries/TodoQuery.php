<?php

namespace App\Queries;

use App\Models\Blog;
use App\Models\Todo;
use App\Queries\Concerns\HasCustomerScope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class TodoQuery extends QueryBuilder
{
    use HasCustomerScope;

    public function __construct(?Builder $query = null)
    {
        parent::__construct($query ?? Todo::query());

        $this
            ->allowedFilters(
                'title',
                'status',
                'platform_id',
                'blog_id',
            )
            ->allowedIncludes(
                'customer',
                'blog',
                'platform',
                'creator',
            )
            ->allowedSorts(
                'title',
                'status',
                'position',
                'due_at',
                'created_at',
                'updated_at',
            );
    }

    public function forBlog(Blog $blog): static
    {
        return $this->whereBelongsTo($blog);
    }
}
