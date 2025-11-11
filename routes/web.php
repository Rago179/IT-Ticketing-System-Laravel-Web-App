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

    Route::post('forgot-password', function (Illuminate\Http\Request $request) {
        $request->validate(['email' => 'required|email']);
        
        // Send the link
        $status = Illuminate\Support\Facades\Password::sendResetLink(
            $request->only('email')
        );

        return $status === Illuminate\Support\Facades\Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withErrors(['email' => __($status)]);
    })->name('password.email');
});

Route::view('profile', 'profile')->middleware(['auth'])->name('profile');

// --- 2. YOUR MAIN AUTH GROUP ---
Route::middleware(['auth', 'verified'])->group(function () {
    // Homepage now uses paginate(5) instead of get()
    Route::get('/', function () {
        $posts = Post::with('user')->latest()->paginate(5);
        return view('posts.index', ['posts' => $posts]);
    })->name('home');

    Route::resource('posts', PostController::class);
    Route::resource('comments', CommentController::class)->only(['store', 'destroy']);
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])->name('posts.updateStatus');

    // Route for the IT dashboard page
    Route::get('/it-dashboard', [App\Http\Controllers\PostController::class, 'itDashboard'])
        ->name('it.dashboard');

    // Route to handle assigning a ticket to the logged-in IT user
    Route::patch('/posts/{post}/assign', [App\Http\Controllers\PostController::class, 'assign'])
        ->name('posts.assign');
});


Route::post('logout', function (Request $request) {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('/login');
})->name('logout');

require __DIR__.'/auth.php';