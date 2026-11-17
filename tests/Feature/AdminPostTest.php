<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login_from_post_management(): void
    {
        $this->get(route('admin.posts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_admin_post_list_includes_published_posts_and_drafts(): void
    {
        $admin = User::factory()->create();
        Post::factory()->for($admin)->create([
            'title' => 'Published Article',
            'is_published' => true,
        ]);
        Post::factory()->for($admin)->create([
            'title' => 'Draft Article',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->actingAs($admin)
            ->get(route('admin.posts.index'))
            ->assertOk()
            ->assertSee('Published Article')
            ->assertSee('Draft Article');
    }

    public function test_admin_can_log_in_with_valid_credentials(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.test',
            'password' => 'test-password',
        ]);

        $this->post(route('login'), [
            'email' => 'admin@example.test',
            'password' => 'test-password',
        ])->assertRedirect(route('admin.posts.index'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_can_create_a_post_as_published(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [
                'title' => 'Immediate Publication',
                'body' => 'Ready for public readers.',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHas('success', 'Post published. URL slug: immediate-publication.');

        $this->assertDatabaseHas('posts', [
            'title' => 'Immediate Publication',
            'slug' => 'immediate-publication',
            'is_published' => 1,
        ]);
        $this->assertNotNull(Post::query()->where('slug', 'immediate-publication')->value('published_at'));
    }

    public function test_admin_can_edit_and_publish_then_unpublish_a_post(): void
    {
        $admin = User::factory()->create();
        $post = Post::factory()->for($admin)->create([
            'title' => 'Draft Title',
            'slug' => 'draft-title',
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $post), [
                'title' => 'Updated Title',
                'body' => 'Updated article content.',
                'is_published' => '1',
            ])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHas('success', 'Post published. URL slug: updated-title.');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'body' => 'Updated article content.',
            'is_published' => 1,
        ]);
        $this->assertNotNull($post->fresh()->published_at);

        $this->actingAs($admin)
            ->put(route('admin.posts.update', $post), [
                'title' => 'Updated Title',
                'body' => 'Updated article content.',
            ])
            ->assertRedirect(route('admin.posts.index'))
            ->assertSessionHas('success', 'Post saved as a draft. URL slug: updated-title.');

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'is_published' => 0,
            'published_at' => null,
        ]);
    }

    public function test_admin_can_permanently_delete_a_draft_post(): void
    {
        $admin = User::factory()->create();
        $post = Post::factory()->for($admin)->create([
            'is_published' => false,
            'published_at' => null,
        ]);

        $this->actingAs($admin)
            ->delete(route('admin.posts.destroy', $post))
            ->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_admin_can_log_out_and_loses_access_to_post_management(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->get(route('admin.posts.index'))
            ->assertRedirect(route('login'));
    }

    public function test_login_attempts_are_rate_limited(): void
    {
        foreach (range(1, 5) as $attempt) {
            $this->post(route('login'), [
                'email' => 'admin@example.test',
                'password' => 'invalid-password',
            ])->assertSessionHasErrors('email');
        }

        $this->post(route('login'), [
            'email' => 'admin@example.test',
            'password' => 'invalid-password',
        ])->assertStatus(429);
    }

    public function test_public_registration_route_does_not_exist(): void
    {
        $this->get('/register')
            ->assertNotFound();
    }

    public function test_post_creation_requires_title_and_body(): void
    {
        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->post(route('admin.posts.store'), [])
            ->assertSessionHasErrors(['title', 'body']);

        $this->assertDatabaseCount('posts', 0);
    }
}
