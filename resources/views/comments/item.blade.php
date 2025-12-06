<div class="bg-slate-50 p-5 rounded-lg mb-4" id="comment-{{ $comment->id }}">
    <div class="flex justify-between items-center mb-2 font-bold">
        <a href="{{ route('users.show', $comment->user) }}" class="text-slate-800 hover:underline">{{ $comment->user->name }}</a>
        <span class="text-sm font-normal text-slate-500">
            {{ $comment->created_at->diffForHumans() }}
        </span>
    </div>

    {{-- Display Mode --}}
    <div class="whitespace-pre-wrap text-slate-700 text-left" id="comment-body-{{ $comment->id }}">
        {!! nl2br(e($comment->content)) !!}
    </div>

    {{-- Edit Mode --}}
    @if(Auth::id() === $comment->user_id || Auth::user()->role === 'admin')
        <form action="{{ route('comments.update', $comment->id) }}" method="POST" 
            id="edit-form-{{ $comment->id }}" class="hidden mt-3 text-left">
            @csrf
            @method('PUT')
            <textarea name="content" class="w-full p-3 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 min-h-[80px]">{{ $comment->content }}</textarea>
            <div class="mt-2 flex gap-2">
                <button type="submit" class="bg-sky-600 text-white px-3 py-1 rounded text-sm hover:bg-sky-700">Save</button>
                <button type="button" onclick="toggleEdit({{ $comment->id }})" class="bg-gray-300 text-gray-700 px-3 py-1 rounded text-sm hover:bg-gray-400">Cancel</button>
            </div>
        </form>

        <div class="text-right mt-3 text-sm">
            <button onclick="toggleEdit({{ $comment->id }})" class="text-orange-500 hover:underline mr-3 bg-transparent border-none cursor-pointer">Edit</button>
            
            <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 underline bg-transparent border-none cursor-pointer">Delete</button>
            </form>
        </div>
    @endif
</div>