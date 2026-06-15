<?php

namespace App\Models;

use App\Models\Concerns\SetsUserStamps;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

#[Fillable(['customer_id', 'blog_id', 'platform_id', 'title', 'content_markdown', 'external_url', 'status', 'position', 'due_at', 'created_by', 'updated_by', 'generated_from_template_id'])]
class Todo extends Model implements Sortable
{
    use HasFactory, HasUuids, SetsUserStamps, SortableTrait;

    public array $sortable = [
        'order_column_name' => 'position',
        'sort_when_creating' => true,
    ];

    public function buildSortQuery(): Builder
    {
        return static::query()
            ->where('customer_id', $this->customer_id)
            ->where('status', $this->status);
    }

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }

    public function platform(): BelongsTo
    {
        return $this->belongsTo(Platform::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function generatedFromTemplate(): BelongsTo
    {
        return $this->belongsTo(CustomerTodoTemplate::class, 'generated_from_template_id');
    }
}
