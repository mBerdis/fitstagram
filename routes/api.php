<?php

use App\Http\Controllers\Post\CommentController;

Route::middleware('auth')->post('/feed', [CommentController::class, 'store'])->name('comments.store');