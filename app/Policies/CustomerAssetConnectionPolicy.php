<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\CustomerAssetConnection;
use App\Models\User;
use Filament\Facades\Filament;

class CustomerAssetConnectionPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user, ?Customer $customer = null): bool
    {
        $customer ??= Filament::getTenant();

        return $user->customers->contains($customer);
    }

    public function view(User $user, CustomerAssetConnection $connection): bool
    {
        return $user->customers->contains($connection->customer);
    }

    public function create(User $user, ?Customer $customer = null): bool
    {
        $customer ??= Filament::getTenant();

        return $user->customers->contains($customer);
    }

    public function update(User $user, CustomerAssetConnection $connection): bool
    {
        return $user->customers->contains($connection->customer);
    }

    public function delete(User $user, CustomerAssetConnection $connection): bool
    {
        return $user->customers->contains($connection->customer);
    }
}
