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
use App\Enums\FriendStatus;

class FriendRequestController extends Controller
{
    public function accept(Request $request, UserAuthenticationService $authService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',        // decides to accept/decline
            'requestee_id' => 'required|exists:users,id',   // asks for friendship
        ]);

        $user         = User::findOrFail($request->input('user_id'));
        $requestee    = User::findOrFail($request->input('requestee_id'));
        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;

        $friends = $user->friends->pluck('user_id')->toArray();

        if (in_array($requestee->id, $friends))
        {
            return back()->with('error', 'Already friends.');
        }

        // verify user has rights
        if (!($loggedUserID === $user->id)                      // is not modifying himself
            && !$authService->role_access(UserRole::MODERATOR)) // is not atleast mod
        {
            return back()->with('error', 'User has unsufficient rights.');
        }

        // Create bothways friendship
        $requestee->friends()->attach($user->id);
        $user->friends()->attach($requestee->id);

        // Remove friend request
        $user->receivedFriendRequests()->detach($requestee->id);

        return back()->with([
            'success' => 'Friend request accepted.',
            'isFriend' => FriendStatus::FRIENDSHIP
        ]);
    }

    public function decline(Request $request, UserAuthenticationService $authService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',        // decides to accept/decline
            'requestee_id' => 'required|exists:users,id',   // asks for friendship
        ]);

        $user         = User::findOrFail($request->input('user_id'));
        $requestee    = User::findOrFail($request->input('requestee_id'));
        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;

        // verify user has rights
        if (!($loggedUserID === $user->id)                      // is not modifying himself
            && !$authService->role_access(UserRole::MODERATOR)) // is not atleast mod
        {
            return back()->with('error', 'User has unsufficient rights.');
        }

        // Remove friend request
        $user->receivedFriendRequests()->detach($requestee->id);

        return back()->with('success', 'Friend request declined.');
    }

    public function send(Request $request, PostRetrievalService $postService)
    {
        $request->validate([
            'username' => 'required|string|exists:users,username',
        ]);

        $loggedUser = auth()->user();
        $userToAdd = User::where('username', $request->username)->firstOrFail();

        $receivedRequests = $loggedUser->receivedFriendRequests->pluck('id')->toArray();

        if (in_array($userToAdd->id, $receivedRequests))
        {
            $loggedUser->friends()->attach($userToAdd->id);
            $userToAdd->friends()->attach($loggedUser->id);

            $loggedUser->receivedFriendRequests()->detach($userToAdd->id);

            return back()->with([
                'success' => 'Friend request accepted automatically.',
                'isFriend' => FriendStatus::FRIENDSHIP
            ]);
        }

        $sentRequests = $loggedUser->friendRequests->pluck('id')->toArray();
        if (in_array($userToAdd->id, $sentRequests)) {
            return back()->with('error', 'Friend request already sent.');
        }

        $loggedUser->friendRequests()->attach($userToAdd->id);

        return back()->with('success', 'Friend request sent.');
    }


    public function unfriend(Request $request, UserAuthenticationService $authService)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',        // wants to remove friend
            'friend_id' => 'required|exists:users,id',      // friend to remove
        ]);

        $user         = User::findOrFail($request->input('user_id'));
        $friend       = User::findOrFail($request->input('friend_id'));
        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;

        // verify user has rights
        if (!($loggedUserID === $user->id)                      // is not modifying himself
            && !$authService->role_access(UserRole::MODERATOR)) // is not atleast mod
        {
            return back()->with('error', 'User has unsufficient rights.');
        }

        // Remove bothways friendship
        $user->friends()->detach($friend->id);
        $friend->friends()->detach($user->id);

        return back()->with('success', 'Unfriended.');
    }
}
