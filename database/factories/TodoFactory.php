<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Todo;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Todo>
 */
class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'title' => fake()->sentence(5, true),
            'content_markdown' => fake()->paragraph(),
            'status' => fake()->randomElement(['todo', 'planned', 'in_progress', 'blocked', 'done']),
            'position' => '0',
            'due_at' => now()->addDays(fake()->numberBetween(1, 30)),
            'created_by' => User::factory(),
        ];
    }
}
