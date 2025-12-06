<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\AssignedTicketNotification;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $pinnedPosts = Post::with(['user', 'categories'])
                            ->where('is_pinned', true)
                            ->latest()
                            ->get();

        $categories = Category::withCount('posts')
                            ->orderBy('name')
                            ->get();

        $query = Post::with(['user', 'categories'])->latest();
        
        if ($request->has('category')) {
            $query->whereHas('categories', function($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        $posts = $query->paginate(10);

        return view('posts.index', compact('pinnedPosts', 'categories', 'posts'));
    }

    public function create()
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Your account is restricted. You cannot create new posts.');
        }

        $categories = Category::all();
        return view('posts.create', compact('categories'));
    }

    public function store(Request $request)
    {
        if (Auth::user()->is_blocked) {
            return redirect()->route('home')->with('error', 'Your account is restricted.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'priority' => 'required|integer|between:1,4',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:1240', 
        ]);

        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('posts', 'public');
        }

        $post = Post::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'priority' => $request->priority,
            'image_path' => $imagePath, 
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

    public function show(Post $post)
    {
        $post->load('categories', 'comments.user');
        $categories = Category::all();
        return view('posts.show', compact('post', 'categories'));
    }

    public function edit(Post $post)
    {
        if (Auth::id() !== $post->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $categories = Category::all();
        return view('posts.edit', compact('post', 'categories'));
    }

    public function update(Request $request, Post $post)
    {
        $isOwner = Auth::id() === $post->user_id;
        $isAdmin = Auth::user()->role === 'admin';
        $isIT = Auth::user()->role === 'it';

        if (!$isOwner && !$isAdmin && !$isIT) {
            abort(403, 'Unauthorized Access');
        }

        $rules = [
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
        ];

        if ($isOwner || $isAdmin) {
            $rules['title'] = 'required|string|max:255';
            $rules['description'] = 'required|string';
            $rules['priority'] = 'required|integer|between:1,4';
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'; // 10MB
        }

        $request->validate($rules);

        if ($isOwner || $isAdmin) {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'priority' => $request->priority,
            ];

            if ($request->hasFile('image')) {
                $data['image_path'] = $request->file('image')->store('posts', 'public');
            }

            $post->update($data);
        }

        if ($request->has('categories')) {
             $post->categories()->sync($request->categories);
        } elseif ($isOwner || $isAdmin) {
             $post->categories()->detach();
        }

        return redirect()->route('posts.show', $post)->with('success', 'Post updated successfully.');
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

        $itStaff = User::whereIn('role', ['it', 'admin'])->orderBy('name')->get();

        return view('it-dashboard', compact('posts', 'itStaff'));
    }

    public function assign(Request $request, Post $post) 
    {
        if (!in_array(Auth::user()->role, ['it', 'admin'])) {
            abort(403, 'Unauthorized Access');
        }

        if (Auth::user()->role === 'admin' && $request->has('assigned_user_id')) {
            $request->validate([
                'assigned_user_id' => 'exists:users,id'
            ]);
            
            $post->assigned_to_user_id = $request->assigned_user_id;
            $targetUser = User::find($request->assigned_user_id);
            $message = "Ticket assigned to " . $targetUser->name;
        } else {
            $post->assigned_to_user_id = Auth::id();
            $message = 'Ticket assigned to you.';
        }

        
        if ($post->status === 'open') {
            $post->status = 'in_progress';
        }
        
        $post->save();
       $assignedUser = User::find($post->assigned_to_user_id); 

        if ($assignedUser && $assignedUser->id !== Auth::id()) {
            $assignedUser->notify(new AssignedTicketNotification($post, Auth::user()->name));
        }
        return back()->with('success', $message);
    }
}