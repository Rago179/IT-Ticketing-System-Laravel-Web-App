<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController; // <-- ADD THIS
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

// --- 0. PUBLIC HOMEPAGE ---
Route::view('/', 'welcome')->name('welcome');

// --- 1. GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    // ... your existing login, register, forgot-password routes ...
});

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// --- 2. MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // /allposts is now the main dashboard
    Route::get('/allposts', [PostController::class, 'index'])->name('home');

    // Resource routes for posts (create, store, show, etc.)
    Route::resource('posts', PostController::class)->except(['index']);

    // Comments
    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    
    // User Profiles
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // --- NEW: Category Page Route ---
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');

    // --- Admin/IT Routes ---
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.updateStatus');
    
    // --- NEW: Pin Post Route (Admin Only) ---
    Route::patch('/posts/{post}/pin', [PostController::class, 'pin'])->name('posts.pin');

    // IT Dashboard
    Route::get('/it-dashboard', [PostController::class, 'itDashboard'])->name('it.dashboard');
    Route::patch('/posts/{post}/assign', [PostController::class, 'assign'])->name('posts.assign');
});

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';