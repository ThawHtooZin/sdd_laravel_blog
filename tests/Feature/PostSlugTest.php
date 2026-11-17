<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostSlugTest extends TestCase
{
    use RefreshDatabase;

    public function test_duplicate_titles_receive_unique_generated_slugs(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Weekly Update',
                'body' => 'First article.',
            ])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHas('success', 'Post saved as a draft. URL slug: weekly-update.');

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Weekly Update',
                'body' => 'Second article.',
            ])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHas('success', 'Post saved as a draft. URL slug: weekly-update-2.');

        $this->assertDatabaseHas('posts', [
            'title' => 'Weekly Update',
            'slug' => 'weekly-update',
        ]);
        $this->assertDatabaseHas('posts', [
            'title' => 'Weekly Update',
            'slug' => 'weekly-update-2',
        ]);
    }

    public function test_changing_a_title_generates_a_unique_slug(): void
    {
        $admin = User::factory()->create();
        $existingPost = Post::factory()->for($admin)->create([
            'title' => 'Weekly Update',
            'slug' => 'weekly-update',
        ]);
        $postToUpdate = Post::factory()->for($admin)->create([
            'title' => 'Old Title',
            'slug' => 'old-title',
        ]);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $postToUpdate), [
                'title' => 'Weekly Update',
                'body' => 'Updated article.',
            ])
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('posts', [
            'id' => $existingPost->id,
            'slug' => 'weekly-update',
        ]);
        $this->assertDatabaseHas('posts', [
            'id' => $postToUpdate->id,
            'slug' => 'weekly-update-2',
        ]);
    }
}
