<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            All Posts
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3>Posts List</h3>
                    {{-- You can loop through your $posts here later --}}
                    @foreach ($posts as $post)
                    <div class="mb-4 p-4 border rounded bg-gray-50 hover:bg-gray-100">
                        <h4 class="text-lg font-bold">
                            <a href="{{ route('posts.show', $post) }}" class="text-indigo-600 hover:underline">
                                {{ $post->title }}
                            </a>
                        </h4>
                        <p class="text-sm text-gray-600">By: {{ $post->user->name }} | Comments: {{ $post->comments->count() }}</p>
                    </div>
                @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>