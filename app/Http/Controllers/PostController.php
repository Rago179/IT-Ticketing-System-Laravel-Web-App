<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display the dashboard.
     * LOGIC: Fetches Pinned posts (sorted by latest) and Categories (sorted by name).
     */
    public function index(Request $request)
    {
        // 1. Fetch ALL pinned posts (Removed ->take(4))
        $pinnedPosts = Post::with(['user', 'categories'])
                            ->where('is_pinned', true)
                            ->latest()
                            ->get();

        // 2. Fetch ALL categories
        $categories = Category::withCount('posts')
                            ->orderBy('name')
                            ->get();

        return view('posts.index', compact('pinnedPosts', 'categories'));
    }
    public function create()
    {
        // Logic: Fetch all categories so the view can just loop through them
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer|between:1,4',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id'
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        // LOGIC: Handle "Other" default if no category selected
        // This logic belongs here, not in the view or a dirty database trigger.
        if ($request->has('categories') && !empty($request->categories)) {
            $post->categories()->attach($request->categories);
        } else {
            // Find "Other" category
            $otherCategory = Category::where('name', 'Other')->first();
            if ($otherCategory) {
                $post->categories()->attach($otherCategory->id);
            }
        }

        return redirect()->route('home')->with('success', 'Post published successfully!');
    }

    public function show(Post $post)
    {
        $post->load('categories', 'comments.user');
        
        // LOGIC: We must fetch all categories here to pass to the 'Edit Categories' modal/form
        // The View should not make calls like Category::all()
        $categories = Category::all();

        return view('posts.show', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     * LOGIC: Handle the Admin/IT Category update.
     */
    public function update(Request $request, Post $post)
    {
        // 1. Authorization Logic
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        // 2. Validation Logic
        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        // 3. Business Logic: Sync categories (replaces old selection with new)
        $post->categories()->sync($request->categories);

        return back()->with('success', 'Post categories updated successfully.');
    }

    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        $post->delete();
        return redirect()->route('home')->with('success', 'Post deleted.');
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved'
        ]);

        $post = Post::findOrFail($id);

        if (!in_array(Auth::user()->role, ['it','admin'])) {
            abort(403, 'Unauthorized');
        }

        // LOGIC: Prevent resolving without comments
        if ($request->status === 'resolved') {
            $hasCommented = $post->comments()
                                 ->where('user_id', Auth::id())
                                 ->exists();

            if (!$hasCommented) {
                return back()->withErrors(['status' => 'You must add a comment before resolving this ticket.']);
            }
        }

        $post->update(['status' => $request->status]);

        return back()->with('success', 'Status updated!');
    }

    public function pin(Post $post)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized Access');
        }

        $post->update([
            'is_pinned' => !$post->is_pinned
        ]);

        $message = $post->is_pinned ? 'Post pinned successfully.' : 'Post unpinned successfully.';

        return back()->with('success', $message);
    }

    public function itDashboard(Request $request)
    {
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        $query = Post::with('user', 'assignedTo');

        // LOGIC: Filtering based on Request
        if ($request->filled('assigned_to_me')) {
            $query->where('assigned_to_user_id', Auth::id());
        }

        // LOGIC: Sorting based on Request
        // This keeps the View clean. The view just sends ?sort=priority
        if ($request->get('sort') === 'priority') {
            $query->orderBy('priority', 'desc');
        } else {
            $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('it-dashboard', compact('posts'));
    }

    public function assign(Post $post)
    {
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        $post->assigned_to_user_id = Auth::id();
        $post->status = 'in_progress';
        $post->save();

        return back()->with('success', 'Ticket assigned to you.');
    }
}