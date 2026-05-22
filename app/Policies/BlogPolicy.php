<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\User;

class BlogPolicy
{
    public function before(User $user): ?bool
    {
        return $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user, ?Customer $customer = null): bool
    {
        if ($customer === null) {
            return true;
        }

        return $user->customers->contains($customer);
    }

    public function view(User $user, Blog $blog): bool
    {
        return $user->customers->contains($blog->customer);
    }

    public function create(User $user, ?Customer $customer = null): bool
    {
        if ($customer === null) {
            return true;
        }

        return $user->customers->contains($customer);
    }

    public function update(User $user, Blog $blog): bool
    {
        return $user->customers->contains($blog->customer);
    }

    public function delete(User $user, Blog $blog): bool
    {
        return $user->customers->contains($blog->customer);
    }
}
