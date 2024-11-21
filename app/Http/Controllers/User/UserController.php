<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Services\UserAuthenticationService;
use App\Models\Post;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\FriendStatus;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function create(Request $request,PostRetrievalService $postService,UserAuthenticationService $authService)
    {
        $user = auth()->user();

        return redirect()->route('user', $user->username);
    }

    public function detail(Request $request, PostRetrievalService $postService, UserAuthenticationService $authService)
    {
        if (!$authService->role_access(UserRole::SILENCED)) {
            return back();
        }

        $user = User::where('username', $request->username)->firstOrFail();
        $loggedUserID   = Auth()->check() ? Auth()->user()->id : -1;

        $sort = $request->get('sort', 'newest');
        $posts = $postService->get_user_images($user->id, $sort);
        $isFriend = $postService->get_friend_status($user->id);

        $groups         = null;
        $friends        = null;
        $friendRequests = null;

        if (($user->id === $loggedUserID) ||
            $authService->role_access(UserRole::MODERATOR))
        {
            $groups = $user->groupsMember;
            $friends = $user->friends;
            $friendRequests = $user->receivedFriendRequests()->get();
        }

        return Inertia::render('PublicUserPage', [
            'user' => $user,
            'posts' => $posts,
            'isFriend' => $isFriend,
            'friends' => $friends,
            'groups' => $groups,
            'friendRequests' => $friendRequests,
            'query' => ['sort' => $sort]
        ]);
    }


    public function updateRole(Request $request,UserAuthenticationService $authService)
    {
        if ($authService->role_access(UserRole::MODERATOR)) {

            $request->validate([
                'role' => 'required|integer|min:0|max:4',
            ]);

            if ($request->role > auth()->user()->role->value ) {
                return;
            }

            $user = User::findOrFail($request->id);

            $user->role = $request->role;
            $user->save();
        }
        return;
    }

    public function delete(Request $request,UserAuthenticationService $authService)
    {
        if ($authService->role_access(UserRole::ADMIN)) {
            $user = User::findOrFail($request->id);
            $user->delete();
        }
        return redirect()->route('feed');
    }
}
