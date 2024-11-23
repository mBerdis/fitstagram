<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Group\GroupJoinController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\User\FriendRequestController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Searchbar\SearchBarController;
use App\Http\Controllers\Tag\TagController;


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
Route::get('/groups/{name}', [GroupController::class, 'detail'])->name('group');
Route::post('/groups/update', [GroupController::class, 'update'])->name('group.update');

Route::get('/MyPage', [UserController::class, 'create'])->middleware(['auth', 'verified'])->name('MyPage');
Route::get('/user/{username}', [UserController::class, 'detail'])->name('user');
Route::post('/user/update', [UserController::class, 'update'])->name('user.update');
Route::post('/user/update-role', [UserController::class, 'updateRole'])->name('user.updateRole');
Route::post('/user/delete', [UserController::class, 'delete'])->name('user.delete');


Route::get('/user/addFriend/{username}', [FriendRequestController::class, 'send'])->middleware(['auth', 'verified'])->name('user.add.friend');

Route::post('/user/friendRequest/accept', [FriendRequestController::class, 'accept'])->name('friendRequest.accept')->middleware(['auth', 'verified']);
Route::post('/user/friendRequest/decline', [FriendRequestController::class, 'decline'])->name('friendRequest.decline')->middleware(['auth', 'verified']);
Route::post('/unfriend', [FriendRequestController::class, 'unfriend'])->name('unfriend')->middleware(['auth', 'verified']);

Route::middleware('auth')->post('/feed', [CommentController::class, 'store'])->name('comments.store');
Route::get('/search', [SearchBarController::class, 'showResults'])->name('search.results');
Route::get('/tag/{tag:name}', [SearchBarController::class, 'showPostsByTag'])->name('tag.posts');
Route::get('/tags/{tags}', [SearchBarController::class, 'showPostsByTags'])->name('tags.posts');
Route::delete('/tag/delete', [TagController::class, 'delete_one_tag'])->middleware(['auth', 'verified'])->name('tag.delete');
Route::delete('/tags/delete', [TagController::class, 'delete_more_tags'])->middleware(['auth', 'verified'])->name('tags.delete');


Route::post('/post/add_tag', [PostController::class, 'add_tag'])->middleware(['auth', 'verified'])->name('post.addTag');
Route::post('/post/delete_tag', [PostController::class, 'delete_tag'])->middleware(['auth', 'verified'])->name('post.delete_tag');
Route::post('/post/toggle_like', [PostController::class, 'toggle_like'])->middleware(['auth', 'verified'])->name('post.toggle_like');
Route::post('/post/toggle_is_public', [PostController::class, 'toggle_is_public'])->middleware(['auth', 'verified'])->name('post.toggle_is_public');
Route::post('/post/edit_description', [PostController::class, 'edit_description'])->middleware(['auth', 'verified'])->name('post.editDescription');
Route::delete('/post/delete', [PostController::class, 'delete_post'])->middleware(['auth', 'verified'])->name('post.delete');
Route::delete('/comment/delete', [CommentController::class, 'delete_comment'])->middleware(['auth', 'verified'])->name('comment.delete');

Route::post('/groups/join', [GroupJoinController::class, 'send_request'])->middleware(['auth', 'verified'])->name('group.join');
Route::post('/groups/leave', [GroupJoinController::class, 'remove_member'])->middleware(['auth', 'verified'])->name('group.leave');
Route::delete('/groups/delete', [GroupController::class, 'delete_group'])->middleware(['auth', 'verified'])->name('group.delete');
Route::delete('/groups/post/remove', [GroupController::class, 'remove_post'])->middleware(['auth', 'verified'])->name('group.post.remove');
Route::post('/groups/requests/accept', [GroupJoinController::class, 'accept'])->middleware(['auth', 'verified'])->name('group.request.accept');
Route::post('/groups/requests/decline', [GroupJoinController::class, 'decline'])->middleware(['auth', 'verified'])->name('group.request.decline');


Route::post('/groups/create', [GroupController::class, 'create_group'])->middleware(['auth', 'verified'])->name('group.create');

require __DIR__.'/auth.php';
require __DIR__.'/api.php';
