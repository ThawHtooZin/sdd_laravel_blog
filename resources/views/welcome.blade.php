@php use Illuminate\Support\Str; @endphp

@extends('layouts.app')

@section('content')
    <div class="space-y-8">
        <header class="space-y-3">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-slate-500">Latest posts</p>
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Welcome to the blog</h1>
        </header>

        @if ($posts->isEmpty())
            <div class="rounded-xl border border-dashed border-slate-300 bg-white p-8 text-slate-600">
                No published posts yet. Check back soon.
            </div>
        @else
            <div class="grid gap-6">
                @foreach ($posts as $post)
                    <article class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
                        <div class="mb-3 text-sm font-medium text-slate-500">
                            {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Published' }}
                        </div>
                        <h2 class="text-2xl font-bold text-slate-900">
                            <a href="{{ route('posts.show', ['post' => $post]) }}" class="hover:text-slate-700">
                                {{ $post->title }}
                            </a>
                        </h2>
                        <p class="mt-4 line-clamp-3 text-slate-600">
                            {{ Str::limit(strip_tags($post->body), 180) }}
                        </p>
                    </article>
                @endforeach
            </div>
        @endif
    </div>
@endsection
