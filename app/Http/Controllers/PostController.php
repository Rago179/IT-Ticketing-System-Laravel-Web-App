<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category; 
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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('posts.create');
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
        ], [
            // Custom message for when it's not a number
            'priority.integer' => 'Only numbers are allowed for priority.',
            // Custom message for when it's a number, but not 1-4
            'priority.between' => 'Priority must be a number between 1 and 4.',
        ]);

        Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);

        return redirect()->route('posts.index')->with('success', 'Post published successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $post = Post::with('comments.user')->findOrFail($id);
        return view('posts.show', compact('post'));
    }

    // Update status (IT/Admin only)
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:open,in_progress,resolved'
        ]);

        $post = Post::findOrFail($id);

        if (!in_array(Auth::user()->role, ['it','admin'])) {
            abort(403, 'Unauthorized');
        }

        // --- NEW LOGIC START ---
        // If trying to mark as 'resolved', check for at least one comment from this user
        if ($request->status === 'resolved') {
            $hasCommented = $post->comments()
                                ->where('user_id', Auth::id())
                                ->exists();

            if (!$hasCommented) {
                return back()->withErrors(['status' => 'You must add a comment before resolving this ticket.']);
            }
        }
        // --- NEW LOGIC END ---

        $post->update(['status' => $request->status]);

        return back()->with('success', 'Status updated!');
    }
    
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Display the IT dashboard with filtering and sorting.
     */
    public function itDashboard(Request $request)
    {
        // 1. Authorize
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        $query = Post::with('user', 'assignedTo');

        // 2. Handle "Assigned to Me" filter
        // Changed from 'has' to 'filled' to ensure value is present
        if ($request->filled('assigned_to_me')) {
            $query->where('assigned_to_user_id', Auth::id());
        }

        // 3. Handle Sorting
        if ($request->get('sort') === 'priority') {
            $query->orderBy('priority', 'desc');
        } else {
            $query->latest();
        }

        // 4. Handle "Priority" sorting
        if ($request->get('sort') === 'priority') {
            $query->orderBy('priority', 'desc'); // Order by highest priority
        } else {
            $query->latest(); // Default sort by newest
        }

        // 5. Paginate results
        $posts = $query->paginate(10)->withQueryString(); // withQueryString preserves filters

        // 6. Return the new view
        return view('it-dashboard', compact('posts'));
    }

    /**
     * Assign a ticket to the currently logged-in IT user.
     */
    public function assign(Post $post)
    {
        // 1. Authorize
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        // 2. Assign the post and set status to 'in_progress'
        $post->update([
            'assigned_to_user_id' => Auth::id(),
            'status' => 'in_progress'
        ]);

        // 3. Redirect back
        return back()->with('success', 'Ticket assigned to you and set to In Progress.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        // MANUAL CHECK: Is the current user the owner?
        if (Auth::id() !== $post->user_id) {
            abort(403, 'Unauthorized Access');
        }

        $post->delete();
        return redirect()->route('posts.index');
    }
}
