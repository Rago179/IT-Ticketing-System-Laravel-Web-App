<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::get('/secret-page', function () {
    return 'You are logged in and can see this secret message!';
})->middleware(['auth']);

Route::middleware(['auth', 'verified'])->group(function () {
    // This single line creates standard routes for index, create, store, show, etc.
    Route::resource('posts', PostController::class);

    // Your custom route for updating status
    Route::patch('/posts/{post}/status', [PostController::class, 'updateStatus'])
        ->name('posts.updateStatus');
});


require __DIR__.'/auth.php';
