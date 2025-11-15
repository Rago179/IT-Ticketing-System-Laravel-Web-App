<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the specified user's profile.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function show(User $user)
    {
        // Eager load the profile, posts, and comments
        $user->load('profile', 'posts', 'comments');

        return view('users.show', compact('user'));
    }

    public function toggleBlock(User $user)
    {
        // 1. Authorization: Only Admin
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // 2. Prevention: Admin cannot block themselves
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot block yourself.');
        }

        // 3. Toggle status
        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'blocked' : 'unblocked';
        return back()->with('success', "User has been {$status}.");
    }
    
}