<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
public function store(Request $request)
{
    $request->validate([
        'content' => 'required|string',
        'post_id' => 'required|exists:posts,id', // Add validation for the hidden input
    ]);

    // Fetch the post_id directly from the form request
    Comment::create([
        'post_id' => $request->post_id, 
        'user_id' => Auth::id(),
        'content' => $request->content,
    ]);

    return back()->with('success', 'Comment posted!');
}

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        // MANUAL CHECK: Only allow if the logged-in user is the comment's owner
        if (Auth::id() !== $comment->user_id) {
            abort(403, 'Unauthorized');
        }

        $comment->delete();

        return back();
    }
    
}