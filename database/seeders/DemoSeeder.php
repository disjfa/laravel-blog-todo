<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $companies = [
            ['name' => 'Acme Corp', 'slug' => 'acme-corp'],
            ['name' => 'Globex Media', 'slug' => 'globex-media'],
            ['name' => 'Initech Publishing', 'slug' => 'initech-publishing'],
        ];

        foreach ($companies as $data) {
            $customer = Customer::firstOrCreate(
                ['slug' => $data['slug']],
                ['name' => $data['name'], 'automation_enabled' => true],
            );

            // Create a dedicated user for this customer if one isn't linked yet.
            if ($customer->users()->doesntExist()) {
                $user = User::factory()->create([
                    'name' => $data['name'] . ' Admin',
                    'email' => $data['slug'] . '@example.com',
                ]);
                $user->assignRole('customer');
                $customer->users()->attach($user);
            } else {
                $user = $customer->users()->first();
            }

            // Add 5 published + 2 draft blogs per customer.
            Blog::factory()
                ->count(5)
                ->published()
                ->create(['customer_id' => $customer->id, 'created_by' => $user->id, 'updated_by' => $user->id]);

            Blog::factory()
                ->count(2)
                ->draft()
                ->create(['customer_id' => $customer->id, 'created_by' => $user->id, 'updated_by' => $user->id]);
        }
    }
}
