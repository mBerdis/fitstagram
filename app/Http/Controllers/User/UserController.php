<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Services\UserAuthenticationService;
use App\Models\Post;
use App\Models\User;
use App\Enums\UserRole;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create(PostRetrievalService $postService,UserAuthenticationService $authService)
    {

        if (!$authService->role_access(UserRole::SILENCED)) {
            return back();
        }

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
        $isFriend = $postService->get_friend_status($user->id);

        return Inertia::render('PublicUserPage', [
            'user' => $user,
            'posts' => $posts,
            'isFriend' => $isFriend
        ]);
    }

    public function sendFriendRequest(Request $request, PostRetrievalService $postService)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
        ]);

        $loggedUser = auth()->user();
        $userToAdd = User::where('username', $request->username)->firstOrFail();

        $friendRequests = $loggedUser->friendRequests->pluck('user_id')->toArray();

        if (in_array($userToAdd->id, $friendRequests))
        {
            return back()->with('error', 'Friend request already sent.');
        }

        $loggedUser->friendRequests()->attach($userToAdd->id);

        return back()->with('success', 'Friend request sent.');
    }

}
