<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // 1. Check if blocked
    if (Auth::user()->is_blocked) {
        return back()->with('error', 'Your account is blocked. You cannot comment.');
    }
        $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
        ]);

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

        // FIX: Allow deletion if User is the Owner OR User is an Admin
        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}