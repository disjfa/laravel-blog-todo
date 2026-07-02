<?php

namespace App\Models;

use App\Services\AssetDrivers\AssetDriverFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Throwable;

/**
 * @property-read ?string $public_url
 */
#[Fillable(['customer_id', 'uploaded_by', 'connection_id', 'disk_driver', 'path', 'provider_asset_id', 'filename', 'mime_type', 'size_bytes', 'meta'])]
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

    protected function publicUrl(): Attribute
    {
        return Attribute::make(
            get: function (): ?string {
                $identifier = $this->provider_asset_id ?: $this->path;
                if (blank($identifier)) {
                    return null;
                }
                $connection = $this->connection;
                if (! $connection instanceof CustomerAssetConnection && filled($this->connection_id)) {
                    $connection = CustomerAssetConnection::query()->find($this->connection_id);
                }
                if (! $connection instanceof CustomerAssetConnection) {
                    return null;
                }
                try {
                    return AssetDriverFactory::buildUrl($connection, $identifier);
                } catch (Throwable) {
                    return null;
                }
            },
        );
    }
}
