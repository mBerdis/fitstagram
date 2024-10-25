<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\User\UserController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/feed', [PostController::class, 'create'])->middleware(['auth', 'verified'])->name('feed');
Route::middleware('auth')->post('/feed', [CommentController::class, 'store'])->name('comments.store');

Route::get('/groups', [GroupController::class, 'create'])->middleware(['auth', 'verified'])->name('groups');
Route::get('/groups/{groupName}', [GroupController::class, 'detail'])->middleware(['auth', 'verified'])->name('group');

Route::get('/MyPage', [UserController::class, 'create'])->middleware(['auth', 'verified'])->name('MyPage');

Route::get('/user/{username}', [UserController::class, 'detail'])->middleware(['auth', 'verified'])->name('user');
Route::get('/user/friendRequest/{username}', [UserController::class, 'sendFriendRequest'])->middleware(['auth', 'verified'])->name('user.friendRequest');

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
