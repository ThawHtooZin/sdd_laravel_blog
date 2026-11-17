@extends('layouts.app')

@section('content')
    <div class="flex items-center justify-between gap-4 pb-6">
        <div>
            <h1 class="text-3xl font-bold text-slate-900">Blog posts</h1>
            <p class="mt-1 text-slate-600">Manage the blog content and publication state.</p>
        </div>

        <a href="{{ route('admin.posts.create') }}" class="rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
            New post
        </a>
    </div>

    <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @if ($posts->isEmpty())
            <p class="text-slate-600">No posts yet. Create your first article.</p>
        @else
            <div class="space-y-4">
                @foreach ($posts as $post)
                    <div class="flex flex-col gap-4 rounded-lg border border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-slate-900">{{ $post->title }}</h2>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ $post->is_published ? 'Published' : 'Draft' }} · {{ $post->updated_at->format('M d, Y') }}
                            </p>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <a href="{{ route('posts.show', $post) }}" class="rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                View
                            </a>
                            <a href="{{ route('admin.posts.edit', $post) }}" class="rounded border border-slate-300 px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.posts.destroy', $post) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded bg-red-600 px-3 py-2 text-sm text-white hover:bg-red-500">
                                    Delete
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
