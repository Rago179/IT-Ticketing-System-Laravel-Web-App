<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(User $user)
    {
        $user->load([
            'profile',
            'posts' => function ($query) {
                $query->latest()->take(10);
            },
            'comments' => function ($query) {
                $query->latest()->take(10)->with('post');
            }
        ]);

        return view('users.show', compact('user'));
    }

    // NEW: Handle Profile Updates (Bio)
    public function update(Request $request, User $user)
    {
        // 1. Authorization: Only the owner can edit
        if (Auth::id() !== $user->id) {
            abort(403, 'Unauthorized');
        }

        // 2. Validation
        $request->validate([
            'bio' => 'nullable|string|max:1000',
        ]);

        // 3. Save to Profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            ['bio' => $request->bio]
        );

        return back()->with('success', 'Profile updated successfully!');
    }

    public function toggleBlock(User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot block yourself.');
        }

        $user->is_blocked = !$user->is_blocked;
        $user->save();

        $status = $user->is_blocked ? 'blocked' : 'unblocked';
        return back()->with('success', "User has been {$status}.");
    }

    public function index()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Fetch users, paginated, usually helpful to sort by ID or name
        $users = User::orderBy('id')->paginate(15);

        return view('users.index', compact('users'));
    }

    // NEW: Update User Role
    public function updateRole(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized');
        }

        // Prevent Admin from changing their own role to something else (lockout protection)
        if ($user->id === Auth::id()) {
            return back()->with('error', 'You cannot change your own role.');
        }

        $request->validate([
            'role' => 'required|in:user,it,admin',
        ]);

        $user->update(['role' => $request->role]);

        return back()->with('success', "User role updated to {$request->role}.");
    }
}