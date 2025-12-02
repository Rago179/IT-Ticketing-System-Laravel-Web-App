<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        if (Auth::user()->is_blocked) {
            if ($request->wantsJson()) {
                return response()->json(['message' => 'Your account is blocked.'], 403);
            }
            return back()->with('error', 'Your account is blocked. You cannot comment.');
        }

        $request->validate([
            'content' => 'required|string',
            'post_id' => 'required|exists:posts,id',
        ]);

        $comment = Comment::create([
            'post_id' => $request->post_id, 
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        if ($request->wantsJson()) {
    
            $comment->load('user');       
            return response()->json([
                'success' => true,
                'message' => 'Comment posted!',
                'count' => Comment::where('post_id', $request->post_id)->count(),
                'comment' => [
                    'id' => $comment->id,
                    'content' => nl2br(e($comment->content)), 
                    'user_name' => $comment->user->name,
                    'user_url' => route('users.show', $comment->user),
                    'created_at' => $comment->created_at->diffForHumans(),
                    'delete_url' => route('comments.destroy', $comment),
                ],
                'count' => Comment::where('post_id', $request->post_id)->count()
            ]);
        }

        return back()->with('success', 'Comment posted!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $comment->delete();

        return back()->with('success', 'Comment deleted.');
    }
}