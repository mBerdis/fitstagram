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
        $groups = $user->groupsMember;

        // Extract friend IDs (exclude the current user id)
        $friendIDs = $user->friends->map(function($friend) use ($user) {
            return $friend->user1 == $user->id ? $friend->user2 : $friend->user1;
        });

        $friends = User::whereIn('id', $friendIDs)->get();

        return Inertia::render('MyPage', [
            'user' => $user,
            'posts' => $posts,
            'friends' => $friends,
            'groups' => $groups
        ]);
    }
}
