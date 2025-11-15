<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // <-- ADD THIS LINE

class CategoryController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // ... (your existing show method) ...
        $posts = $category->posts()
                          ->with(['user', 'categories'])
                          ->latest()
                          ->paginate(10);
                          
        return view('categories.show', compact('category', 'posts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ... (your existing store method) ...
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can create categories.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        Category::create($validated);

        return back()->with('success', 'Category created successfully!');
    }

    /**
     * Remove the specified resource from storage.
     * (NEW METHOD)
     */
    public function destroy(Category $category)
    {
        // 1. Authorize: Only admin can delete
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // 2. Safeguard: Prevent deleting the "Other" category
        if (strtolower($category->name) === 'other') {
            return back()->withErrors(['name' => 'The default "Other" category cannot be deleted.']);
        }

        // 3. Use a transaction to ensure data integrity
        DB::transaction(function () use ($category) {
            
            // 4. Find or create the "Other" category
            $otherCategory = Category::firstOrCreate(['name' => 'Other']);

            // 5. Find all posts attached to the category being deleted
            // We must eager load 'categories' to count them efficiently
            foreach ($category->posts()->with('categories')->get() as $post) {
                
                // 6. Check if this is the post's *only* category
                if ($post->categories->count() === 1) {
                    // If yes, attach it to the "Other" category
                    $post->categories()->attach($otherCategory->id);
                }
                // If the post has > 1 category, we do nothing.
                // It will just be detached from the deleted category below.
            }

            // 7. Detach all posts from the category we are about to delete
            $category->posts()->detach();

            // 8. Delete the category itself
            $category->delete();
        });

        // 9. Redirect with success
        return redirect()->route('home')->with('success', 'Category deleted. Orphaned posts moved to "Other".');
    }
}