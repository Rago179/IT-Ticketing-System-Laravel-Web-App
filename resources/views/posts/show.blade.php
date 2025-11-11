<!DOCTYPE html>
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
        .user-info a { text-decoration:none; color: #3490dc; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }

        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        .post-title { margin-top: 0; }
        .post-meta { color: #888; font-size: 0.9em; margin-bottom: 20px; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .post-meta a { color: #3490dc; text-decoration: none; font-weight: bold; }
        .post-meta a:hover { text-decoration: underline; }
        .post-content { font-size: 1.1em; line-height: 1.6; margin-bottom: 40px; white-space: pre-wrap; }
        
        .category-tags { margin-top: 10px; margin-bottom: 20px; }
        .category-tag {
            background: #e2e8f0; color: #475569; padding: 4px 10px; border-radius: 12px;
            font-size: 0.85em; margin-right: 5px; font-weight: 500;
        }

        .comments-section { border-top: 2px solid #eee; padding-top: 20px; }
        .comment { background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .comment-header { font-weight: bold; margin-bottom: 5px; display: flex; justify-content: space-between; }
        .comment-header a { color: #333; text-decoration: none; }
        .comment-header a:hover { text-decoration: underline; }
        .comment-date { font-weight: normal; color: #777; font-size: 0.9em; }
        .comment-body { white-space: pre-wrap; }
        .delete-btn { color: red; background: none; border: none; cursor: pointer; font-size: 0.85em; text-decoration: underline; }
        
        textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; min-height: 80px; margin-bottom: 10px; box-sizing: border-box; }
        .submit-btn { background: #3490dc; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .submit-btn:hover { background: #2779bd; }
        
        .admin-controls { margin-bottom: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px; border-left: 5px solid #3490dc; }
        .admin-controls h3 { margin-top: 0; margin-bottom: 10px; }
        .admin-controls form { margin-bottom: 10px; }
        .admin-controls form:last-child { margin-bottom: 0; }
        .admin-controls label { font-weight: bold; margin-right: 10px; }
        .admin-controls select { padding: 8px; border-radius: 4px; border: 1px solid #ccc; }
        .admin-controls button { border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; color: white; }
        .admin-controls .pin-btn { background: #64748b; }
        .admin-controls .unpin-btn { background: #f59e0b; }
        .admin-error { color: #dc2626; font-weight: bold; margin-top: 10px; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <a href="{{ route('home') }}" class="header-title">{{ config('app.name', 'IT-Ticket-System') }}</a>
            <div class="header-controls">
                <span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}">{{ Auth::user()->name }}</a></span>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">&larr; Back to Dashboard</a>

        <h1 class="post-title">{{ $post->title }}</h1>

        {{-- START: Admin/IT Controls --}}
        @if(in_array(Auth::user()->role, ['admin', 'it']))
            <div class="admin-controls">
                <h3>IT/Admin Controls</h3>
                
                {{-- Status Update Form --}}
                <form action="{{ route('posts.updateStatus', $post->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <div style="display: flex; align-items: center; flex-wrap: wrap;">
                        <label for="status">Update Status:</label>
                        <select name="status" id="status" onchange="this.form.submit()">
                            <option value="open" {{ $post->status == 'open' ? 'selected' : '' }}>Open</option>
                            <option value="in_progress" {{ $post->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="resolved" {{ $post->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        </select>
                        <noscript><button type="submit" style="margin-left: 10px;">Update</button></noscript>
                    </div>
                    @error('status') <div class="admin-error">{{ $message }}</div> @enderror
                </form>
                
                {{-- Pin Post Form (Admin Only) --}}
                @if(Auth::user()->role === 'admin')
                <form action="{{ route('posts.pin', $post) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    @if($post->is_pinned)
                        <button type="submit" class="unpin-btn">📌 Unpin Post</button>
                    @else
                        <button type="submit" class="pin-btn">📌 Pin Post</button>
                    @endif
                </form>
                @endif
            </div>
        @endif
        {{-- END: Admin/IT Controls --}}

        <div class="post-meta">
            Priority: <strong>{{ $post->priority }}/4</strong> 
            | Posted by <a href="{{ route('users.show', $post->user) }}"><strong>{{ $post->user->name }}</strong></a> on {{ $post->created_at->format('M d, Y') }}
        </div>
        
        <div class="category-tags">
            @foreach($post->categories as $category)
                <span class="category-tag">{{ $category->name }}</span>
            @endforeach
        </div>

        <div class="post-content">
            {!! nl2br(e($post->description)) !!}
        </div>

        <div class="comments-section">
            <h2>Comments ({{ $post->comments->count() }})</h2>

            @forelse($post->comments as $comment)
                <div class="comment">
                    <div class="comment-header">
                        <span><a href="{{ route('users.show', $comment->user) }}">{{ $comment->user->name }}</a></span>
                        <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <div class="comment-body">{!! nl2br(e($comment->content)) !!}</div>
                    
                    @if(Auth::id() === $comment->user_id || Auth::user()->role === 'admin')
                        <div style="text-align: right; margin-top: 10px;">
                            <form method="POST" action="{{ route('comments.destroy', $comment) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </div>
                    @endif
                </div>
            @empty
                <p>No comments yet.</p>
            @endforelse

            <div style="margin-top: 40px;">
                <h3>Leave a Comment</h3>
                <form method="POST" action="{{ route('comments.store') }}">
                    @csrf
                    <input type="hidden" name="post_id" value="{{ $post->id }}">
                    <textarea name="content" required placeholder="Write something...">{{ old('content') }}</textarea>
                    @error('content') <div style="color: #dc2626; font-size: 0.9em; margin-bottom: 10px;">{{ $message }}</div> @enderror
                    <button type="submit" class="submit-btn">Post Comment</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>