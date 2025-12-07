<div class="bg-slate-50 p-5 rounded-lg mb-4" id="comment-{{ $comment->id }}">
    <div class="flex justify-between items-center mb-2 font-bold">
        <a href="{{ route('users.show', $comment->user) }}" class="text-slate-800 hover:underline">{{ $comment->user->name }}</a>
        <span class="text-sm font-normal text-slate-500">
            {{ $comment->created_at->diffForHumans() }}
        </span>
    </div>

    {{-- Display Mode --}}
    <div class="whitespace-pre-wrap text-slate-700 text-left mb-3" id="comment-body-{{ $comment->id }}">
        {!! nl2br(e($comment->content)) !!}
    </div>

    {{-- LIKE BUTTON FOR COMMENTS --}}
    <div class="flex items-center justify-start mb-2">
        <form action="{{ route('like.toggle') }}" method="POST">
            @csrf
            <input type="hidden" name="id" value="{{ $comment->id }}">
            <input type="hidden" name="type" value="comment">
            
            <button type="submit" 
                class="group flex items-center gap-1.5 px-3 py-1 rounded-full border transition-all duration-200 ease-in-out cursor-pointer text-xs
                {{ $comment->isLikedBy(Auth::user()) 
                    ? 'bg-rose-50 border-rose-200 text-rose-600 hover:bg-rose-100' 
                    : 'bg-white border-gray-200 text-slate-500 hover:bg-gray-50 hover:border-gray-300' 
                }}">
                
                <svg xmlns="http://www.w3.org/2000/svg" 
                    fill="{{ $comment->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}" 
                    viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                    class="w-4 h-4 transition-transform duration-200 group-hover:scale-110 {{ $comment->isLikedBy(Auth::user()) ? 'scale-110' : '' }}">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                </svg>
                
                <span class="font-bold">{{ $comment->likes->count() }}</span> 
            </button>
        </form>
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

        <div class="text-right mt-1 text-sm">
            <button onclick="toggleEdit({{ $comment->id }})" class="text-orange-500 hover:underline mr-3 bg-transparent border-none cursor-pointer">Edit</button>
            
            <form method="POST" action="{{ route('comments.destroy', $comment) }}" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-800 underline bg-transparent border-none cursor-pointer">Delete</button>
            </form>
        </div>
    @endif
</div>