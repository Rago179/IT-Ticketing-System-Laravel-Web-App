<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Main Container --}}
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        
        {{-- Header Section --}}
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800 no-underline hover:text-sky-600">
                {{ config('app.name', 'My App') }}
            </a>
            
            <div class="flex items-center gap-4 text-sm font-bold text-slate-700">
                <span>
                    Hi, <a href="{{ route('users.show', Auth::user()) }}" class="text-sky-600 hover:underline">{{ Auth::user()->name }}</a>
                </span>
                
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-500 underline hover:text-red-600 cursor-pointer bg-transparent border-none">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="inline-block mb-5 text-sky-600 font-bold hover:underline">
            &larr; Back to Dashboard
        </a>
        
        <h1 class="text-3xl font-bold text-sky-600 mb-6">Create a New Post</h1>

        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
            @csrf
            
            {{-- Title Input --}}
            <div class="mb-5">
                <label for="title" class="block font-bold text-gray-700 mb-1">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2">
                @error('title') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            {{-- Description Input --}}
            <div class="mb-5">
                <label for="description" class="block font-bold text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" required
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2 min-h-[120px]">{{ old('description') }}</textarea>
                @error('description') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Image Upload --}}
            <div class="mb-5">
                <label for="image" class="block font-bold text-gray-700 mb-1">Attach Image (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
                @error('image') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Priority Input --}}
            <div class="mb-5">
                <label for="priority" class="block font-bold text-gray-700 mb-1">
                    Priority <span class="font-normal text-sm text-gray-500">(1 = Lowest, 4 = Highest)</span>
                </label>
                <input type="number" id="priority" name="priority" min="1" max="4" value="{{ old('priority', 1) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2">
                @error('priority') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>

            {{-- Categories Grid --}}
            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Categories (Optional)</label>
                <div class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-3 bg-slate-50 p-4 rounded-lg border border-gray-200">
                    @forelse($categories as $category)
                        <div>
                            <label for="category-{{ $category->id }}" class="flex items-center gap-2 cursor-pointer font-normal text-gray-700 hover:text-sky-600">
                                <input type="checkbox"
                                       name="categories[]"
                                       id="category-{{ $category->id }}"
                                       value="{{ $category->id }}"
                                       class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                       @if(is_array(old('categories')) && in_array($category->id, old('categories'))) checked @endif
                                >
                                {{ $category->name }}
                            </label>
                        </div>
                    @empty
                        <p class="text-gray-500 italic col-span-full">No categories created. Admin can create them from the dashboard.</p>
                    @endforelse
                </div>
                @error('categories.*') <div class="text-red-600 text-sm mt-1">{{ $message }}</div> @enderror
            </div>
            
            {{-- Submit Button --}}
            <div class="mt-6">
                <button type="submit" class="bg-sky-600 text-white py-3 px-6 rounded-md font-bold text-lg hover:bg-sky-700 transition duration-200 cursor-pointer shadow-sm w-full sm:w-auto">
                    Publish Post
                </button>
            </div>
        </form>
    </div>
</body>
</html>