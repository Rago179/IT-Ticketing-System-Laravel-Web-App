<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Auth\Events\Registered;

// --- 0. NEW PUBLIC HOMEPAGE ---
Route::view('/', 'welcome')->name('welcome');

// --- 1. CUSTOM GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    Route::get('login', function () { return view('simple-login'); })->name('login');
    Route::post('login', function (Request $request) {
        $credentials = $request->validate(['email' => ['required', 'email'], 'password' => ['required']]);
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('home'));
        }
        return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
    });

    Route::get('register', function () { return view('simple-register'); })->name('register');
    Route::post('register', function (Request $request) {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        $user = User::create(['name' => $request->name, 'email' => $request->email, 'password' => Hash::make($request->password)]);
        event(new Registered($user));
        Auth::login($user);
        return redirect(route('home'));
    });

    // Custom Forgot Password Routes
    Route::get('forgot-password', function () {
        return view('simple-forgot-password');
    })->name('password.request');

    Route::post('forgot-password', function (Request $request) {
        $request->validate(['email' => 'required|email']);
        $status = Illuminate\Support\Facades\Password::sendResetLink($request->only('email'));
        return $status === Illuminate\Support\Facades\Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    })->name('password.email');
});

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// --- 2. YOUR MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Point 'home' to the 'allposts' URL for login redirects
    // This now uses the controller method instead of a closure
    Route::get('/allposts', [PostController::class, 'index'])->name('home');

    // Standard resource routes for posts
    // We can keep 'posts.index' pointing to /posts for standard REST conventions if desired, 
    // but /allposts is now your main dashboard.
    Route::resource('posts', PostController::class);

    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.updateStatus');

    // IT Dashboard Routes
    Route::get('/it-dashboard', [PostController::class, 'itDashboard'])
        ->name('it.dashboard');
    Route::patch('/posts/{post}/assign', [PostController::class, 'assign'])
        ->name('posts.assign');
});

Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';