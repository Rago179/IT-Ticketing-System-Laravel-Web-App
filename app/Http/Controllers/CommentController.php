<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Comment; // Use the Comment model

class CommentController extends Controller
{
    /**
     * Store a new comment for a given post.
     * * We use Eloquent's relationship create method to automatically 
     * assign the foreign key (post_id), adhering to best practices.
     */
    public function store(Request $request, $postId)
    {
        $request->validate([
            'content' => 'required|string'
        ]);

        // 1. Find the Post using the ID from the route segment
        $post = Post::findOrFail($postId);

        // 2. Use the Eloquent relationship method to create the Comment.
        // This implicitly sets the 'post_id'.
        $post->comments()->create([
            'user_id' => Auth::id(), // Still need the current user's ID
            'content' => $request->content,
        ]);

        return back();
    }
}
