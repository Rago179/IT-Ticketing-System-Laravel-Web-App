<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display the specified user's profile.
     */
    public function show(User $user)
    {
        // LOGIC MOVED TO CONTROLLER:
        // 1. Sort relations by 'latest()' so most recent activity shows first.
        // 2. Limit the results to 10 (or 20) so the profile page doesn't crash for active users.
        // 3. Eager load 'comments.post' to prevent N+1 query issues in the view.
        
        $user->load([
            'profile',
            'posts' => function ($query) {
                $query->latest()->take(10);
            },
            'comments' => function ($query) {
                $query->latest()->take(10)->with('post'); // Nested eager load for the post title
            }
        ]);

        return view('users.show', compact('user'));
    }
}