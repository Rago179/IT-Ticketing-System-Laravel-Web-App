<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    <div class="max-w-4xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        
        {{-- Header --}}
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold text-slate-800 m-0">IT Dashboard</h1>
            <div class="flex items-center gap-4 text-sm font-bold text-slate-700">
                <span>
                    Hi, <a href="{{ route('users.show', Auth::user()) }}" class="text-sky-600 hover:underline">{{ Auth::user()->name }}</a>
                </span>
                <a href="{{ route('home') }}" class="bg-slate-500 text-white px-3 py-2 rounded-md text-sm font-bold no-underline hover:bg-slate-600 transition-colors">
                    All Posts
                </a>
                 <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-slate-500 underline hover:text-red-600 cursor-pointer bg-transparent border-none">
                        Log Out
                    </button>
                </form>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="mb-6 p-4 bg-slate-100 rounded-lg flex flex-wrap gap-3 items-center text-sm">
            <span class="font-bold text-slate-700 mr-1">View:</span>
            
            <a href="{{ route('it.dashboard') }}" 
               class="px-3 py-2 rounded-md border font-bold transition-colors no-underline
               {{ !request()->filled('assigned_to_me') ? 'bg-sky-600 text-white border-transparent' : 'bg-white text-slate-700 border-gray-300 hover:bg-white' }}">
               All Tickets
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['assigned_to_me' => 1]) }}"
               class="px-3 py-2 rounded-md border font-bold transition-colors no-underline
               {{ request()->filled('assigned_to_me') ? 'bg-sky-600 text-white border-transparent' : 'bg-white text-slate-700 border-gray-300 hover:bg-white' }}">
               Assigned to Me
            </a>
            
            <span class="font-bold text-slate-700 ml-4 mr-1">Sort By:</span>
            
            <a href="{{ request()->fullUrlWithQuery(['sort' => null]) }}"
               class="px-3 py-2 rounded-md border font-bold transition-colors no-underline
               {{ !request()->filled('sort') ? 'bg-slate-500 text-white border-transparent' : 'bg-white text-slate-700 border-gray-300 hover:bg-white' }}">
               Latest
            </a>
            
            <a href="{{ request()->fullUrlWithQuery(['sort' => 'priority']) }}"
               class="px-3 py-2 rounded-md border font-bold transition-colors no-underline
               {{ request()->get('sort') === 'priority' ? 'bg-slate-500 text-white border-transparent' : 'bg-white text-slate-700 border-gray-300 hover:bg-white' }}">
               Priority
            </a>
        </div>

        @if (session('success'))
            <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg mb-6 border border-green-200 font-bold">
                {{ session('success') }}
            </div>
        @endif

        <div class="border-t border-gray-100">
            @forelse ($posts as $post)
                <div class="p-6 border-b border-gray-100 relative hover:bg-slate-50 transition-colors">
                    
                    {{-- Title Row --}}
                    <div class="flex justify-between items-start mb-2">
                        <h2 class="text-xl font-bold m-0">
                            <a href="{{ route('posts.show', $post) }}" class="text-sky-600 no-underline hover:underline">
                                {{ $post->title }}
                            </a>
                        </h2>

                        {{-- Delete Button (Admin Only) --}}
                        @if(Auth::user()->role === 'admin')
                            <form action="{{ route('posts.destroy', $post) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this ticket?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-500 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-red-600 transition-colors cursor-pointer border-none ml-3">
                                    Delete
                                </button>
                            </form>
                        @endif
                    </div>
                    
                    <div class="text-sm text-slate-500 mb-3 leading-relaxed">
                        <span class="text-slate-800 font-bold">Priority: {{ $post->priority }}/4</span>
                        <span class="mx-1 text-slate-300">|</span>
                        Submitted by: <a href="{{ route('users.show', $post->user) }}" class="text-sky-600 hover:underline font-bold">{{ $post->user->name }}</a> 
                        on {{ $post->created_at->format('M d, Y') }}
                        <span class="mx-1 text-slate-300">|</span>
                        💬 {{ $post->comments_count ?? $post->comments->count() }} Comments
                        <br>
                        <div class="mt-1">
                            Assigned to: 
                            @if($post->assignedTo)
                                <a href="{{ route('users.show', $post->assignedTo) }}" class="text-sky-600 hover:underline font-bold">{{ $post->assignedTo->name }}</a>
                            @else
                                <span class="font-bold text-slate-400">Unassigned</span>
                            @endif
                        </div>
                    </div>
                    
                    <p class="text-slate-700 mb-4">{{ Str::limit($post->description, 150) }}</p>

                    <div class="flex justify-between items-center mt-4 pt-3 border-t border-dashed border-gray-200">
                        {{-- Status Form --}}
                        <form action="{{ route('posts.updateStatus', $post->id) }}" method="POST" class="flex items-center gap-2 text-sm">
                            @csrf
                            @method('PATCH')
                            <label for="status-{{ $post->id }}" class="font-bold text-slate-600">Status:</label>
                            {{-- UPDATED: Added appearance-none, pr-8, and custom SVG background --}}
                            <select name="status" id="status-{{ $post->id }}" onchange="this.form.submit()" 
                                    class="appearance-none py-1.5 pl-3 pr-8 border border-gray-300 rounded text-sm focus:ring-sky-500 focus:border-sky-500 bg-white cursor-pointer bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E')] bg-[length:1.25em_1.25em] bg-no-repeat bg-[right_0.5rem_center]">
                                <option value="open" @if($post->status == 'open') selected @endif>Open</option>
                                <option value="in_progress" @if($post->status == 'in_progress') selected @endif>In Progress</option>
                                <option value="resolved" @if($post->status == 'resolved') selected @endif>Resolved</option>
                            </select>
                        </form>

                        {{-- Assignment Controls --}}
                        <div class="flex items-center gap-2">
                            @if(Auth::user()->role === 'admin')
                                {{-- ADMIN: Dropdown to assign to anyone --}}
                                <form action="{{ route('posts.assign', $post) }}" method="POST" class="flex items-center gap-2">
                                    @csrf
                                    @method('PATCH')
                                    {{-- UPDATED: Added appearance-none, pr-8, and custom SVG background --}}
                                    <select name="assigned_user_id" class="appearance-none py-1.5 pl-3 pr-8 border border-gray-300 rounded text-xs focus:ring-sky-500 focus:border-sky-500 max-w-[150px] bg-white cursor-pointer bg-[url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%27http://www.w3.org/2000/svg%27 fill=%27none%27 viewBox=%270 0 20 20%27%3E%3Cpath stroke=%27%236b7280%27 stroke-linecap=%27round%27 stroke-linejoin=%27round%27 stroke-width=%271.5%27 d=%27M6 8l4 4 4-4%27/%3E%3C/svg%3E')] bg-[length:1.25em_1.25em] bg-no-repeat bg-[right_0.5rem_center]">
                                        <option value="{{ Auth::id() }}">Assign to Me</option>
                                        @foreach($itStaff as $staff)
                                            @if($staff->id !== Auth::id())
                                                <option value="{{ $staff->id }}" {{ $post->assigned_to_user_id == $staff->id ? 'selected' : '' }}>
                                                    {{ $staff->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-indigo-700 transition-colors cursor-pointer border-none">
                                        Update
                                    </button>
                                </form>

                            @elseif(!$post->assignedTo)
                                {{-- IT STAFF: Assign to self only --}}
                                <form action="{{ route('posts.assign', $post) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="bg-green-600 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-green-700 transition-colors cursor-pointer border-none">
                                        Assign to Me
                                    </button>
                                </form>

                            @elseif($post->assigned_to_user_id === Auth::id())
                                <span class="text-green-600 font-bold text-sm flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    Assigned to You
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-slate-500 italic p-6 text-center">No tickets found matching your filters.</p>
            @endforelse
        </div>

        <div class="mt-8">
            {{ $posts->links() }}
        </div>
    </div>
</body>
</html>