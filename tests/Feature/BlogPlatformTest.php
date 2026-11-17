<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogPlatformTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_log_in_and_create_draft_post(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@blog.test',
            'password' => 'password123',
        ]);

        $this->post('/admin/login', [
            'email' => 'admin@blog.test',
            'password' => 'password123',
        ])->assertRedirect('/admin/posts');

        $this->assertAuthenticatedAs($user);

        $this->post('/admin/posts', [
            'title' => 'Hello World',
            'body' => 'This is the body of the blog post.',
            'is_published' => false,
        ])->assertRedirect('/admin/posts');

        $this->assertDatabaseHas('posts', [
            'title' => 'Hello World',
            'slug' => 'hello-world',
            'is_published' => 0,
        ]);
    }

    public function test_public_homepage_shows_only_published_posts_and_404s_unpublished(): void
    {
        Post::factory()->create([
            'title' => 'Published Post',
            'slug' => 'published-post',
            'body' => 'Visible to readers.',
            'is_published' => true,
        ]);

        Post::factory()->create([
            'title' => 'Draft Post',
            'slug' => 'draft-post',
            'body' => 'Hidden from readers.',
            'is_published' => false,
        ]);

        $this->get('/')->assertOk()->assertSee('Published Post')->assertDontSee('Draft Post');

        $this->get('/posts/published-post')->assertOk();
        $this->get('/posts/draft-post')->assertNotFound();
        $this->get('/posts/missing-post')->assertNotFound();
    }

    public function test_duplicate_titles_generate_unique_slugs(): void
    {
        Post::factory()->create([
            'title' => 'Same Title',
            'slug' => 'same-title',
            'body' => 'First version',
            'is_published' => true,
        ]);

        Post::factory()->create([
            'title' => 'Same Title',
            'slug' => 'same-title-2',
            'body' => 'Second version',
            'is_published' => true,
        ]);

        $this->assertDatabaseHas('posts', ['slug' => 'same-title']);
        $this->assertDatabaseHas('posts', ['slug' => 'same-title-2']);
    }

    public function test_admin_can_delete_post_regardless_of_visibility(): void
    {
        $user = User::factory()->create([
            'email' => 'admin2@blog.test',
            'password' => 'password123',
        ]);

        $this->actingAs($user)->delete('/admin/posts/' . Post::factory()->create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'body' => 'This will be removed.',
            'is_published' => true,
        ])->id)->assertRedirect('/admin/posts');

        $this->assertDatabaseMissing('posts', ['slug' => 'delete-me']);
    }
}
