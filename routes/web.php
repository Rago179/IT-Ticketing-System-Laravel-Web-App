<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Models\Post; // Make sure this is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

Route::get('register', function () {
    return view('simple-register');
})->name('register');

Route::post('register', function (Request $request) {
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('home', absolute: false));
});

// --- 1. UPDATED GET LOGIN ROUTE ---
Route::get('login', function () {
    // Use your existing file: resources/views/simple.login.blade.php
    return view('simple.login');
})->name('login');

// --- 2. KEEP THE SAME POST ROUTE ---
Route::get('login', function () {
    // Use the new filename with the hyphen
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