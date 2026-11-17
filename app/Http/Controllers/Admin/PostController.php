<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::query()->latest()->get();

        return view('posts.index', compact('posts'));
    }

    public function create()
    {
        return view('posts.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Post::generateUniqueSlug($validated['title']);
        $validated['is_published'] = $request->boolean('is_published');
        $validated['published_at'] = $validated['is_published'] ? now() : null;

        $post = Post::create($validated);

        $status = $post->is_published ? 'published' : 'saved as a draft';

        return redirect()->route('admin.posts.index')
            ->with('success', "Post {$status}. URL slug: {$post->slug}.");
    }

    public function edit(Post $post)
    {
        return view('posts.edit', compact('post'));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $validated['is_published'] = $request->boolean('is_published');

        if ($validated['title'] !== $post->title) {
            $validated['slug'] = Post::generateUniqueSlug($validated['title'], $post->id);
        }

        $validated['published_at'] = $validated['is_published'] ? ($post->published_at ?? now()) : null;

        $post->update($validated);

        $status = $post->is_published ? 'published' : 'saved as a draft';

        return redirect()->route('admin.posts.index')
            ->with('success', "Post {$status}. URL slug: {$post->slug}.");
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully.');
    }
}
