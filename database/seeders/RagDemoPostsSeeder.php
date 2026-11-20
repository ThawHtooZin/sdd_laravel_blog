<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class RagDemoPostsSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::query()->oldest('id')->first();

        if ($admin === null) {
            throw new RuntimeException('Seed an admin user before running the RAG demo posts seeder.');
        }

        $posts = [
            [
                'slug' => 'rag-demo-laravel-authentication',
                'title' => 'Laravel Authentication Explained',
                'body' => implode("\n\n", [
                    'Authentication checks who a visitor is. In this blog, the login form sends an email address and password to the LoginController. The controller validates both values before trying to sign the visitor in.',
                    'Laravel Auth::attempt() checks the submitted credentials against the user record. If they match, Laravel starts an authenticated session. The controller regenerates the session ID after login so the old session cannot be reused.',
                    'Routes inside the auth middleware group require a signed-in user. The admin post-management pages use that group, while the public homepage and published article pages remain available without signing in.',
                ]),
            ],
            [
                'slug' => 'rag-demo-laravel-routing',
                'title' => 'How Laravel Routes Work',
                'body' => implode("\n\n", [
                    'A route connects an incoming URL and HTTP method to the code that handles the request. This blog defines its browser routes in routes/web.php and sends requests to controller methods.',
                    'Named routes give URLs a stable name, such as home or posts.show. Blade templates can generate links with route() instead of hard-coding paths, so links keep working when a URL changes.',
                    'The individual post route uses route model binding with the post slug. Laravel finds the matching Post record and provides it to the controller. Unpublished posts are rejected by the public controller.',
                ]),
            ],
            [
                'slug' => 'rag-demo-eloquent-and-migrations',
                'title' => 'Eloquent Models and Migrations',
                'body' => implode("\n\n", [
                    'An Eloquent model represents a database record in application code. The Post model represents rows in the posts table and defines how the application works with blog posts.',
                    'A migration describes a database structure change in code. The posts migration creates columns for the post owner, title, slug, body, publication state, and timestamps.',
                    'Factories create realistic model records for automated tests. For example, PostFactory can make a published post with a title, body, and owning user without manually inserting SQL.',
                ]),
            ],
        ];

        foreach ($posts as $post) {
            $admin->posts()->updateOrCreate(
                ['slug' => $post['slug']],
                [
                    'title' => $post['title'],
                    'body' => $post['body'],
                    'is_published' => true,
                    'published_at' => now(),
                ],
            );
        }
    }
}
