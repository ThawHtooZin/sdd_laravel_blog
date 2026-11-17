<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicPostAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_lists_published_posts_but_hides_drafts(): void
    {
        $publishedPost = Post::factory()->create([
            'title' => 'Public Article',
            'slug' => 'public-article',
            'body' => 'Available to readers.',
            'is_published' => true,
        ]);
        Post::factory()->create([
            'title' => 'Private Draft',
            'slug' => 'private-draft',
            'body' => 'Not available to readers.',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee($publishedPost->title)
            ->assertDontSee('Private Draft');
    }

    public function test_published_post_is_readable_without_authentication(): void
    {
        $post = Post::factory()->create([
            'title' => 'Public Article',
            'slug' => 'public-article',
            'body' => 'The article text.',
            'is_published' => true,
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertOk()
            ->assertSee('The article text.');

        $this->assertGuest();
    }

    public function test_unpublished_post_returns_404_to_public_visitors(): void
    {
        $post = Post::factory()->create([
            'slug' => 'private-draft',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertNotFound()
            ->assertDontSee($post->body);
    }

    public function test_nonexistent_post_returns_404(): void
    {
        $this->get(route('posts.show', ['post' => 'missing-post']))
            ->assertNotFound();
    }

    public function test_published_post_body_is_html_escaped(): void
    {
        $post = Post::factory()->create([
            'slug' => 'escaped-body',
            'body' => '<script>alert(1)</script>',
            'is_published' => true,
        ]);

        $this->get(route('posts.show', ['post' => $post->slug]))
            ->assertOk()
            ->assertSee('&lt;script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }
}
