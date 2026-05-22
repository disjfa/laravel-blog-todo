<?php

namespace App\Models;

use App\Observers\CustomerObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable(['name', 'slug', 'automation_enabled'])]
#[ObservedBy(CustomerObserver::class)]
class Customer extends Model
{
    use HasFactory, HasUuids;

    protected function casts(): array
    {
        return [
            'automation_enabled' => 'boolean',
        ];
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    public function platforms(): BelongsToMany
    {
        return $this->belongsToMany(Platform::class, 'customer_platforms')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function blogs(): HasMany
    {
        return $this->hasMany(Blog::class);
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function todoTemplates(): HasMany
    {
        return $this->hasMany(CustomerTodoTemplate::class);
    }

    public function assetConnection(): HasOne
    {
        return $this->hasOne(CustomerAssetConnection::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(CustomerAsset::class);
    }
}
