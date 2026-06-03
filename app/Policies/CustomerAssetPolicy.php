<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\CustomerAsset;
use App\Models\User;

class CustomerAssetPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return (bool) $user->hasRole(['admin', 'customer']);
    }

    public function view(User $user, CustomerAsset $asset): bool
    {
        return $user->customers->contains($asset->customer);
    }

    public function create(User $user, Customer $customer): bool
    {
        return $user->customers->contains($customer);
    }

    public function delete(User $user, CustomerAsset $asset): bool
    {
        return $user->customers->contains($asset->customer);
    }
}
