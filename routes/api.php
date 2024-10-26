<?php

use App\Http\Controllers\Post\CommentController;
use App\Http\Controllers\Post\PostController;

Route::middleware('auth')->post('/feed', [CommentController::class, 'store'])->name('comments.store');
Route::middleware('auth')->post('/NewPost', [PostController::class, 'store_post'])->name('post.store');
