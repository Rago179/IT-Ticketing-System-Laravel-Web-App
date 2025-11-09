<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        // 1. AUTHENTICATION & AUTHORIZATION CHECK
        // Ensure the user is logged in AND has the 'admin' role
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