<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence(4),
            'slug' => fn (array $attributes) => Post::generateUniqueSlug($attributes['title']),
            'body' => $this->faker->paragraphs(3, true),
            'is_published' => true,
            'published_at' => now(),
        ];
    }
}
