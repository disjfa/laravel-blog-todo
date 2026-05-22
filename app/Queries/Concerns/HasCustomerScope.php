<?php

namespace App\Queries\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

trait HasCustomerScope
{
    public function forCustomer(string|int $customerId): static
    {
        return $this->where('customer_id', $customerId);
    }

    public function forUser(User $user): static
    {
        if ($user->hasRole('admin')) {
            return $this;
        }

        return $this->whereIn('customer_id', $user->customers()->pluck('customers.id'));
    }

    public static function scopeForUser(Builder $query, User $user): Builder
    {
        if ($user->hasRole('admin')) {
            return $query;
        }

        return $query->whereIn('customer_id', $user->customers()->pluck('customers.id'));
    }
}
