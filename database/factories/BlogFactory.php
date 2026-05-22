<?php

namespace Database\Factories;

use App\Models\Blog;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Blog>
 */
class BlogFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->sentence(6, true);
        $title = rtrim($title, '.');

        return [
            'customer_id' => Customer::factory(),
            'created_by' => User::factory(),
            'updated_by' => fn (array $attrs) => $attrs['created_by'],
            'title' => $title,
            'slug' => Str::slug($title) . '-' . fake()->unique()->numerify('####'),
            'excerpt' => fake()->paragraph(2),
            'content_markdown' => implode("\n\n", [
                '## ' . fake()->sentence(4, true),
                fake()->paragraph(4),
                '## ' . fake()->sentence(4, true),
                fake()->paragraph(4),
            ]),
            'status' => fake()->randomElement(['draft', 'draft', 'published', 'published', 'published']),
            'publish_at' => fn (array $attrs) => $attrs['status'] === 'published'
                ? fake()->dateTimeBetween('-30 days', 'now')
                : null,
        ];
    }

    public function published(): static
    {
        return $this->state([
            'status' => 'published',
            'publish_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ]);
    }

    public function draft(): static
    {
        return $this->state([
            'status' => 'draft',
            'publish_at' => null,
        ]);
    }
}
