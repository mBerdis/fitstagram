<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\GroupJoinController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\FriendRequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Searchbar\SearchBarController;

Route::get('/', [PostController::class, 'create']);

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/feed', [PostController::class, 'create'])->name('feed');
Route::get('/NewPost', [PostController::class, 'render_create_post'])->middleware(['auth', 'verified'])->name('NewPost');
Route::get('/groups', [GroupController::class, 'create'])->middleware(['auth', 'verified'])->name('groups');
Route::get('/groups/{groupName}', [GroupController::class, 'detail'])->name('group');

Route::get('/MyPage', [UserController::class, 'create'])->middleware(['auth', 'verified'])->name('MyPage');
Route::get('/user/{username}', [UserController::class, 'detail'])->name('user');
Route::get('/user/friendRequest/{username}', [FriendRequestController::class, 'send'])->middleware(['auth', 'verified'])->name('user.friendRequest');
Route::post('/friendRequest/accept', [FriendRequestController::class, 'accept'])->name('friendRequest.accept')->middleware(['auth', 'verified']);
Route::post('/friendRequest/decline', [FriendRequestController::class, 'decline'])->name('friendRequest.decline')->middleware(['auth', 'verified']);
Route::post('/unfriend', [FriendRequestController::class, 'unfriend'])->name('unfriend')->middleware(['auth', 'verified']);

Route::middleware('auth')->post('/feed', [CommentController::class, 'store'])->name('comments.store');
Route::get('/search', [SearchBarController::class, 'showResults'])->name('search.results');



Route::post('/groups/join', [GroupJoinController::class, 'send_request'])->middleware(['auth', 'verified'])->name('group.join');
Route::post('/groups/leave', [GroupJoinController::class, 'remove_member'])->middleware(['auth', 'verified'])->name('group.leave');
Route::post('/groups/requests/accept', [GroupJoinController::class, 'accept'])->middleware(['auth', 'verified'])->name('group.request.accept');
Route::post('/groups/requests/decline', [GroupJoinController::class, 'decline'])->middleware(['auth', 'verified'])->name('group.request.decline');


require __DIR__.'/auth.php';
require __DIR__.'/api.php';
