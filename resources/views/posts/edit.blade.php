<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - {{ $post->title }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        <h1 class="text-3xl font-bold text-sky-600 mb-6">Edit Post</h1>

        <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            {{-- Title --}}
            <div class="mb-5">
                <label for="title" class="block font-bold text-gray-700 mb-1">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2">
            </div>
            
            {{-- Description --}}
            <div class="mb-5">
                <label for="description" class="block font-bold text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" required
                          class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2 min-h-[120px]">{{ old('description', $post->description) }}</textarea>
            </div>

            {{-- Image Upload & Preview --}}
            <div class="mb-5">
                <label for="image" class="block font-bold text-gray-700 mb-1">Change Image (Optional)</label>
                
                @if($post->image_path)
                    <div class="mb-3">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="Current Image" class="h-24 rounded-md border border-gray-200">
                        <div class="text-xs text-gray-500 mt-1">Current Image</div>
                    </div>
                @endif

                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full border border-gray-300 rounded-md p-2 bg-white text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-sky-50 file:text-sky-700 hover:file:bg-sky-100">
            </div>

            {{-- Priority --}}
            <div class="mb-5">
                <label for="priority" class="block font-bold text-gray-700 mb-1">Priority</label>
                <input type="number" id="priority" name="priority" min="1" max="4" value="{{ old('priority', $post->priority) }}" required
                       class="w-full border-gray-300 rounded-md shadow-sm focus:border-sky-500 focus:ring-sky-500 p-2">
            </div>

            {{-- Categories --}}
            <div class="mb-5">
                <label class="block font-bold text-gray-700 mb-2">Categories</label>
                <div class="grid grid-cols-[repeat(auto-fill,minmax(150px,1fr))] gap-3 bg-slate-50 p-4 rounded-lg border border-gray-200">
                    @foreach($categories as $category)
                        <label class="flex items-center gap-2 cursor-pointer font-normal text-gray-700 hover:text-sky-600">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                class="rounded border-gray-300 text-sky-600 shadow-sm focus:ring-sky-500"
                                @if($post->categories->contains($category->id)) checked @endif
                            >
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            
            {{-- Actions --}}
            <div class="flex items-center gap-3 mt-8">
                <a href="{{ route('posts.show', $post) }}" class="bg-slate-500 text-white py-3 px-6 rounded-md font-bold text-lg hover:bg-slate-600 transition duration-200 cursor-pointer shadow-sm no-underline inline-block">
                    Cancel
                </a>
                <button type="submit" class="bg-sky-600 text-white py-3 px-6 rounded-md font-bold text-lg hover:bg-sky-700 transition duration-200 cursor-pointer shadow-sm">
                    Update Post
                </button>
            </div>
        </form>
    </div>
</body>
</html>