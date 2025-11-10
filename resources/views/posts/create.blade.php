<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        
        /* Header Styles */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 1px solid #eee;}
        .header-title { font-size: 1.5em; font-weight: bold; margin: 0; text-decoration: none; color: #333; }
        .header-controls { display: flex; align-items: center; gap: 15px; }
        .user-info { font-weight: bold; color: #333; }
        .logout-btn { background: none; border: none; color: #666; cursor: pointer; text-decoration: underline; }

        /* Form Styles */
        .back-link { text-decoration: none; color: #3490dc; display: inline-block; margin-bottom: 20px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        textarea { min-height: 120px; }
        .submit-btn { background: #3490dc; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .submit-btn:hover { background: #2779bd; }
        .error { color: #e3342f; font-size: 0.875em; margin-top: 5px; }
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
        
        <h1>Create a New Post</h1>

        <form method="POST" action="{{ route('posts.store') }}">
            @csrf
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title') }}" required autofocus>
                @error('title') <div class="error">{{ $message }}</div> @enderror
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required>{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="priority">Priority (1 = Lowest, 4 = Highest)</label>
                <input type="text" id="priority" name="priority" value="{{ old('priority', 1) }}" required>
                @error('priority') <div class="error">{{ $message }}</div> @enderror
            </div>
            <div>
                <button type="submit" class="submit-btn">Publish Post</button>
            </div>
        </form>
    </div>
</body>
</html>