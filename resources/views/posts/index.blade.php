<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - {{ config('app.name', 'IT-Ticket-System') }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 900px; margin: 0 auto; background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }

        .top-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 20px;
        }
        .site-branding h1 { margin: 0; color: #1e293b; font-size: 1.8em; }
        
        .user-controls { display: flex; align-items: center; gap: 20px; font-size: 0.95em; }
        .user-name { font-weight: bold; color: #333; text-decoration: none; }
        .user-name:hover { text-decoration: underline; color: #3490dc; }
        
        .logout-btn { 
            background: none; border: none; color: #64748b; cursor: pointer; 
            font-weight: normal; padding: 0; text-decoration: underline; 
        }
        .logout-btn:hover { color: #ef4444; }

        .action-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 40px;
        }
        .btn {
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 0.9em;
            display: inline-block;
            transition: background-color 0.2s;
        }
        .btn-primary { background-color: #3490dc; color: white; }
        .btn-primary:hover { background-color: #2779bd; }
        
        .btn-admin { background-color: #4f46e5; color: white; }
        .btn-admin:hover { background-color: #4338ca; }
        
        .btn-it { background-color: #ea580c; color: white; }
        .btn-it:hover { background-color: #c2410c; }

        /* --- EXISTING STYLES --- */
        .section-title {
            margin-top: 40px;
            margin-bottom: 20px;
            font-size: 1.3em;
            color: #333;
            border-bottom: 2px solid #f1f5f9;
            padding-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .pinned-post {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px;
            background: #f8fafc;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 4px solid #3490dc;
        }
        .pinned-post-title a { text-decoration: none; color: #3490dc; font-weight: bold; font-size: 1.1em; }
        .pinned-post-title a:hover { text-decoration: underline; }
        .pinned-post-meta { font-size: 0.85em; color: #64748b; }

        .category-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .category-box {
            display: block; padding: 25px; background: #f8fafc;
            border-radius: 8px; text-decoration: none; color: #333;
            border: 1px solid #eee; transition: all 0.2s ease;
        }
        .category-box:hover { transform: translateY(-3px); box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-color: #3490dc; }
        .category-box-title { font-size: 1.25em; font-weight: bold; color: #3490dc; margin: 0; }
        .category-box-count { font-size: 0.9em; color: #64748b; margin-top: 5px; }

        /* Admin Create Form */
        .admin-create-category { background: #f1f5f9; padding: 20px; border-radius: 8px; margin-top: 30px; }
        .admin-create-category h3 { margin-top: 0; margin-bottom: 15px; }
        .admin-create-category form { display: flex; gap: 10px; }
        .admin-create-category input[type="text"] { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .admin-create-category .form-group { flex-grow: 1; }
        .admin-create-category button { border: none; cursor: pointer; background-color: #16a34a; color: white; padding: 10px 15px; border-radius: 5px; font-weight: bold; }
    </style>
</head>
<body>

    {{-- Alerts --}}
    @if (session('success'))
        {{-- (Keep your existing success alert code here) --}}
        <div id="success-alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background-color: #dcfce7; color: #166534; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); font-weight: bold;">
            {{ session('success') }}
        </div>
        <script>setTimeout(() => document.getElementById('success-alert')?.remove(), 3000);</script>
    @endif
    
    @if (session('error'))
        <div id="error-alert" style="position: fixed; top: 80px; left: 50%; transform: translateX(-50%); z-index: 9999; background-color: #fee2e2; color: #dc2626; padding: 12px 24px; border-radius: 8px; border: 1px solid #ef4444; font-weight: bold;">
            🚫 {{ session('error') }}
        </div>
        <script>setTimeout(() => document.getElementById('error-alert')?.remove(), 4000);</script>
    @endif


    <div class="container">
        
        {{-- NEW HEADER LAYOUT --}}
        <div class="top-nav">
            <div class="site-branding">
                <h1>Dashboard</h1>
            </div>
            <div class="user-controls">
                <span>
                    Hi, <a href="{{ route('users.show', Auth::user()) }}" class="user-name">{{ Auth::user()->name }}</a>
                </span>
                <span style="color: #cbd5e1;">|</span>
                <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        {{-- ACTION BUTTONS ROW --}}
        <div class="action-bar">
            <a href="{{ route('posts.create') }}" class="btn btn-primary">
                + Create New Post
            </a>

            @if(Auth::user()->role === 'admin')
                <a href="{{ route('users.index') }}" class="btn btn-admin">
                    Manage Users
                </a>
            @endif

            @if(in_array(Auth::user()->role, ['it', 'admin']))
                <a href="{{ route('it.dashboard') }}" class="btn btn-it">
                    IT Dashboard
                </a>
            @endif
        </div>


        {{-- PINNED POSTS SECTION --}}
        <div>
            <h2 class="section-title"><span>📌</span> Pinned Posts</h2>
            @forelse ($pinnedPosts as $post)
                <div class="pinned-post">
                    <div>
                        <div class="pinned-post-title"><a href="{{ route('posts.show', $post) }}">{{ $post->title }}</a></div>
                        <div class="pinned-post-meta">
                            By {{ $post->user->name }} | {{ $post->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <p style="color: #64748b; font-style: italic;">No pinned posts.</p>
            @endforelse
        </div>


        {{-- CATEGORIES SECTION --}}
        <div>
            <h2 class="section-title"><span>📂</span> Browse by Category</h2>
            
            <div class="category-grid">
                @forelse ($categories as $category)
                    <div style="position: relative;">
                        {{-- The Main Category Link --}}
                        <a href="{{ route('categories.show', $category) }}" class="category-box" style="height: 100%; box-sizing: border-box;">
                            <h3 class="category-box-title">{{ $category->name }}</h3>
                            <div class="category-box-count">{{ $category->posts_count }} {{ Str::plural('post', $category->posts_count) }}</div>
                        </a>

                        {{-- Admin Delete Button --}}
                        @if(Auth::user()->role === 'admin' && $category->name !== 'Other')
                            <form action="{{ route('categories.destroy', $category) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure? All posts in this category will be moved to \'Other\'.');"
                                  style="position: absolute; top: 10px; right: 10px;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #fee2e2; color: #dc2626; border: 1px solid #ef4444; border-radius: 50%; width: 25px; height: 25px; cursor: pointer; font-weight: bold; line-height: 1; padding: 0;" title="Delete Category">
                                    &times;
                                </button>
                            </form>
                        @endif
                    </div>
                @empty
                    <p>No categories have been created yet.</p>
                @endforelse
            </div>
        </div>


        {{-- ADMIN: CREATE CATEGORY (Bottom of page) --}}
        @if(Auth::user()->role === 'admin')
            <div class="admin-create-category">
                <h3>Admin: Create New Category</h3>
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    <div class="form-group">
                        <input type="text" name="name" placeholder="New category name" required value="{{ old('name') }}">
                        @error('name')
                            <div style="color: #dc2626; margin-top: 5px;">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit">Create</button>
                </form>
            </div>
        @endif

    </div>
</body>
</html>