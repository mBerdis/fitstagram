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

class FriendRequestController extends Controller
{
    public function accept($id)
    {
        $loggedUser = auth()->user();
        $user = User::findOrFail($id);

        // Create bothways friendship
        $loggedUser->friends()->attach($user->id);
        $user->friends()->attach($loggedUser->id);

        // Remove friend request
        $loggedUser->friendRequests()->detach($user->id);

        return back()->with('success', 'Friend request accepted.');
    }

    public function decline($id)
    {
        $loggedUser = auth()->user();
        $user = User::findOrFail($id);

        // Remove friend request
        $loggedUser->friendRequests()->detach($user->id);

        return back()->with('success', 'Friend request declined.');
    }

    public function send(Request $request, PostRetrievalService $postService)
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

    public function unfriend(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $id = $request->input('id');

        $loggedUser = auth()->user();
        $user = User::findOrFail($id);

        // Remove bothways friendship
        $loggedUser->friends()->detach($user->id);
        $user->friends()->detach($loggedUser->id);

        return back()->with('success', 'Unfriended.');
    }
}
