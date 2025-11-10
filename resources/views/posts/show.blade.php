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
        .delete-btn { color: red; background: none; border: none; cursor: pointer; font-size: 0.85em; text-decoration: underline; }
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