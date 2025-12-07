<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Comment;

class LikeController extends Controller
{
    public function toggle(Request $request)
    {
        $request->validate([
            'id' => 'required|integer',
            'type' => 'required|in:post,comment',
        ]);

        $user = Auth::user();
        
        // Find the model based on type
        if ($request->type === 'post') {
            $model = Post::findOrFail($request->id);
        } else {
            $model = Comment::findOrFail($request->id);
        }

        // Check if already liked
        $existingLike = $model->likes()->where('user_id', $user->id)->first();

        if ($existingLike) {
            $existingLike->delete();
            $liked = false;
            $message = 'Unliked';
        } else {
            $model->likes()->create([
                'user_id' => $user->id
            ]);
            $liked = true;
            $message = 'Liked';
        }

        // Return JSON for AJAX calls, or redirect back for standard requests
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'liked' => $liked,
                'count' => $model->likes()->count()
            ]);
        }

        return back()->with('success', $message);
    }
}