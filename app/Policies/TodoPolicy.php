<?php

namespace App\Policies;

use App\Models\Customer;
use App\Models\Todo;
use App\Models\User;
use Filament\Facades\Filament;

class TodoPolicy
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

    public function view(User $user, Todo $todo): bool
    {
        return $user->customers->contains($todo->customer);
    }

    public function create(User $user, ?Customer $customer = null): bool
    {
        $customer ??= Filament::getTenant();

        return $user->customers->contains($customer);
    }

    public function update(User $user, Todo $todo): bool
    {
        return $user->customers->contains($todo->customer);
    }

    public function delete(User $user, Todo $todo): bool
    {
        return $user->customers->contains($todo->customer);
    }
}
