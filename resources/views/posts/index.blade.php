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
                        <div>
                            <h4>{{ $post->title }}</h4>
                            <p>By: {{ $post->user->name }}</p>
                        </div>
                        <hr>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>