<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RagDemoPostsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

class RagDemoPostsSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_seeds_published_demo_posts_and_can_be_run_again(): void
    {
        $admin = User::factory()->create();

        $this->seed(RagDemoPostsSeeder::class);
        $this->seed(RagDemoPostsSeeder::class);

        $this->assertDatabaseCount('posts', 3);
        $this->assertDatabaseHas('posts', [
            'user_id' => $admin->id,
            'slug' => 'rag-demo-laravel-authentication',
            'is_published' => 1,
        ]);
        $this->assertDatabaseMissing('posts', [
            'is_published' => 0,
        ]);
    }

    public function test_it_requires_an_existing_user_before_seeding_posts(): void
    {
        $this->expectException(RuntimeException::class);

        $this->seed(RagDemoPostsSeeder::class);
    }
}
