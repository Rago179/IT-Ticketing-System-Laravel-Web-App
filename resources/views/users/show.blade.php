<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }}'s Profile</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .header-title { font-size: 1.5em; font-weight: bold; margin: 0; text-decoration: none; color: #333; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }
        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        
        .profile-card { background: #f1f5f9; border-radius: 8px; padding: 25px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; background: #3490dc; color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5em; font-weight: bold; }
        .profile-name { font-size: 2em; margin: 0; }
        .profile-role {
            display: inline-block;
            background: #e2e8f0;
            color: #475569;
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 0.9em;
            font-weight: bold;
            text-transform: capitalize;
            margin-top: 5px;
        }
        .profile-info { list-style: none; padding: 0; }
        .profile-info li { padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .profile-info li:last-child { border-bottom: none; }
        .profile-info strong { color: #333; width: 100px; display: inline-block; }
    </style>
</head>
<body>
    <div class="container">
        <!-- Standard Header -->
        <div class="header">
            <a href="{{ route('home') }}" class="header-title">{{ config('app.name', 'IT-Ticket-System') }}</a>
            <div class="header-controls">
                <span class="user-info">Hi, {{ Auth::user()->name }}</span>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ url()->previous() }}" class="back-link">&larr; Back</a>

        <!-- Profile Content -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <span class="profile-role">{{ $user->role }}</span>
                </div>
            </div>
            
            <ul class="profile-info">
                <li><strong>Email</strong> {{ $user->email }}</li>
                <li><strong>Joined</strong> {{ $user->created_at->format('M d, Y') }}</li>
                <li>
                    <strong style="vertical-align: top;">Bio</strong>
                    <span style="display: inline-block; max-width: 80%;">
                        {{ $user->profile->bio ?? 'This user has not set up a bio.' }}
                    </span>
                </li>
            </ul>
        </div>

        <div class="user-activity" style="margin-top: 30px;">
            <h2 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">User Activity</h2>
            <h3>Tickets Posted ({{ $user->posts->count() }})</h3>
            @forelse($user->posts as $post)
                <p><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></p>
            @empty
                <p>This user has not posted any tickets.</p>
            @endforelse
            
            <h3 style="margin-top: 25px;">Comments Posted ({{ $user->comments->count() }})</h3>
            @forelse($user->comments as $comment)
                <p style="padding-left: 15px; border-left: 3px solid #eee;">
                    "{{ Str::limit($comment->content, 50) }}" 
                    on <a href="{{ route('posts.show', $comment->post) }}">{{ $comment->post->title }}</a>
                </p>
            @empty
                <p>This user has not posted any comments.</p>
            @endforelse
        </div>

    </div>
</body>
</html>