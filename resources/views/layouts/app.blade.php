<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Laravel Blog') }}</title>

        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="bg-slate-100 text-slate-900 antialiased">
        <header class="border-b border-slate-200 bg-white/80 backdrop-blur">
            <div class="mx-auto flex max-w-6xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <a href="{{ route('home') }}" class="text-xl font-bold text-slate-900">Laravel Blog</a>

                <nav class="flex items-center gap-4 text-sm font-medium text-slate-600">
                    <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                    @auth
                        <a href="{{ route('admin.posts.index') }}" class="hover:text-slate-900">Admin</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="rounded bg-slate-900 px-3 py-2 text-white hover:bg-slate-700">
                                Log out
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="rounded border border-slate-300 px-3 py-2 hover:bg-slate-50">
                            Admin login
                        </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 rounded border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @yield('content')
        </main>
    </body>
</html>
