<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts - {{ config('app.name', 'IT-Ticket-System') }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { margin: 0; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .create-btn { background: #3490dc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }
        .post-item { padding: 20px; border-bottom: 1px solid #eee; position: relative;}
        .post-item:last-child { border-bottom: none; }
        .status-badge { position: absolute; top: 20px; right: 20px; padding: 6px 12px; border-radius: 20px;
            font-size: 0.75em; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px;
            color: white; /* Set text color to white for all badges */
        }

        .status-open { background-color: #3479f8ff; } /* Solid Green */
        .status-ongoing { background-color: #fc7f3cff; } /* Solid Orange */
        .status-resolved { background-color: #00a344ff; } /* Solid Red */
        
        .post-title { margin: 0 0 10px 0; }
        .post-title a { text-decoration: none; color: #3490dc; }
        .post-title a:hover { text-decoration: underline; }
        .post-meta { color: #888; font-size: 0.9em; }
        .post-meta a { color: #3490dc; text-decoration: none; font-weight: bold; }
        .post-meta a:hover { text-decoration: underline; }
        
        /* Pagination Styles */
        .pagination-wrapper { margin-top: 30px; }
        .pagination-wrapper nav > div:first-child { display: none; }
        .pagination-wrapper nav > div:last-child { display: flex; justify-content: space-between; align-items: center; }
        .pagination-wrapper span[aria-current="page"] > span { background-color: #3490dc; color: white; border-color: #3490dc; }
        .pagination-wrapper a, .pagination-wrapper span {
            display: inline-block; padding: 8px 12px; margin: 0 2px; border: 1px solid #ddd;
            border-radius: 4px; text-decoration: none; color: #333; font-size: 0.9em;
        }
        .pagination-wrapper a:hover { background-color: #f1f1f1; }
        .pagination-wrapper svg { width: 20px; height: 20px; vertical-align: middle; } 
    </style>
</head>
<body>
    {{-- START: Success Notification --}}
    @if (session('success'))
        <div id="success-alert" style="
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 9999;
            background-color: #dcfce7;
            color: #166534;
            padding: 12px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
            justify-content: space-between;
            min-width: 300px;
            font-weight: bold;
        ">
            <span>{{ session('success') }}</span>
            <button onclick="closeAlert()" style="
                background: none;
                border: none;
                color: #15803d;
                font-size: 20px;
                cursor: pointer;
                padding: 0 0 0 15px;
                line-height: 1;
            ">&times;</button>
        </div>

        <script>
            function closeAlert() {
                const alert = document.getElementById('success-alert');
                if (alert) {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }
            }
            setTimeout(closeAlert, 3000);
        </script>
    @endif
    {{-- END: Success Notification --}}
    <div class="container">
        <div class="header">
            <h1>All Posts</h1>
            <div class="header-controls">
                <span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}" style="text-decoration:none; color: #3490dc;">{{ Auth::user()->name }}</a></span>
                
                @if(in_array(Auth::user()->role, ['it', 'admin']))
                    <a href="{{ route('it.dashboard') }}" class="create-btn" style="background-color: #ea580c;">IT Dashboard</a>
                @endif

                <a href="{{ route('posts.create') }}" class="create-btn">Create New Post</a>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>
          <!-- Filter Bar -->
        <div style="margin-bottom: 20px; padding: 15px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; gap: 15px;">
            <strong style="color: #555;">Filter by:</strong>
            
            <form method="GET" action="{{ route('home') }}" style="margin: 0;">
                <select name="category" onchange="this.form.submit()" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 5px; cursor: pointer;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </form>

            @if(request()->filled('category'))
                <a href="{{ route('home') }}" style="text-decoration: none; color: #dc2626; font-size: 0.9em;">
                    &times; Clear Filter
                </a>
            @endif
        </div>
        <div class="posts-list">
            @forelse ($posts as $post)
                <div class="post-item">
                    {{-- STATUS BADGE LOGIC START --}}
                    @php
                        $statusClass = match($post->status) {
                            'in_progress' => 'status-ongoing',
                            'resolved' => 'status-resolved',
                            default => 'status-open',
                        };
                        
                        $statusText = match($post->status) {
                            'in_progress' => 'Ongoing',
                            'resolved' => 'Resolved',
                            default => 'Open',
                        };
                    @endphp
                    <span class="status-badge {{ $statusClass }}">
                        {{ $statusText }}
                    </span>
                    {{-- STATUS BADGE LOGIC END --}}

                    <h2 class="post-title">
                        <a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a>
                    </h2>

                    <div style="margin-bottom: 10px;">
                        @foreach($post->categories as $category)
                            <span style="background: #e2e8f0; color: #475569; padding: 3px 8px; border-radius: 12px; font-size: 0.8em; margin-right: 5px;">
                                {{ $category->name }}
                            </span>
                        @endforeach
                    </div>

                    <div class="post-meta">
                        <span style="color: #333; font-weight: bold;">Priority: {{ $post->priority }}/4</span>
                        | Posted by <a href="{{ route('users.show', $post->user) }}">{{ $post->user->name }}</a> on {{ $post->created_at->format('M d, Y') }}
                        | Comments: {{ $post->comments->count() }}
                    </div>
                    <p>{{ Str::limit($post->description, 150) }}</p>
                </div>
            @empty
                <p>No posts found.</p>
            @endforelse
        </div>

        <!-- PAGINATION LINKS -->
        <div class="pagination-wrapper">
            {{ $posts->links() }}
        </div>
    </div>
</body>
</html>