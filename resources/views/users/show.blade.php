<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $user->name }}'s Profile</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .header-title { font-size: 1.5em; font-weight: bold; margin: 0; text-decoration: none; color: #333; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }
        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        
        /* Profile Styles */
        .profile-card { background: #f1f5f9; border-radius: 8px; padding: 25px; }
        .profile-header { display: flex; align-items: center; gap: 20px; margin-bottom: 20px; }
        .profile-avatar { width: 80px; height: 80px; border-radius: 50%; background: #3490dc; color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5em; font-weight: bold; }
        .profile-details { flex-grow: 1; }
        .profile-name { font-size: 2em; margin: 0; }
        .profile-role { display: inline-block; background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 15px; font-size: 0.9em; font-weight: bold; text-transform: capitalize; margin-top: 5px; }
        .profile-info { list-style: none; padding: 0; }
        .profile-info li { padding: 10px 0; border-bottom: 1px solid #e2e8f0; }
        .profile-info li:last-child { border-bottom: none; }
        .profile-info strong { color: #333; width: 100px; display: inline-block; }

        /* Form Styles (Consistent with other views) */
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-height: 100px; margin-bottom: 10px; box-sizing: border-box; font-family: sans-serif; }
        .submit-btn { background: #3490dc; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9em; }
        .submit-btn:hover { background: #2779bd; }
        .cancel-btn { background: #94a3b8; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer; font-size: 0.9em; text-decoration: none; display: inline-block; }
        .cancel-btn:hover { background: #64748b; }
        
        .edit-btn { background: white; border: 1px solid #ccc; padding: 5px 10px; border-radius: 4px; cursor: pointer; font-size: 0.85em; color: #333; font-weight: bold; }
        .edit-btn:hover { background: #e2e8f0; }
        
        /* Utility */
        .hidden { display: none; }
    </style>
</head>
<body>
    {{-- Notifications --}}
    @if (session('success'))
        <div style="background-color: #dcfce7; color: #166534; padding: 12px 24px; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 99; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
            {{ session('success') }}
        </div>
    @endif

    <div class="container">
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

        {{-- CHANGED: Use specific route instead of url()->previous() to avoid loops --}}
        <a href="{{ route('home') }}" class="back-link">&larr; Back to Dashboard</a>

        <div class="profile-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="profile-details">
                    <h1 class="profile-name">{{ $user->name }}</h1>
                    <span class="profile-role">{{ $user->role }}</span>
                </div>
                
                {{-- Edit Button (Only for Owner) --}}
                @if(Auth::id() === $user->id)
                    <button onclick="toggleEdit()" class="edit-btn" id="editButton">✎ Edit Profile</button>
                @endif
            </div>
            
            <ul class="profile-info">
                <li><strong>Email</strong> {{ $user->email }}</li>
                <li><strong>Joined</strong> {{ $user->created_at->format('M d, Y') }}</li>
                
                {{-- Bio Section: View Mode --}}
                <li id="bio-display">
                    <strong style="vertical-align: top;">Bio</strong>
                    <span style="display: inline-block; max-width: 80%; white-space: pre-wrap;">{{ $user->profile->bio ?? 'This user has not set up a bio.' }}</span>
                </li>

                {{-- Bio Section: Edit Mode (Hidden by default) --}}
                <li id="bio-edit-form" class="hidden">
                    <strong style="vertical-align: top;">Bio</strong>
                    <div style="display: inline-block; width: 80%;">
                        <form action="{{ route('users.update', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <textarea name="bio" placeholder="Tell us about yourself...">{{ $user->profile->bio ?? '' }}</textarea>
                            <div style="margin-top: 5px;">
                                <button type="submit" class="submit-btn">Save Changes</button>
                                <button type="button" onclick="toggleEdit()" class="cancel-btn">Cancel</button>
                            </div>
                        </form>
                    </div>
                </li>
            </ul>

            {{-- Admin Block Controls --}}
            @if(Auth::user()->role === 'admin' && Auth::id() !== $user->id)
                <div style="margin-top: 20px; border-top: 1px solid #eee; padding-top: 20px;">
                    <form action="{{ route('users.block', $user) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        @if($user->is_blocked)
                            <button type="submit" style="background: #16a34a; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">✅ Unblock User</button>
                            <span style="color: #dc2626; font-weight: bold; margin-left: 10px;">This user is currently blocked.</span>
                        @else
                            <button type="submit" style="background: #dc2626; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">🚫 Block User</button>
                        @endif
                    </form>
                </div>
            @endif
        </div>

        <div class="user-activity" style="margin-top: 30px;">
            <h2 style="border-bottom: 1px solid #eee; padding-bottom: 10px;">User Activity</h2>
            <h3>Tickets Posted ({{ $user->posts->count() }})</h3>
            @forelse($user->posts as $post)
                <p><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a> - <small>{{ $post->created_at->diffForHumans() }}</small></p>
            @empty
                <p>This user has not posted any tickets.</p>
            @endforelse
            
            <h3 style="margin-top: 25px;">Comments Posted ({{ $user->comments->count() }})</h3>
            @forelse($user->comments as $comment)
                <p style="padding-left: 15px; border-left: 3px solid #eee;">
                    "{{ Str::limit($comment->content, 50) }}" 
                    on 
                    @if($comment->post)
                        <a href="{{ route('posts.show', $comment->post) }}">{{ $comment->post->title }}</a>
                    @else
                        <em>Deleted Post</em>
                    @endif
                </p>
            @empty
                <p>This user has not posted any comments.</p>
            @endforelse
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
                btn.style.display = 'none'; // Hide edit button while editing
            } else {
                form.classList.add('hidden');
                display.classList.remove('hidden');
                btn.style.display = 'inline-block';
            }
        }
    </script>
</body>
</html>