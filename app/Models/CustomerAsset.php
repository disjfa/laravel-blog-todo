<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['customer_id', 'uploaded_by', 'connection_id', 'disk_driver', 'path', 'public_url', 'provider_asset_id', 'filename', 'mime_type', 'size_bytes', 'meta'])]
class CustomerAsset extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'size_bytes' => 'integer',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(CustomerAssetConnection::class, 'connection_id');
    }

    public function blogs(): BelongsToMany
    {
        return $this->belongsToMany(Blog::class, 'blog_assets')
            ->withPivot('sort_order')
            ->withTimestamps();
    }
}
