<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Models\Post; // Make sure this is imported

// --- NEW LOGIN ROUTE ---
// This overrides the Breeze default login page
// It MUST come before the `require __DIR__.'/auth.php';` line
Route::get('login', function () {
    return view('simple-login');
})->name('login');


// --- BREEZE DASHBOARD & PROFILE ---
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/secret-page', function () {
    return 'You are logged in and can see this secret message!';
})->middleware(['auth']);

// --- MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {

    // 2. PROTECTED HOMEPAGE
    // Now, visiting "/" will require login
    Route::get('/', function () {
        // 4. Get ALL posts
        $posts = Post::with('user')->latest()->get();
        // Use the new 'home' view
        return view('home', ['posts' => $posts]);
    })->name('home'); // Give it a name, like 'home'

    // This single line creates standard routes for index, create, store, show, etc.
    Route::resource('posts', PostController::class);
    
    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    
    // Your custom route for updating status
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])
        ->name('posts.updateStatus');
});

Route::post('logout', function (Illuminate\Http\Request $request) {
    Illuminate\Support\Facades\Auth::guard('web')->logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
})->name('logout');
require __DIR__.'/auth.php';