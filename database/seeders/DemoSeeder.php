<?php

namespace Database\Seeders;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\CustomerTodoTemplate;
use App\Models\Platform;
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
                    'name' => $data['name'].' Admin',
                    'email' => $data['slug'].'@example.com',
                ]);
                $user->assignRole('customer');
                $customer->users()->attach($user);
            } else {
                $user = $customer->users()->first();
            }

            // Seed automation templates before blogs so blog creation generates todos.
            $platforms = Platform::query()
                ->where('is_active', true)
                ->limit(2)
                ->get();

            if ($platforms->isNotEmpty()) {
                foreach ($platforms as $platform) {
                    CustomerTodoTemplate::firstOrCreate(
                        [
                            'customer_id' => $customer->id,
                            'platform_id' => $platform->id,
                            'title_template' => "Publish on {$platform->name}",
                        ],
                        [
                            'body_template' => 'Promote the latest blog post with a short teaser and link.',
                            'default_status' => 'todo',
                            'due_offset_iso8601' => 'P1D',
                            'is_active' => true,
                        ]
                    );
                }
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
