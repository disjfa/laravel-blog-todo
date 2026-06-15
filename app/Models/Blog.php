<?php

namespace App\Models;

use App\Models\Concerns\SetsUserStamps;
use App\Observers\BlogObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'title', 'slug', 'excerpt', 'content_markdown', 'status', 'publish_at', 'external_url', 'created_by', 'updated_by'])]
#[ObservedBy(BlogObserver::class)]
class Blog extends Model
{
    use HasFactory, HasUuids, SetsUserStamps;

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(CustomerAsset::class, 'blog_assets')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
