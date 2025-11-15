<!DOCTYPE html>

<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Dashboard - {{ config('app.name', 'IT-Ticket-System') }}</title>

<style>

body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }

.container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }


/* Header */

.header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }

.header h1 { margin: 0; }

.header-controls { display: flex; align-items: center; gap: 15px; }

.user-info { font-weight: bold; color: #333; }

.user-info a { text-decoration:none; color: #3490dc; }

.create-btn { background: #3490dc; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; }

.logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }



/* Section Title */

.section-title {

margin-top: 30px;

margin-bottom: 15px;

font-size: 1.3em;

color: #333;

border-bottom: 2px solid #eee;

padding-bottom: 10px;

display: flex;

align-items: center;

gap: 10px;

}



/* Pinned Posts */

.pinned-post {

display: flex;

justify-content: space-between;

align-items: center;

padding: 15px;

background: #f1f5f9;

border-radius: 8px;

margin-bottom: 10px;

border-left: 4px solid #3490dc;

}

.pinned-post-title a {

text-decoration: none;

color: #3490dc;

font-weight: bold;

font-size: 1.1em;

}

.pinned-post-title a:hover { text-decoration: underline; }

.pinned-post-meta { font-size: 0.85em; color: #64748b; }



/* Admin Create Category */

.admin-create-category {

background: #f1f5f9;

padding: 20px;

border-radius: 8px;

margin-top: 30px;

}

.admin-create-category h3 { margin-top: 0; margin-bottom: 15px; }

.admin-create-category form { display: flex; gap: 10px; }

.admin-create-category input[type="text"] {

width: 100%;

padding: 10px;

border: 1px solid #ccc;

border-radius: 5px;

box-sizing: border-box;

}

.admin-create-category .form-group { flex-grow: 1; }

.admin-create-category .error { color: #dc2626; font-size: 0.9em; margin-top: 5px; }

.admin-create-category button { border: none; cursor: pointer; background-color: #16a34a !important; }



/* Category Grid */

.category-grid {

display: grid;

grid-template-columns: 1fr 1fr; /* 2 columns */

gap: 15px;

}

.category-box {

display: block;

padding: 25px;

background: #f8fafc;

border-radius: 8px;

text-decoration: none;

color: #333;

border: 1px solid #eee;

transition: all 0.2s ease;

}

.category-box:hover {

transform: translateY(-3px);

box-shadow: 0 4px 10px rgba(0,0,0,0.05);

border-color: #3490dc;

}

.category-box-title {

font-size: 1.25em;

font-weight: bold;

color: #3490dc;

margin: 0;

}

.category-box-count {

font-size: 0.9em;

color: #64748b;

margin-top: 5px;

}

</style>

</head>

<body>

{{-- Success Notification --}}

@if (session('success'))

<div id="success-alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; background-color: #dcfce7; color: #166534; padding: 12px 24px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); display: flex; align-items: center; justify-content: space-between; min-width: 300px; font-weight: bold;">

<span>{{ session('success') }}</span>

<button onclick="closeAlert()" style="background: none; border: none; color: #15803d; font-size: 20px; cursor: pointer; padding: 0 0 0 15px; line-height: 1;">&times;</button>

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



<div class="container">

<div class="header">

<h1>Dashboard</h1>

<div class="header-controls">

<span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}">{{ Auth::user()->name }}</a></span>

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

<p>No pinned posts. Admin can pin important posts from the post page.</p>

@endforelse

</div>



{{-- Admin Create Category --}}

@if(Auth::user()->role === 'admin')

<div class="admin-create-category">

<h3>Admin: Create New Category</h3>

<form action="{{ route('categories.store') }}" method="POST">

@csrf

<div class="form-group">

<input type="text" name="name" placeholder="New category name" required value="{{ old('name') }}">

@error('name')

<div class="error">{{ $message }}</div>

@enderror

</div>

<button type="submit" class="create-btn">Create</button>

</form>

</div>

@endif



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


</div>

</body>

</html>