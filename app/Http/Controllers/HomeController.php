<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        if (! Schema::hasTable('posts')) {
            return view('welcome', ['posts' => collect()]);
        }

        $posts = Post::query()
            ->where('is_published', true)
            ->latest('published_at')
            ->latest('id')
            ->get();

        return view('welcome', compact('posts'));
    }

    public function show(Post $post)
    {
        if (! Schema::hasTable('posts')) {
            abort(404);
        }

        abort_unless($post->is_published, 404);

        return view('posts.show', compact('post'));
    }
}
