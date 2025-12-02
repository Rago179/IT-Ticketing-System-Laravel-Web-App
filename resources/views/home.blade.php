<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - {{ config('app.name', 'My App') }}</title>
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    <div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-sm">
        
        {{-- Header --}}
        <div class="flex justify-between items-center border-b border-gray-100 pb-4 mb-6">
            <h1 class="text-2xl font-bold m-0 text-slate-800">{{ config('app.name', 'My App') }}</h1>
            <div class="flex items-center">
                <a href="{{ route('posts.create') }}" class="bg-sky-600 text-white px-3 py-2 rounded-md text-sm font-bold no-underline hover:bg-sky-700 transition-colors mr-2 inline-block">
                    Create New Post
                </a>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-600 text-white px-3 py-2 rounded-md text-sm font-bold border-none cursor-pointer hover:bg-red-700 transition-colors">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        {{-- Post List --}}
        <div class="mt-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4">All Posts</h2>

            @forelse ($posts as $post)
                <div class="border border-gray-200 p-4 rounded-lg mb-4 hover:bg-slate-50 transition-colors">
                    <h2 class="mt-0 mb-2 text-xl font-bold">
                        <a href="{{ route('posts.show', $post) }}" class="text-sky-600 no-underline hover:underline">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="text-sm text-slate-500 mb-2">
                        By <span class="font-semibold">{{ $post->user->name }}</span> on {{ $post->created_at->format('M d, Y') }} | Comments: {{ $post->comments->count() }}
                    </p>
                    <p class="text-slate-700 leading-relaxed">
                        {{ Str::limit($post->description, 150) }}
                    </p>
                </div>
            @empty
                <p class="text-slate-500 italic">No posts found.</p>
            @endforelse
        </div>
    </div>
</body>
</html>