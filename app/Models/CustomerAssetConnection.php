<?php

namespace App\Models;

use App\Enums\AssetDriver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['customer_id', 'name', 'driver', 'config_encrypted', 'is_active', 'last_validated_at'])]
#[Hidden(['config_encrypted'])]
class CustomerAssetConnection extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_validated_at' => 'datetime',
            'config_encrypted' => 'encrypted:array',
            'driver' => AssetDriver::class,
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CustomerAsset::class, 'connection_id');
    }

    public function getDecryptedConfig(): array
    {
        return $this->config_encrypted ?? [];
    }
}
