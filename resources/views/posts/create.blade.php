<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post</title>
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
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea, select, input[type="file"] {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;
        }
        textarea { min-height: 120px; }
        
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            background: #f8fafc;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #eee;
        }
        .category-grid label {
            font-weight: normal;
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }
        .category-grid input[type="checkbox"] {
            width: auto;
        }

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
                <span class="user-info">Hi, <a href="{{ route('users.show', Auth::user()) }}">{{ Auth::user()->name }}</a></span>
                 <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                    @csrf
                    <button type="submit" class="logout-btn">Log Out</button>
                </form>
            </div>
        </div>

        <a href="{{ route('home') }}" class="back-link">&larr; Back to Dashboard</a>
        
        <h1>Create a New Post</h1>

        {{-- Added enctype for file upload support --}}
        <form method="POST" action="{{ route('posts.store') }}" enctype="multipart/form-data">
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

            {{-- New Image Upload Field --}}
            <div class="form-group">
                <label for="image">Attach Image (Optional)</label>
                <input type="file" id="image" name="image" accept="image/*">
                @error('image') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="priority">Priority <span style="font-weight: normal; font-size: 0.9em; color: #666;">(1 = Lowest, 4 = Highest)</span></label>
                <input type="number" id="priority" name="priority" min="1" max="4" value="{{ old('priority', 1) }}" required>
                @error('priority') <div class="error">{{ $message }}</div> @enderror
            </div>

            <!-- Category Selector -->
            <div class="form-group">
                <label>Categories (Optional)</label>
                <div class="category-grid">
                    @forelse($categories as $category)
                        <div>
                            <label for="category-{{ $category->id }}">
                                <input type="checkbox"
                                       name="categories[]"
                                       id="category-{{ $category->id }}"
                                       value="{{ $category->id }}"
                                       @if(is_array(old('categories')) && in_array($category->id, old('categories'))) checked @endif
                                >
                                {{ $category->name }}
                            </label>
                        </div>
                    @empty
                        <p style="color: #666; font-style: italic;">No categories created. Admin can create them from the dashboard.</p>
                    @endforelse
                </div>
                @error('categories.*') <div class="error">{{ $message }}</div> @enderror
            </div>
            
            <div>
                <button type="submit" class="submit-btn">Publish Post</button>
            </div>
        </form>
    </div>
</body>
</html>