@extends('layouts.app')

@section('content')
    <article class="mx-auto max-w-3xl rounded-xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <div class="mb-6 text-sm font-medium uppercase tracking-[0.12em] text-slate-500">
            {{ $post->published_at ? $post->published_at->format('M d, Y') : 'Published' }}
        </div>

        <h1 class="text-4xl font-bold tracking-tight text-slate-900">{{ $post->title }}</h1>

        <div class="prose prose-slate mt-8 max-w-none">
            {!! nl2br(e($post->body)) !!}
        </div>
    </article>
@endsection
