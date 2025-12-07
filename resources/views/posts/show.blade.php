<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {{-- IMPORTANT: This loads Tailwind --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Global Alerts --}}
    @if (session('success'))
        <div id="post-success-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow-md font-bold flex items-center gap-3 min-w-[300px] justify-between">
            <span>{{ session('success') }}</span>
            <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900 text-xl font-bold bg-transparent border-none cursor-pointer">&times;</button>
        </div>
    @endif

    @if (session('error'))
        <div id="post-error-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-red-100 text-red-800 px-6 py-3 rounded-lg shadow-md font-bold border border-red-500 flex items-center gap-3 min-w-[300px] justify-between">
            <span>🚫 {{ session('error') }}</span>
            <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900 text-xl font-bold bg-transparent border-none cursor-pointer">&times;</button>
        </div>
    @endif

    {{-- Main Container --}}
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800 no-underline hover:text-sky-600">
                {{ config('app.name', 'IT-Ticket-System') }}
            </a>
            <div class="flex items-center gap-4 text-sm font-bold text-slate-700">
                <span>
                    Hi, <a href="{{ route('users.show', Auth::user()) }}" class="text-sky-600 hover:underline">{{ Auth::user()->name }}</a>
                </span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-500 underline hover:text-red-600 cursor-pointer bg-transparent border-none">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="inline-block mb-5 text-sky-600 font-bold hover:underline">
            &larr; Back to Dashboard
        </a>

        <h1 class="text-3xl font-bold text-slate-800 mb-4">{{ $post->title }}</h1>

        @if(Auth::id() === $post->user_id || Auth::user()->role === 'admin')
            <div class="mb-6">
                <a href="{{ route('posts.edit', $post) }}" class="inline-block bg-orange-500 text-white px-3 py-2 rounded-md font-bold text-sm hover:bg-orange-600 transition-colors no-underline">
                    ✎ Edit Post
                </a>
            </div>
        @endif

        {{-- START: Admin/IT Controls --}}
        @if(in_array(Auth::user()->role, ['admin', 'it']))
            <div class="bg-slate-50 p-6 rounded-r-lg border-l-4 border-sky-600 mb-8">
                <h3 class="mt-0 mb-4 text-lg font-bold text-slate-700">IT/Admin Controls</h3>
                
                {{-- 1. Status Update --}}
                <form action="{{ route('posts.updateStatus', $post->id) }}" method="POST" class="mb-4 border-b border-slate-200 pb-4 last:border-0 last:pb-0 last:mb-0">
                    @csrf
                    @method('PATCH')
                    <label for="status" class="block font-bold text-slate-700 mb-2">Update Status:</label>
                    <div class="flex gap-3">
                        <select name="status" id="status" class="p-2 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500">
                            <option value="open" {{ $post->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $post->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $post->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700 transition-colors">Update Status</button>
                    </div>
                    @error('status') <div class="text-red-600 text-sm font-bold mt-2">{{ $message }}</div> @enderror
                </form>

                {{-- 2. Category Update --}}
                <form action="{{ route('posts.update', $post->id) }}" method="POST" class="mb-4 border-b border-slate-200 pb-4 last:border-0 last:pb-0 last:mb-0">
                    @csrf
                    @method('PUT')
                    <label class="block font-bold text-slate-700 mb-2">Update Categories:</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 mb-3 bg-white p-3 rounded border border-gray-200">
                        @foreach($categories as $category)
                            <label for="cat-{{ $category->id }}" class="flex items-center gap-2 cursor-pointer text-sm text-slate-700 hover:text-sky-600">
                                <input type="checkbox" name="categories[]" id="cat-{{ $category->id }}" value="{{ $category->id }}"
                                       class="rounded border-gray-300 text-sky-600 focus:ring-sky-500"
                                       @if($post->categories->contains($category->id)) checked @endif>
                                {{ $category->name }}
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-md hover:bg-sky-700 transition-colors">Save Categories</button>
                </form>
                
                {{-- 3. Pin Post --}}
                @if(Auth::user()->role === 'admin')
                <form action="{{ route('posts.pin', $post) }}" method="POST" class="mb-0">
                    @csrf
                    @method('PATCH')
                    @if($post->is_pinned)
                        <button type="submit" class="bg-orange-500 text-white px-4 py-2 rounded-md hover:bg-orange-600 transition-colors">📌 Unpin Post</button>
                    @else
                        <button type="submit" class="bg-slate-500 text-white px-4 py-2 rounded-md hover:bg-slate-600 transition-colors">📌 Pin Post</button>
                    @endif
                </form>
                @endif
            </div>
        @endif
        {{-- END: Admin/IT Controls --}}

        {{-- INFO BAR WITH STATUS --}}
        <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 mb-6 pb-3 border-b border-gray-100">
            @php
                $statusStyles = [
                    'open' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                    'in_progress' => 'bg-sky-100 text-sky-800 border-sky-200',
                    'resolved' => 'bg-slate-100 text-slate-600 border-slate-200',
                ];
                $statusColor = $statusStyles[$post->status] ?? 'bg-gray-100 text-gray-800';
            @endphp
            
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase border {{ $statusColor }}">
                {{ str_replace('_', ' ', $post->status) }}
            </span>

            <span class="text-slate-300">|</span>

            <span>
                Priority: <strong class="text-slate-800">{{ $post->priority }}/4</strong>
            </span>
            
            <span class="text-slate-300">|</span>
            
            <span>
                Posted by <a href="{{ route('users.show', $post->user) }}" class="font-bold text-sky-600 hover:underline">{{ $post->user->name }}</a> 
                on {{ $post->created_at->format('M d, Y') }}
            </span>
        </div>
        
        <div class="flex flex-wrap gap-2 mb-6">
            @foreach($post->categories as $category)
                <span class="bg-slate-200 text-slate-600 px-3 py-1 rounded-full text-xs font-bold">{{ $category->name }}</span>
            @endforeach
        </div>

        @if($post->image_path)
            <div class="mb-6">
                <img src="{{ asset('storage/' . $post->image_path) }}" 
                    alt="Post Image" 
                    class="max-w-full h-auto rounded-lg border border-gray-200 shadow-sm">
            </div>
        @endif

        <div class="text-lg leading-relaxed text-slate-800 whitespace-pre-wrap mb-10">
            {!! nl2br(e($post->description)) !!}
        </div>

        {{-- START: LIKE BUTTON (HEART ICON) --}}
            <div class="mb-10 pt-4 border-t border-gray-50">
                <form action="{{ route('like.toggle') }}" method="POST">
                    @csrf
                    <input type="hidden" name="id" value="{{ $post->id }}">
                    <input type="hidden" name="type" value="post">
                    
                    <button type="submit" 
                        class="group flex items-center gap-2 px-4 py-2 rounded-full border transition-all duration-200 ease-in-out cursor-pointer
                        {{ $post->isLikedBy(Auth::user()) 
                            ? 'bg-rose-50 border-rose-200 text-rose-600 hover:bg-rose-100' 
                            : 'bg-white border-gray-200 text-slate-500 hover:bg-gray-50 hover:border-gray-300' 
                        }}">
                        
                        <svg xmlns="http://www.w3.org/2000/svg" 
                            fill="{{ $post->isLikedBy(Auth::user()) ? 'currentColor' : 'none' }}" 
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" 
                            class="w-6 h-6 transition-transform duration-200 group-hover:scale-110 {{ $post->isLikedBy(Auth::user()) ? 'scale-110' : '' }}">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z" />
                        </svg>
                        
                        <span class="font-bold">{{ $post->likes->count() }}</span> 
                        <span class="text-sm font-medium">{{ Str::plural('Like', $post->likes->count()) }}</span>
                    </button>
                </form>
            </div>
            {{-- END: LIKE BUTTON --}}

        {{-- COMMENTS SECTION --}}
        <div id="comments-container">
            @forelse($post->comments as $comment)
                @include('comments.item', ['comment' => $comment])
            @empty
                <p id="no-comments-msg" class="text-slate-500 italic">No comments yet.</p>
            @endforelse
        </div>

        <div class="mt-10">
            <h3 class="text-xl font-bold text-slate-800 mb-4">Leave a Comment</h3>
            <form id="comment-form" method="POST" action="{{ route('comments.store') }}">
                @csrf
                <input type="hidden" name="post_id" value="{{ $post->id }}">
                <textarea id="comment-content" name="content" required placeholder="Write something..."
                        class="w-full p-4 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 min-h-[100px] mb-3"></textarea>
                
                <div id="comment-error" class="hidden text-red-600 text-sm mb-3 font-bold"></div>
                
                <button type="submit" id="submit-btn" class="bg-sky-600 text-white px-6 py-3 rounded-md font-bold hover:bg-sky-700 transition-colors shadow-sm">
                    Post Comment
                </button>
            </form>
        </div>
    </div>

    {{-- AJAX Script --}}
    <script>
        function toggleEdit(commentId) {
            const body = document.getElementById(`comment-body-${commentId}`);
            const form = document.getElementById(`edit-form-${commentId}`);
            
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                body.classList.add('hidden');
            } else {
                form.classList.add('hidden');
                body.classList.remove('hidden');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const commentForm = document.getElementById('comment-form');

            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const form = this;
                    const submitBtn = document.getElementById('submit-btn');
                    const errorDiv = document.getElementById('comment-error');
                    const noCommentsMsg = document.getElementById('no-comments-msg');
                    const commentsContainer = document.getElementById('comments-container');
                    const countSpan = document.getElementById('comment-count');
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                
                    submitBtn.disabled = true;
                    submitBtn.innerText = 'Posting...';
                    errorDiv.classList.add('hidden');

                    const formData = new FormData(form);

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            
                            if(noCommentsMsg) noCommentsMsg.remove();
                            commentsContainer.insertAdjacentHTML('beforeend', data.html);
                            
                            if(countSpan) countSpan.innerText = data.count;
                            form.reset();
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        errorDiv.innerText = "Error posting comment.";
                        errorDiv.classList.remove('hidden');
                    })
                    .finally(() => {
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Post Comment';
                    });
                });
            }
        });
    </script>
</body>
</html>