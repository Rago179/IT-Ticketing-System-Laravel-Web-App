<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        $posts = $category->posts()
                          ->with(['user', 'categories'])
                          ->latest()
                          ->paginate(10);
                          
        return view('categories.show', compact('category', 'posts'));
    }

    public function store(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can create categories.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return back()->with('success', 'Category created successfully!');
    }

    public function destroy(Category $category)
    {
        // 1. Authorization
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // 2. Prevent deleting 'Other'
        if ($category->name === 'Other') {
            return back()->with('error', 'You cannot delete the default "Other" category.');
        }

        // 3. Find or Create 'Other' to act as the safety net
        $otherCategory = Category::firstOrCreate(['name' => 'Other']);

        // 4. Smart Move: Only move posts that would otherwise be orphaned
        $category->posts()->each(function($post) use ($otherCategory) {
            // If this post has ONLY 1 category (which is the one we are deleting),
            // then we must assign it to 'Other' so it doesn't become category-less.
            if ($post->categories()->count() === 1) {
                $post->categories()->syncWithoutDetaching([$otherCategory->id]);
            }
        });

        // 5. Delete
        // Database cascade will handle removing the relationships for posts 
        // that had multiple categories.
        $category->delete();

        return back()->with('success', "Category '{$category->name}' deleted. Orphaned posts moved to 'Other'.");
    }
}