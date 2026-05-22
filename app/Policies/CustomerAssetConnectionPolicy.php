<?php

namespace App\Policies;

use App\Models\CustomerAssetConnection;
use App\Models\User;

class CustomerAssetConnectionPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, CustomerAssetConnection $connection): bool
    {
        return false;
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, CustomerAssetConnection $connection): bool
    {
        return false;
    }

    public function delete(User $user, CustomerAssetConnection $connection): bool
    {
        return false;
    }
}
