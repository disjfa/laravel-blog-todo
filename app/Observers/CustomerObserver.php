<?php

namespace App\Observers;

use App\Models\Customer;
use Illuminate\Support\Str;

class CustomerObserver
{
    public function creating(Customer $customer): void
    {
        $customer->slug = $this->uniqueSlug($customer, $customer->name);
    }

    public function saving(Customer $customer): void
    {
        if (empty($customer->slug)) {
            $customer->slug = $this->uniqueSlug($customer, $customer->name);
        }
    }

    private function uniqueSlug(Customer $customer, string $name): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 1;

        while (Customer::where('slug', $slug)->where('id', '!=', $customer->id ?? '')->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
    }
}
