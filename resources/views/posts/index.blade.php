<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; }
        .create-btn { background: #3490dc; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; }
        .create-btn:hover { background: #2779bd; }
        .back-link { text-decoration: none; color: #666; display: inline-block; margin-bottom: 20px; }
        .post-item { padding: 20px; border-bottom: 1px solid #eee; }
        .post-item:last-child { border-bottom: none; }
        .post-title { margin: 0 0 10px 0; }
        .post-title a { text-decoration: none; color: #3490dc; }
        .post-title a:hover { text-decoration: underline; }
        .post-meta { color: #888; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <a href="{{ route('home') }}" class="back-link">&larr; Back to Home</a>

        <div class="header">
            <h1>All Posts</h1>
            <a href="{{ route('posts.create') }}" class="create-btn">Create New Post</a>
        </div>

        <div class="posts-list">
            @forelse ($posts as $post)
                <div class="post-item">
                    <h2 class="post-title">
                        <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                    </h2>
                    <div class="post-meta">
                        Posted by {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }}
                        | Comments: {{ $post->comments->count() }}
                    </div>
                    <p>{{ Str::limit($post->description, 150) }}</p>
                </div>
            @empty
                <p>No posts found.</p>
            @endforelse
        </div>
    </div>
</body>
</html>