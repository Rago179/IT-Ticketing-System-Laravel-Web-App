<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'IT-Ticket-System') }}</title>
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Alerts --}}
    @if (session('success'))
        <div id="success-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow-md font-bold flex items-center gap-3">
            <span>{{ session('success') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('success-alert')?.remove(), 3000);</script>
    @endif
    
    @if (session('error'))
        <div id="error-alert" class="fixed top-20 left-1/2 -translate-x-1/2 z-50 bg-red-100 text-red-800 px-6 py-3 rounded-lg border border-red-500 font-bold flex items-center gap-3">
            <span>🚫 {{ session('error') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('error-alert')?.remove(), 4000);</script>
    @endif


    <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-sm">
        
        {{-- HEADER LAYOUT --}}
        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
            <div>
                <h1 class="text-3xl font-bold text-slate-800 m-0">Dashboard</h1>
            </div>
            <div class="flex items-center gap-5 text-sm text-slate-600">
                <span>
                    Hi, <a href="{{ route('users.show', Auth::user()) }}" class="font-bold text-slate-800 no-underline hover:text-sky-600 hover:underline">{{ Auth::user()->name }}</a>
                </span>
                <span class="text-slate-300">|</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-500 hover:text-red-500 underline bg-transparent border-none cursor-pointer p-0">Log Out</button>
                </form>
            </div>
        </div>

        {{-- ACTION BUTTONS ROW --}}
        <div class="flex gap-3 mb-10">
            <a href="{{ route('posts.create') }}" class="inline-block px-5 py-2.5 bg-sky-600 text-white rounded-md font-bold text-sm hover:bg-sky-700 transition-colors no-underline">
                + Create New Post
            </a>

            @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}" class="inline-block px-5 py-2.5 bg-indigo-600 text-white rounded-md font-bold text-sm hover:bg-indigo-700 transition-colors no-underline">
                    Manage Users
                </a>
            @endif

            @if(in_array(Auth::user()->role, ['it', 'admin']))
                <a href="{{ route('it.dashboard') }}" class="inline-block px-5 py-2.5 bg-orange-600 text-white rounded-md font-bold text-sm hover:bg-orange-700 transition-colors no-underline">
                    IT Dashboard
                </a>
            @endif
        </div>


        {{-- PINNED POSTS SECTION --}}
        <div>
            <h2 class="mt-10 mb-5 text-xl text-slate-800 border-b-2 border-slate-100 pb-2 flex items-center gap-2">
                <span>📌</span> Pinned Posts
            </h2>
            @forelse ($pinnedPosts as $post)
                <div class="flex justify-between items-center p-4 bg-slate-50 rounded-lg mb-3 border-l-4 border-sky-600">
                    <div>
                        <div class="mb-1">
                            <a href="{{ route('posts.show', $post) }}" class="text-sky-600 font-bold text-lg no-underline hover:underline">{{ $post->title }}</a>
                        </div>
                        <div class="text-xs text-slate-500">
                            By {{ $post->user->name }} | {{ $post->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 italic">No pinned posts.</p>
            @endforelse
        </div>


        {{-- CATEGORIES SECTION --}}
        <div>
            <h2 class="mt-10 mb-5 text-xl text-slate-800 border-b-2 border-slate-100 pb-2 flex items-center gap-2">
                <span>📂</span> Browse by Category
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse ($categories as $category)
                    <div class="relative group h-full">
                        {{-- The Main Category Link --}}
                        <a href="{{ route('categories.show', $category) }}" class="block p-6 bg-slate-50 rounded-lg border border-gray-200 text-slate-800 no-underline h-full box-border transition transform hover:-translate-y-1 hover:shadow-md hover:border-sky-500">
                            <h3 class="text-xl font-bold text-sky-600 m-0">{{ $category->name }}</h3>
                            <div class="text-sm text-slate-500 mt-1">{{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}</div>
                        </a>

                        {{-- Admin Delete Button --}}
                        @if(Auth::user()->role === 'admin' && $category->name !== 'Other')
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure? All posts in this category will be moved to \'Other\'.');"
                                  class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-6 h-6 rounded-full bg-red-100 text-red-600 border border-red-500 flex items-center justify-center cursor-pointer font-bold leading-none hover:bg-red-200" title="Delete Category">
                                    &times;
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p class="col-span-full text-slate-500">No categories have been created yet.</p>
                @endforelse
            </div>
        </div>


        {{-- ADMIN: CREATE CATEGORY (Bottom of page) --}}
        @if(Auth::user()->role === 'admin')
            <div class="bg-slate-100 p-5 rounded-lg mt-8">
                <h3 class="mt-0 mb-4 text-lg font-bold text-slate-700">Admin: Create New Category</h3>
                <form action="{{ route('categories.store') }}" method="POST" class="flex gap-3">
                    @csrf
                    <div class="flex-grow">
                        <input type="text" name="name" placeholder="New category name" required value="{{ old('name') }}"
                               class="w-full p-2.5 border border-gray-300 rounded-md focus:border-sky-500 focus:ring-sky-500">
                        @error('name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="bg-green-600 text-white px-4 py-2.5 rounded-md font-bold border-none cursor-pointer hover:bg-green-700 transition-colors">
                        Create
                    </button>
                </form>
            </div>
        @endif

    </div>
</body>
</html>