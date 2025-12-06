<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inbox - Notifications</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans p-5 bg-gray-50 text-slate-800">

    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-sm">
        <div class="flex justify-between items-center mb-8 border-b border-gray-100 pb-4">
            <h1 class="text-2xl font-bold text-slate-800 m-0">Inbox</h1>
            <div class="flex gap-4 items-center">
                @if(Auth::user()->unreadNotifications->count() > 0)
                    <form action="{{ route('notifications.readAll') }}" method="POST">
                        @csrf
                        <button type="submit" class="text-sm text-sky-600 hover:underline bg-transparent border-none cursor-pointer">Mark all read</button>
                    </form>
                @endif
                <a href="{{ route('home') }}" class="text-sky-600 font-bold no-underline hover:underline">&larr; Back to Dashboard</a>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($notifications as $notification)
                <div class="p-4 rounded-lg border {{ $notification->read_at ? 'bg-slate-50 border-gray-100' : 'bg-sky-50 border-sky-200' }} flex justify-between items-start gap-4">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if(!$notification->read_at)
                                <span class="w-2 h-2 rounded-full bg-sky-600 inline-block"></span>
                            @endif
                            <span class="font-bold text-slate-700">{{ $notification->data['commenter_name'] }}</span>
                            <span class="text-slate-500 text-sm">commented on your post</span>
                        </div>
                        
                        <p class="text-slate-600 mb-2 italic">"{{ $notification->data['comment_content'] }}"</p>
                        <small class="text-slate-400">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>

                    <a href="{{ route('notifications.read', $notification->id) }}" 
                       class="whitespace-nowrap px-4 py-2 bg-white border border-gray-200 rounded text-sm font-bold text-slate-600 hover:border-sky-500 hover:text-sky-600 transition-colors no-underline">
                       View Post &rarr;
                    </a>
                </div>
            @empty
                <div class="text-center py-10 text-slate-500 italic">
                    You have no notifications.
                </div>
            @endforelse

            <div class="mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</body>
</html>