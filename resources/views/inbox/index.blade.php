<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - {{ config('app.name', 'IT-Ticket-System') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    {{-- Alerts --}}
    @if (session('success'))
        <div id="success-alert" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 bg-green-100 text-green-800 px-6 py-3 rounded-lg shadow-md font-bold flex items-center gap-3">
            <span>{{ session('success') }}</span>
        </div>
        <script>setTimeout(() => document.getElementById('success-alert')?.remove(), 3000);</script>
    @endif

    <div class="max-w-5xl mx-auto bg-white p-10 rounded-xl shadow-sm">
        
        {{-- HEADER --}}
        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-5">
            <div class="flex items-center gap-4">
                <a href="{{ route('home') }}" class="text-slate-400 hover:text-sky-600 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <h1 class="text-3xl font-bold text-slate-800 m-0">Inbox</h1>
            </div>
            
            <div class="flex items-center gap-5 text-sm text-slate-600">
                @if(auth()->user()->unreadNotifications->count() > 0)
                    <form action="{{ route('inbox.markAll') }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-sky-600 hover:text-sky-800 font-bold bg-transparent border-none cursor-pointer">
                            Mark all as read
                        </button>
                    </form>
                    <span class="text-slate-300">|</span>
                @endif
                
                <span>
                    Hi, <span class="font-bold text-slate-800">{{ Auth::user()->name }}</span>
                </span>
            </div>
        </div>

        {{-- NOTIFICATIONS LIST --}}
        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="flex justify-between items-start p-5 rounded-lg border {{ $notification->read_at ? 'bg-white border-gray-100' : 'bg-sky-50 border-sky-100' }}">
                    <div class="flex gap-4">
                        <div>
                            <p class="text-slate-800 mb-1">
                                <span class="font-bold">{{ $notification->data['user_name'] ?? 'Someone' }}</span> 
                                
                                {{-- UPDATED LOGIC START --}}
                                @if(isset($notification->data['type']) && $notification->data['type'] === 'assigned')
                                    assigned ticket
                                @else
                                    commented on 
                                @endif
                                {{-- UPDATED LOGIC END --}}

                                <a href="{{ route('posts.show', $notification->data['post_id']) }}" class="text-sky-600 font-bold no-underline hover:underline">
                                    "{{ $notification->data['post_title'] ?? 'your post' }}"
                                </a>

                                {{-- SUFFIX START --}}
                                @if(isset($notification->data['type']) && $notification->data['type'] === 'assigned')
                                    to you
                                @endif
                                {{-- SUFFIX END --}}
                            </p>
                            <span class="text-xs text-slate-500">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                    </div>

                    @if(is_null($notification->read_at))
                        <form action="{{ route('inbox.read', $notification->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="text-xs bg-white border border-gray-200 hover:bg-gray-50 text-slate-600 px-3 py-1.5 rounded-md cursor-pointer transition-colors shadow-sm">
                                Mark read
                            </button>
                        </form>
                    @else
                        <span class="text-xs text-green-600 font-medium px-3 py-1.5">
                            ✓ Read
                        </span>
                    @endif
                </div>
            @empty
                <div class="text-center py-10">
                    <div class="inline-block p-4 rounded-full bg-slate-50 mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-slate-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <p class="text-slate-500">You're all caught up! No new notifications.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $notifications->links() }}
        </div>
    </div>
</body>
</html>