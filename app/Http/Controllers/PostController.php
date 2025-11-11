<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Change get() to paginate(10)
        $posts = Post::with('user')->latest()->paginate(10);
        
        return view('posts.index', compact('posts'));
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

        return redirect()->route('posts.index');
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
        // 1. Authorize: Only 'it' or 'admin' can see this page
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        // 2. Start query
        $query = Post::with('user', 'assignedTo');

        // 3. Handle "Assigned to Me" filter
        if ($request->has('assigned_to_me')) {
            $query->where('assigned_to_user_id', Auth::id());
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
