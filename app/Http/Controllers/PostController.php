<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category; // <-- Ensure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display the new homepage (pinned posts & categories).
     */
    public function index(Request $request)
    {
        // 1. Fetch pinned posts
        $pinnedPosts = Post::with(['user', 'categories'])
                            ->where('is_pinned', true)
                            ->latest()
                            ->take(4) // Get max 4 pinned posts
                            ->get();

        // 2. Fetch categories, counting how many posts are in each
        $categories = Category::withCount('posts')->get();

        return view('posts.index', compact('pinnedPosts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Pass categories to the create view
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer|between:1,4',
            'categories' => 'nullable|array', // Validate categories
            'categories.*' => 'integer|exists:categories,id' // Validate each item in array
        ], [
            'priority.integer' => 'Only numbers are allowed for priority.',
            'priority.between' => 'Priority must be a number between 1 and 4.',
        ]);

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        // Attach the selected categories
        if ($request->has('categories')) {
            $post->categories()->attach($request->categories);
        }

        return redirect()->route('home')->with('success', 'Post published successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        // Eager load categories and comments
        $post->load('categories', 'comments.user');
        return view('posts.show', compact('post'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        $post->delete();
        return redirect()->route('home')->with('success', 'Post deleted.');
    }

    /**
     * Update the status of a post.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved'
        ]);

        $post = Post::findOrFail($id);

        if (!in_array(Auth::user()->role, ['it','admin'])) {
            abort(403, 'Unauthorized');
        }

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

    /**
     * Toggle the pinned status of a post (Admin Only).
     */
    public function pin(Post $post)
    {
        // 1. Authorize: Only 'admin' can pin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized Access');
        }

        // 2. Toggle the is_pinned status
        $post->update([
            'is_pinned' => !$post->is_pinned
        ]);

        $message = $post->is_pinned ? 'Post pinned successfully.' : 'Post unpinned successfully.';

        return back()->with('success', $message);
    }

    /**
     * Display the IT dashboard.
     */
    public function itDashboard(Request $request)
    {
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        $query = Post::with('user', 'assignedTo');

        if ($request->filled('assigned_to_me')) {
            $query->where('assigned_to_user_id', Auth::id());
        }

        if ($request->get('sort') === 'priority') {
            $query->orderBy('priority', 'desc');
        } else {
            $query->latest();
        }

        $posts = $query->paginate(10)->withQueryString();

        return view('it-dashboard', compact('posts'));
    }

    /**
     * Assign a ticket to the logged-in user.
     */
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