@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl rounded-xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-slate-900">Create post</h1>
            <p class="mt-2 text-slate-600">Write a new article and decide whether it should be published immediately.</p>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.posts.store') }}" class="space-y-6">
            @csrf

            <div>
                <label for="title" class="mb-2 block text-sm font-medium text-slate-700">Title</label>
                <input id="title" name="title" type="text" value="{{ old('title') }}" required
                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label for="body" class="mb-2 block text-sm font-medium text-slate-700">Body</label>
                <textarea id="body" name="body" rows="12" required
                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 focus:border-slate-500 focus:outline-none">{{ old('body') }}</textarea>
            </div>

            <div class="flex items-center gap-3">
                <input id="is_published" name="is_published" type="checkbox" value="1" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-500">
                <label for="is_published" class="text-sm font-medium text-slate-700">Publish immediately</label>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.posts.index') }}" class="rounded border border-slate-300 px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">Cancel</a>
                <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-sm font-medium text-white hover:bg-slate-700">
                    Save post
                </button>
            </div>
        </form>
    </div>
@endsection
