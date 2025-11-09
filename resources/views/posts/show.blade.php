<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $post->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    <h3 class="text-lg font-bold mb-2">{{ $post->title }}</h3>
                    <p class="mb-4 text-gray-600">Posted by {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }}</p>
                    <p class="text-lg border-b pb-4 mb-4">{{ $post->description }}</p>

                    <h4 class="font-bold text-md mb-4">Comments ({{ $post->comments->count() }})</h4>

                    <div class="space-y-4 mb-8">
                        @forelse ($post->comments as $comment)
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="font-semibold">{{ $comment->user->name }}</span>
                                    <span class="text-sm text-gray-500">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p>{{ $comment->content }}</p>
                                
                                @if (auth()->id() === $comment->user_id)
                                    <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="mt-2 text-right">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 text-sm hover:underline">Delete</button>
                                    </form>
                                @endif
                            </div>
                        @empty
                            <p class="text-gray-500 italic">No comments yet.</p>
                        @endforelse
                    </div>

                    <div class="mt-6 border-t pt-6">
                        <h4 class="font-bold mb-2">Leave a Comment</h4>
                        <form method="POST" action="{{ route('comments.store') }}">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $post->id }}">

                            <textarea name="content" rows="3" 
                                class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                placeholder="Write something..." required></textarea>
                            
                            <div class="mt-2 text-right">
                                <x-primary-button>Post Comment</x-primary-button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>