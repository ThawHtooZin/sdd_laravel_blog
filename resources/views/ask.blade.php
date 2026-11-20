@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-3xl space-y-8">
        <header class="space-y-3">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-indigo-600">Retrieval-augmented generation</p>
            <h1 class="text-4xl font-bold tracking-tight text-slate-900">Ask the blog</h1>
            <p class="max-w-2xl text-slate-600">
                Ask a question about published posts. The app searches relevant passages, then uses them to prepare an answer with sources.
            </p>
        </header>

        <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <form method="POST" action="{{ route('ask.store') }}" class="space-y-4">
                @csrf
                <div class="space-y-2">
                    <label for="question" class="block text-sm font-semibold text-slate-800">Your question</label>
                    <textarea
                        id="question"
                        name="question"
                        rows="3"
                        maxlength="1000"
                        required
                        placeholder="For example: Which method checks submitted login credentials?"
                        class="block w-full resize-y rounded-xl border border-slate-300 px-4 py-3 text-slate-900 shadow-sm outline-none placeholder:text-slate-400 focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >{{ $question }}</textarea>
                    @error('question')
                        <p class="text-sm text-red-700">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-500">Answers are based on published blog posts.</p>
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200"
                    >
                        Search and answer
                    </button>
                </div>
            </form>
        </section>

        @if ($error)
            <div role="alert" class="rounded-xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-900">
                {{ $error }}
            </div>
        @endif

        @if ($answer)
            <section aria-live="polite" class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <div class="space-y-2">
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-slate-500">Answer</p>
                    <div class="whitespace-pre-line leading-7 text-slate-800">{{ $answer }}</div>
                </div>
            </section>
        @endif

        @if ($sources !== [])
            <section class="space-y-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                <h2 class="text-sm font-semibold text-slate-800">Sources from the blog</h2>
                <ol class="space-y-2">
                    @foreach ($sources as $source)
                        <li class="flex flex-wrap items-baseline justify-between gap-x-4 gap-y-1 text-sm">
                            <a
                                href="{{ route('posts.show', ['post' => $source['slug']]) }}"
                                class="font-medium text-indigo-700 underline decoration-indigo-200 underline-offset-4 hover:text-indigo-900"
                            >
                                [{{ $source['number'] }}] {{ $source['title'] }}
                            </a>
                            <span class="text-xs text-slate-500">
                                distance {{ number_format($source['distance'], 4) }}
                            </span>
                        </li>
                    @endforeach
                </ol>
            </section>
        @endif
    </div>
@endsection
