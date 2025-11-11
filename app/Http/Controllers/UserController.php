<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
}