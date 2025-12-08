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
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        if ($category->name === 'Other') {
            return back()->with('error', 'You cannot delete the default "Other" category.');
        }

        $otherCategory = Category::firstOrCreate(['name' => 'Other']);

        $category->posts()->each(function($post) use ($otherCategory) {
            if ($post->categories()->count() === 1) {
                $post->categories()->syncWithoutDetaching([$otherCategory->id]);
            }
        });

        // Database cascade will handle removing the relationships for posts 
        // that had multiple categories.
        $category->delete();

        return back()->with('success', "Category '{$category->name}' deleted. Orphaned posts moved to 'Other'.");
    }
}