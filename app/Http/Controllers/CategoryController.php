<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        // Load posts for this category, along with user and category data
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
        // 1. AUTHENTICATION & AUTHORIZATION CHECK
        if (!Auth::check() || Auth::user()->role !== 'admin') {
            abort(403, 'Only admins can create categories.');
        }

        // 2. VALIDATION
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
        ]);

        // 3. CREATE
        Category::create($validated);

        // 4. REDIRECT
        return back()->with('success', 'Category created successfully!');
    }
}