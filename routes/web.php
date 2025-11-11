<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController; // <-- IMPORTED
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

// --- 2. MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {
    
    // /allposts is now the main dashboard
    Route::get('/allposts', [PostController::class, 'index'])->name('home');

    // Resource routes for posts (create, store, show, etc.)
    // We remove 'index' since it's now handled by the 'home' route above
    Route::resource('posts', PostController::class)->except(['index']);

    // Comments
    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    
    // User Profiles
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');

    // --- Category Routes ---
    Route::get('/categories/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');

    // --- Admin/IT Routes ---
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.updateStatus');
    Route::patch('/posts/{post}/pin', [PostController::class, 'pin'])->name('posts.pin');
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