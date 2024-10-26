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
    public function accept(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $id = $request->input('id');
        $loggedUser = auth()->user();
        $user = User::findOrFail($id);

        $friends = $loggedUser->friends->pluck('user_id')->toArray();

        if (in_array($user->id, $friends))
        {
            return back()->with('error', 'Already friends.');
        }

        // Create bothways friendship
        $loggedUser->friends()->attach($user->id);
        $user->friends()->attach($loggedUser->id);

        // Remove friend request
        $loggedUser->receivedFriendRequests()->detach($user->id);

        return back()->with([
            'success' => 'Friend request accepted.',
            'isFriend' => FriendStatus::FRIENDSHIP
        ]);
    }

    public function decline(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
        ]);

        $id = $request->input('id');
        $loggedUser = auth()->user();
        $user = User::findOrFail($id);

        // Remove friend request
        $loggedUser->receivedFriendRequests()->detach($user->id);

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

        if (in_array($userToAdd->id, $receivedRequests)) {
            
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
