<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create()
    {
        $user = auth()->user();
        $posts = $user->posts;

        return Inertia::render('MyPage', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
