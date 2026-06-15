<?php

namespace App\Filament\Concerns;

use App\Models\Customer;
use Filament\Facades\Filament;
use RuntimeException;

trait ResolvesCustomerTenant
{
    protected function getCustomerTenant(): Customer
    {
        $tenant = Filament::getTenant();

        if (! $tenant instanceof Customer) {
            throw new RuntimeException('No customer tenant is selected.');
        }

        return $tenant;
    }
}
