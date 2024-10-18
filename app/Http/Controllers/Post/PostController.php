<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PostController extends Controller
{
    public function create(Request $request): Response
    {
        $post = Post::with('owner')->get();

        return Inertia::render('Feed/Post', [
            'posts' => $post
        ]);
    }
}
