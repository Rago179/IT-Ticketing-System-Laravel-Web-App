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

// --- 1. CUSTOM GUEST ROUTES ---
Route::middleware('guest')->group(function () {
    
    // --- LOGIN ---
    Route::get('login', function () {
        return view('simple-login'); 
    })->name('login');

    Route::post('login', function (Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            // Redirect to home instead of dashboard
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    });

    // --- REGISTER ---
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

        return redirect(route('home'));
    });
});

// --- 2. PROFILE (Keep this if you want users to be able to edit their data later) ---
Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');


// --- 3. YOUR MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {

    // Your new simple homepage
    Route::get('/', function () {
        $posts = Post::with('user')->latest()->get();
        return view('posts.index', ['posts' => $posts]);
    })->name('home');

    // Your Post & Comment routes
    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])
        ->name('posts.updateStatus');
});

// --- 4. YOUR CUSTOM LOGOUT ROUTE ---
Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login'); // Redirect to login page after logout
})->name('logout');


// --- 5. BREEZE AUTH FILE (must be last) ---
require __DIR__.'/auth.php';