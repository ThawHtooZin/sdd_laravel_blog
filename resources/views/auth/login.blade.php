@extends('layouts.app')

@section('content')
    <div class="mx-auto max-w-md rounded-xl bg-white p-8 shadow-sm ring-1 ring-slate-200">
        <h1 class="mb-6 text-2xl font-bold text-slate-900">Admin login</h1>

        @if ($errors->any())
            <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <label for="email" class="mb-1 block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 focus:border-slate-500 focus:outline-none">
            </div>

            <div>
                <label for="password" class="mb-1 block text-sm font-medium text-slate-700">Password</label>
                <input id="password" name="password" type="password" required
                    class="w-full rounded-lg border border-slate-300 bg-slate-50 px-3 py-2 focus:border-slate-500 focus:outline-none">
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center gap-2 text-sm text-slate-700">
                    <input type="checkbox" name="remember" value="1">
                    Remember me
                </label>
                <button type="submit" class="rounded bg-slate-900 px-4 py-2 text-white hover:bg-slate-700">
                    Log in
                </button>
            </div>
        </form>
    </div>
@endsection
