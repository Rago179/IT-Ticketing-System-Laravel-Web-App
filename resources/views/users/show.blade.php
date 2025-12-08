<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }}'s Profile</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Notifications --}}
    @if (session('success'))
        <div id="success-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow-md font-bold flex items-center gap-3">
            <span>{{ session('success') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('success-alert')?.remove(), 3000);</script>
    @endif

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-5 pb-3 border-b border-gray-100">
            <a href="{{ route('home') }}" class="text-2xl font-bold text-slate-800 no-underline hover:text-sky-600">
                {{ config('app.name', 'IT-Ticket-System') }}
            </a>
            <div class="flex items-center gap-4 text-sm font-bold text-slate-700">
                <span>
                    Hi, <span class="text-slate-900">{{ Auth::user()->name }}</span>
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

        {{-- Profile Card --}}
        <div class="bg-slate-50 rounded-lg p-6 mb-8">
            <div class="flex items-center gap-5 mb-6">
                <div class="w-20 h-20 rounded-full bg-sky-600 text-white flex items-center justify-center text-3xl font-bold shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="flex-grow">
                    <h1 class="text-3xl font-bold text-slate-800 m-0 leading-tight">{{ $user->name }}</h1>
                    <span class="inline-block bg-slate-200 text-slate-600 px-3 py-1 rounded-full text-xs font-bold capitalize mt-2">
                        {{ $user->role }}
                    </span>
                </div>
                
                {{-- Edit Button (Only for Owner) --}}
                @if(Auth::id() === $user->id)
                    <button onclick="toggleEdit()" id="editButton" class="bg-white border border-gray-300 text-slate-700 px-3 py-1.5 rounded text-sm font-bold hover:bg-gray-50 cursor-pointer shadow-sm">
                        ✎ Edit Profile
                    </button>
                @endif
            </div>
            
            <ul class="list-none p-0 m-0">
                <li class="py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center">
                    <strong class="text-slate-800 w-24 shrink-0">Email</strong> 
                    <span class="text-slate-600">{{ $user->email }}</span>
                </li>
                <li class="py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center">
                    <strong class="text-slate-800 w-24 shrink-0">Joined</strong> 
                    <span class="text-slate-600">{{ $user->created_at->format('M d, Y') }}</span>
                </li>
                
                {{-- Bio Section: View Mode --}}
                <li id="bio-display" class="py-3 border-b border-gray-200 sm:flex">
                    <strong class="text-slate-800 w-24 shrink-0 block mb-1 sm:mb-0">Bio</strong>
                    <span class="block text-slate-600 whitespace-pre-wrap max-w-xl">{{ $user->profile->bio ?? 'This user has not set up a bio.' }}</span>
                </li>

                {{-- Bio Section: Edit Mode (Hidden by default) --}}
                <li id="bio-edit-form" class="hidden py-3 border-b border-gray-200">
                    <div class="sm:flex">
                        <strong class="text-slate-800 w-24 shrink-0 block mb-2 sm:mb-0 pt-2">Bio</strong>
                        <div class="flex-grow max-w-xl">
                            <form action="{{ route('users.update', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <textarea name="bio" placeholder="Tell us about yourself..." 
                                          class="w-full p-3 border border-gray-300 rounded-md focus:ring-sky-500 focus:border-sky-500 min-h-[100px] mb-2">{{ $user->profile->bio ?? '' }}</textarea>
                                <div class="flex gap-2">
                                    <button type="submit" class="bg-sky-600 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-sky-700 transition-colors">Save Changes</button>
                                    <button type="button" onclick="toggleEdit()" class="bg-slate-400 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-slate-500 transition-colors">Cancel</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>

            {{-- Admin Block Controls --}}
            @if(Auth::user()->role === 'admin' && Auth::id() !== $user->id)
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <form action="{{ route('users.block', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if($user->is_blocked)
                            <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-green-700 cursor-pointer">✅ Unblock User</button>
                            <span class="text-red-600 font-bold ml-3 text-sm">This user is currently blocked.</span>
                        @else
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-md font-bold text-sm hover:bg-red-700 cursor-pointer">🚫 Block User</button>
                        @endif
                    </form>
                </div>
            @endif
        </div>

        <div class="mt-8">
            <h2 class="text-xl font-bold text-slate-800 border-b border-gray-100 pb-3 mb-4">User Activity</h2>
            
            <h3 class="text-lg font-bold text-slate-700 mb-2">Tickets Posted ({{ $user->posts->count() }})</h3>
            <div class="mb-6">
                @forelse($user->posts as $post)
                    <p class="mb-2">
                        <a href="{{ route('posts.show', $post) }}" class="text-sky-600 hover:underline font-medium">{{ $post->title }}</a> 
                        <span class="text-slate-400 text-sm ml-1">- {{ $post->created_at->diffForHumans() }}</span>
                    </p>
                @empty
                    <p class="text-slate-500 italic">This user has not posted any tickets.</p>
                @endforelse
            </div>
            
            <h3 class="text-lg font-bold text-slate-700 mb-2">Comments Posted ({{ $user->comments->count() }})</h3>
            <div>
                @forelse($user->comments as $comment)
                    <div class="mb-3 pl-4 border-l-4 border-slate-100 py-1">
                        <p class="text-slate-600 italic mb-1">"{{ Str::limit($comment->content, 60) }}"</p>
                        <p class="text-xs text-slate-400">
                            on 
                            @if($comment->post)
                                <a href="{{ route('posts.show', $comment->post) }}" class="text-sky-600 hover:underline">{{ $comment->post->title }}</a>
                            @else
                                <em class="text-red-400">Deleted Post</em>
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-slate-500 italic">This user has not posted any comments.</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Simple JS to toggle the form --}}
    <script>
        function toggleEdit() {
            const display = document.getElementById('bio-display');
            const form = document.getElementById('bio-edit-form');
            const btn = document.getElementById('editButton');

           
            if (form.classList.contains('hidden')) {
                form.classList.remove('hidden');
                display.classList.add('hidden');
                if(btn) btn.style.display = 'none'; 
            } else {
                form.classList.add('hidden');
                display.classList.remove('hidden');
                if(btn) btn.style.display = 'inline-block';
            }
        }
    </script>
</body>
</html>