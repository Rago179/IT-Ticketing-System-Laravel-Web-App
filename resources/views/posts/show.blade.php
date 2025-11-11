<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Header Styles */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .header-title { font-size: 1.5em; font-weight: bold; margin: 0; text-decoration: none; color: #333; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }

        /* Page Specific Styles */
        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        .post-title { margin-top: 0; }
        .post-meta { color: #888; font-size: 0.9em; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .post-content { font-size: 1.1em; line-height: 1.6; margin-bottom: 40px; }
        .comments-section { border-top: 2px solid #eee; padding-top: 20px; }
        .comment { background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .comment-header { font-weight: bold; margin-bottom: 5px; display: flex; justify-content: space-between; }
        .comment-date { font-weight: normal; color: #777; font-size: 0.9em; }
        .delete-btn { color: red; backgrou<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .header-title { font-size: 1.5em; font-weight: bold; margin: 0; text-decoration: none; color: #333; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }

        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        .post-title { margin-top: 0; }
        .post-meta { color: #888; font-size: 0.9em; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .post-meta a { color: #3490dc; text-decoration: none; font-weight: bold; }
        .post-meta a:hover { text-decoration: underline; }
        .post-content { font-size: 1.1em; line-height: 1.6; margin-bottom: 40px; }
        .comments-section { border-top: 2px solid #eee; padding-top: 20px; }
        .comment { background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .comment-header { font-weight: bold; margin-bottom: 5px; display: flex; justify-content: space-between; }
        .comment-header a { color: #333; text-decoration: none; }
        .comment-header a:hover { text-decoration: underline; }
        .comment-date { font-weight: normal; color: #777; font-size: 0.9em; }
        .delete-btn { color: red; background: none; border: none; cursor: pointer; font-size: 0.85em; text-decoration: underline; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-height: 80px; margin-bottom: 10px; box-sizing: border-box; }
        .submit-btn { background: #3490dc; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .submit-btn:hover { background: #2779bd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('home') }}" class="header-title">{{ config('app.name', 'IT-Ticket-System') }}</a>
            <div class="header-controls">
                <span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}" style="text-decoration:none; color: #3490dc;">{{ Auth::user()->name }}</a></span>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">&larr; Back to All Posts</a>

        <h1 class="post-title">{{ $post->title }}</h1>

        {{-- START: Admin/IT Controls --}}
        @if(in_array(Auth::user()->role, ['admin', 'it']))
            <div style="margin-bottom: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px; border-left: 5px solid #3490dc;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">IT/Admin Controls</h3>
                <form action="{{ route('posts.updateStatus', $post->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                        <label for="status" style="font-weight: bold; margin-right: 10px;">Update Status:</label>
                        <select name="status" id="status" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="open" {{ $post->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $post->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $post->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        <noscript><button type="submit" style="margin-left: 10px;">Update</button></noscript>
                    </div>
                    @error('status')
                        <div style="color: #dc2626; font-weight: bold; margin-top: 10px; font-size: 0.9em;">
                            {{ $message }}
                        </div>
                    @enderror
                </form>
            </div>
        @endif
        {{-- END: Admin/IT Controls --}}

        <div class="post-meta">
            Priority: <strong>{{ $post->priority }}/4</strong> 
            | Posted by <a href="{{ route('users.show', $post->user) }}"><strong>{{ $post->user->name }}</strong></a> on {{ $post->created_at->format('M d, Y') }}
        </div>
        <div class="post-content">
            {{ $post->description }}
        </div>

        <div class="comments-section">
            <h2>Comments ({{ $post->comments->count() }})</h2>

            @foreach($post->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <span><a href="{{ route('users.show', $comment->user) }}">{{ $comment->user->name }}</a></span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div>{{ $comment->content }}</div>
                    
                    @if(Auth::id() === $comment->user_id)
                        <div style="text-align: right; margin-top: 10px;">
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 40px;">
                <h3>Leave a Comment</h3>
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <textarea name="content" required placeholder="Write something..."></textarea>
                    <button type="submit" class="submit-btn">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>nd: none; border: none; cursor: pointer; font-size: 0.85em; text-decoration: underline; }
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-height: 80px; margin-bottom: 10px; box-sizing: border-box; }
        .submit-btn { background: #3490dc; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .submit-btn:hover { background: #2779bd; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('home') }}" class="header-title">{{ config('app.name', 'My App') }}</a>
            <div class="header-controls">
                <span class="user-info">Hi, {{ Auth::user()->name }}</span>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">&larr; Back to All Posts</a>

        <h1 class="post-title">{{ $post->title }}</h1>
{{-- START: Admin/IT Controls --}}
        @if(Auth::user()->role === 'admin' || Auth::user()->role === 'it')
            <div style="margin-bottom: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px; border-left: 5px solid #3490dc;">
                <h3 style="margin-top: 0; margin-bottom: 10px;">IT/Admin Controls</h3>
                <form action="{{ route('posts.updateStatus', $post->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                        <label for="status" style="font-weight: bold; margin-right: 10px;">Update Status:</label>
                        <select name="status" id="status" onchange="this.form.submit()" style="padding: 8px; border-radius: 4px; border: 1px solid #ccc;">
                            <option value="open" {{ $post->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $post->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $post->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        <noscript><button type="submit" style="margin-left: 10px;">Update</button></noscript>
                    </div>
                    {{-- ERROR MESSAGE DISPLAY --}}
                    @error('status')
                        <div style="color: #dc2626; font-weight: bold; margin-top: 10px; font-size: 0.9em;">
                            {{ $message }}
                        </div>
                    @enderror
                </form>
            </div>
        @endif
        {{-- END: Admin/IT Controls --}}

        <div class="post-meta">
            Priority: <strong>{{ $post->priority }}/4</strong> 
            | Posted by <strong>{{ $post->user->name }}</strong> on {{ $post->created_at->format('M d, Y') }}
        </div>
        <div class="post-content">
            {{ $post->description }}
        </div>

        <div class="comments-section">
            <h2>Comments ({{ $post->comments->count() }})</h2>

            @foreach($post->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <span>{{ $comment->user->name }}</span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div>{{ $comment->content }}</div>
                    
                    @if(Auth::id() === $comment->user_id)
                        <div style="text-align: right; margin-top: 10px;">
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            @endforeach

            <div style="margin-top: 40px;">
                <h3>Leave a Comment</h3>
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <textarea name="content" required placeholder="Write something..."></textarea>
                    <button type="submit" class="submit-btn">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>