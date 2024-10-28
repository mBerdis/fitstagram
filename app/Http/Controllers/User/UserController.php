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
        $friends = $user->friends;
        $friendRequests = $user->receivedFriendRequests()->get();

        return Inertia::render('MyPage', [
            'user' => $user,
            'posts' => $posts,
            'friends' => $friends,
            'groups' => $groups,
            'friendRequests' => $friendRequests
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
