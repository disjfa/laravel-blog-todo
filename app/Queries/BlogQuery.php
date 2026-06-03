<?php

namespace App\Queries;

use App\Models\Blog;
use App\Queries\Concerns\HasCustomerScope;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\QueryBuilder;

class BlogQuery extends QueryBuilder
{
    use HasCustomerScope;

    public function __construct(?Builder $query = null)
    {
        parent::__construct($query ?? Blog::query());

        $this
            ->allowedFilters(
                'title',
                'status',
                'slug',
            )
            ->allowedIncludes(
                'customer',
                'author',
                'assets',
            )
            ->allowedSorts(
                'title',
                'status',
                'created_at',
                'updated_at',
                'publish_at',
            );
    }
}
