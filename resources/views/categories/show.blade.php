<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Posts in {{ $category->name }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .user-info a { text-decoration:none; color: #3490dc; }
        .create-btn { background: #3490dc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }
        .post-item { padding: 20px; border-bottom: 1px solid #eee; position: relative;}
        .post-item:last-child { border-bottom: none; }
        .status-badge { position: absolute; top: 20px; right: 20px; padding: 6px 12px; border-radius: 20px;
            font-size: 0.75em; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
            color: white;
        }
        .status-open { background-color: #16a34a; }
        .status-ongoing { background-color: #ea580c; }
        .status-resolved { background-color: #dc2626; }
        .post-title { margin: 0 0 10px 0; }
        .post-title a { text-decoration: none; color: #3490dc; }
        .post-title a:hover { text-decoration: underline; }
        .post-meta { color: #888; font-size: 0.9em; }
        .post-meta a { color: #3490dc; text-decoration: none; font-weight: bold; }
        .post-meta a:hover { text-decoration: underline; }
        .pagination-wrapper { margin-top: 30px; }
        .pagination-wrapper nav { display: flex; justify-content: space-between; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Posts in: {{ $category->name }}</h1>
            <div class="header-controls">
                <span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}">{{ Auth::user()->name }}</a></span>
                <a href="{{ route('home') }}" class="create-btn" style="background-color: #64748b;">&larr; All Categories</a>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <!-- List of Posts -->
        <div class="posts-list">
            @forelse ($posts as $post)
                <div class="post-item">
                    @php
                        $statusClass = match($post->status) { 'in_progress' => 'status-ongoing', 'resolved' => 'status-resolved', default => 'status-open' };
                        $statusText = match($post->status) { 'in_progress' => 'Ongoing', 'resolved' => 'Resolved', default => 'Open' };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                    <h2 class="post-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></h2>
                    <div class="post-meta">
                        <span style="color: #333; font-weight: bold;">Priority: {{ $post->priority }}/4</span>
                        | Posted by <a href="{{ route('users.show', $post->user) }}">{{ $post->user->name }}</a>
                        | Comments: {{ $post->comments->count() }}
                    </div>
                    <p>{{ Str::limit($post->description, 150) }}</p>
                </div>
            @empty
                <p>No posts found in this category.</p>
            @endforelse
        </div>

        <div class="pagination-wrapper">
            {{ $posts->links() }}
        </div>
    </div>
</body>
</html>