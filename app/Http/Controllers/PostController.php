<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    // ... [Keep index method] ...
    public function index(Request $request)
    {
        // ... (your existing index code)
        $pinnedPosts = Post::with(['user', 'categories'])
                            ->where('is_pinned', true)
                            ->latest()
                            ->get();

        $categories = Category::withCount('posts')
                            ->orderBy('name')
                            ->get();

        return view('posts.index', compact('pinnedPosts', 'categories'));
    }

    public function create()
    {
        // NEW: Check if user is blocked before showing the form
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Your account is restricted. You cannot create new posts.');
        }

        // Logic: Fetch all categories so the view can just loop through them
        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        // Double-check: Block submission as well (security best practice)
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Your account is restricted. You cannot create new posts.');
        }

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

        if ($request->has('categories') && !empty($request->categories)) {
            $post->categories()->attach($request->categories);
        } else {
            $otherCategory = Category::where('name', 'Other')->first();
            if ($otherCategory) {
                $post->categories()->attach($otherCategory->id);
            }
        }

        return redirect()->route('home')->with('success', 'Post published successfully!');
    }

    // ... [The rest of your controller (show, update, destroy, etc.) remains exactly the same] ...
    public function show(Post $post)
    {
        $post->load('categories', 'comments.user');
        $categories = Category::all();
        return view('posts.show', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        $request->validate([
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
        ]);

        $post->categories()->sync($request->categories);

        return back()->with('success', 'Post categories updated successfully.');
    }

    public function destroy(Post $post)
    {
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }
        
        $post->delete();
        
        return back()->with('success', 'Post deleted.');
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

        $query = Post::with('user', 'assignedTo')->withCount('comments');

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