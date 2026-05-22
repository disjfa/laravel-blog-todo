<?php

namespace App\Models;

use App\Observers\PlatformObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'slug', 'is_active'])]
#[ObservedBy(PlatformObserver::class)]
class Platform extends Model
{
    use HasUuids;

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_platforms')
            ->withPivot('is_enabled')
            ->withTimestamps();
    }

    public function todos(): HasMany
    {
        return $this->hasMany(Todo::class);
    }

    public function todoTemplates(): HasMany
    {
        return $this->hasMany(CustomerTodoTemplate::class);
    }
}
