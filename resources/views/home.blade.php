<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Use the app name from your config -->
    <title>Home - {{ config('app.name', 'My App') }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background-color: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; }
        .header h1 { margin: 0; }
        .logout-btn { background: #e53e3e; color: white; padding: 8px 12px; border-radius: 5px; text-decoration: none; font-size: 0.9rem; border: none; cursor: pointer; }
        .post-list { margin-top: 20px; }
        .post { border: 1px solid #ddd; padding: 15px; border-radius: 5px; margin-bottom: 10px; }
        .post h2 { margin-top: 0; }
        .post-meta { font-size: 0.85rem; color: #555; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <!-- 1. Use App Name -->
            <h1>{{ config('app.name', 'My App') }}</h1>
            
            <!-- Simple Logout Button -->
            <!-- Note: Logout must be a POST request, so it needs a mini-form -->
            <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                @csrf
                <button type="submit" class="logout-btn">Log Out</button>
            </form>
        </div>

        <div class="post-list">
            <h2>All Posts</h2>

            <!-- 4. Show all posts -->
            @forelse ($posts as $post)
                <div class="post">
                    <h2>{{ $post->title }}</h2>
                    <p class="post-meta">By {{ $post->user->name }} on {{ $post->created_at->format('M d, Y') }}</p>
                    <p>{{ $post->description }}</p>
                </div>
            @empty
                <p>No posts found.</p>
            @endforelse
        </div>
    </div>
</body>
</html>