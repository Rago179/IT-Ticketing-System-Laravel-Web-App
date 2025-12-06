<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post; 
use App\Notifications\NewCommentNotification;

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
        $post = Post::find($request->post_id);
        // Notify the post owner if it's not their own comment
        if ($post->user_id !== Auth::id()) {
            $post->user->notify(new NewCommentNotification($comment));
        }

        if ($request->wantsJson()) {
            $comment->load('user');
           
            $html = view('comments.item', compact('comment'))->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Comment posted!',
                'count' => Comment::where('post_id', $request->post_id)->count(),
                'html' => $html, 
            ]);
        }

        return back()->with('success', 'Comment posted!');
    }

    public function update(Request $request, $id)
    {
        $comment = Comment::findOrFail($id);

        if (Auth::id() !== $comment->user_id && Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment->update([
            'content' => $request->content
        ]);

        if ($request->wantsJson()) {
            $comment->load('user');

            $html = view('comments.item', compact('comment'))->render();
            
            return response()->json([
                'success' => true,
                'message' => 'Comment posted!',
                'count' => Comment::where('post_id', $request->post_id)->count(),
                'html' => $html, 
            ]);
        }
        return back()->with('success', 'Comment updated successfully.');
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