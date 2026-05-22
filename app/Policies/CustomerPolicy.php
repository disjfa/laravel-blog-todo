<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\User;

class CustomerPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool
    {
        return false;
    }

    public function view(User $user, Customer $customer): bool
    {
        return $user->customers->contains($customer);
    }

    public function create(User $user): bool
    {
        return $user->hasRole('customer');
    }

    public function update(User $user, Customer $customer): bool
    {
        return $user->customers->contains($customer);
    }

    public function delete(User $user, Customer $customer): bool
    {
        return false; // only admins (handled by before()) may delete
    }
}
