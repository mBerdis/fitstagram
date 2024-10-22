<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Models\Post;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create(PostRetrievalService $postService)
    {
        $user = auth()->user();
        $posts = Post::with('owner','comments', 'comments.user')
        ->where('user_id', $user->id)
        ->orderBy('created_at')
        ->get();
        $groups = $user->groupsMember;

        $friends = $postService->get_friends($user->id);

        return Inertia::render('MyPage', [
            'user' => $user,
            'posts' => $posts,
            'friends' => $friends,
            'groups' => $groups
        ]);
    }

    public function detail(Request $request, PostRetrievalService $postService)
    {
        $user = User::where('username', $request->username)->firstOrFail();
        $posts = $postService->get_user_images($user->id);

        return Inertia::render('PublicUserPage', [
            'user' => $user,
            'posts' => $posts,
        ]);
    }
}
