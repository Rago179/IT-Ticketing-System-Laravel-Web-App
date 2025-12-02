<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Post - {{ $post->title }}</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f9f9f9; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 20px; }
        label { display: block; font-weight: bold; margin-bottom: 5px; }
        input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
        textarea { min-height: 120px; }
        .submit-btn { background: #3490dc; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; }
        .cancel-btn { background: #64748b; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; margin-right: 10px;}
        .category-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 10px; padding: 10px; border: 1px solid #eee; background: #f8fafc; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Post</h1>

        <form method="POST" action="{{ route('posts.update', $post) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" value="{{ old('title', $post->title) }}" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" required>{{ old('description', $post->description) }}</textarea>
            </div>

            <div class="form-group">
                <label for="image">Change Image (Optional)</label>
                @if($post->image_path)
                    <div style="margin-bottom: 10px;">
                        <img src="{{ asset('storage/' . $post->image_path) }}" alt="Current Image" style="height: 100px; border-radius: 5px;">
                        <div style="font-size: 0.8em; color: #666;">Current Image</div>
                    </div>
                @endif
                <input type="file" id="image" name="image" accept="image/*">
            </div>

            <div class="form-group">
                <label for="priority">Priority</label>
                <input type="number" id="priority" name="priority" min="1" max="4" value="{{ old('priority', $post->priority) }}" required>
            </div>

            <div class="form-group">
                <label>Categories</label>
                <div class="category-grid">
                    @foreach($categories as $category)
                        <label>
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}"
                                @if($post->categories->contains($category->id)) checked @endif
                            >
                            {{ $category->name }}
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div>
                <a href="{{ route('posts.show', $post) }}" class="cancel-btn">Cancel</a>
                <button type="submit" class="submit-btn">Update Post</button>
            </div>
        </form>
    </div>
</body>
</html>