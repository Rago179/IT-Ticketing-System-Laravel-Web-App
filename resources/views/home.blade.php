<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - {{ config('app.name', 'My App') }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .header h1 { margin: 0; }
        .logout-btn { background: #e53e3e; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; border: none; cursor: pointer; }
        .create-post-btn { background: #3490dc; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; margin-right: 10px; }
        .post-list { margin-top: 20px; }
        .post { border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 10px; transition: background-color 0.2s; }
        .post:hover { background-color: #f8f9fa; }
        .post h2 { margin-top: 0; }
        .post-meta { font-size: 0.85rem; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>{{ config('app.name', 'My App') }}</h1>
            <div>
                <a href="{{ route('posts.create') }}" class="create-post-btn">Create New Post</a>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <div class="post-list">
            <h2>All Posts</h2>

            @forelse ($posts as $post)
                <div class="post">
                    <h2>
                        <a href="{{ route('posts.show', $post) }}" style="text-decoration: none; color: #3490dc;">
                            {{ $post->title }}
                        </a>
                    </h2>
                    <p class="post-meta">By {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }} | Comments: {{ $post->comments->count() }}</p>
                    <p>{{ Str::limit($post->description, 150) }}</p>
                </div>
            @empty
                <p>No posts found.</p>
            @endforelse
        </div>
    </div>
</body>
</html>