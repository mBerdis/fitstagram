<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function create(Request $request, PostRetrievalService $postService): Response
    {
        $posts = $postService->get_personal_feed();

        return Inertia::render('Feed', [
            'posts' => $posts
        ]);
    }
}
