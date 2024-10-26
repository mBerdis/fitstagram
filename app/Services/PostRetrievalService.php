<?php

namespace App\Services;
use App\Services\UserAuthenticationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Group;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\FriendStatus;

class PostRetrievalService
{
    // returns posts to show at /feed page
    public function get_personal_feed()
    {
        if (Auth::check())
        {
            $user = Auth::user();
            $friendIds = $user->friends->pluck('user2');
            $groupIds = $user->groupsMember->pluck('id');

            return Post::with('owner', 'comments', 'comments.user') // include comments and comments author
            ->where('is_public', true)                                                      // get public posts
            ->orWhereHas('owner', fn($query) => $query->whereIn('users.id', $friendIds))    // get posts from friends
            ->orWhereHas('groups', fn($query) => $query->whereIn('groups.id', $groupIds))   // get posts from groups
            ->orderBy('created_at')
            ->get();
        }
        return Post::with('owner', 'comments', 'comments.user') // include comments and comments author
            ->where('is_public', true)->orderBy('created_at')->get();

    }

    // returns posts to show at /profile page
    public function get_user_images($user_id)
    {
        if (PostRetrievalService::has_access($user_id))
        {
            // show public and private posts since logged user has access to them
            return Post::with('owner', 'comments', 'comments.user') // include comments and comments author
            ->whereHas('owner', fn($query) => $query->where('user_id', $user_id))
            ->orderBy('created_at')
            ->get();
        }

        return Post::with('owner', 'comments', 'comments.user') // include comments and comments author
        ->whereHas('owner', fn($query) => $query->where('user_id', $user_id))
        ->where('is_public', true)    // get public posts
        ->orderBy('created_at')
        ->get();
    }

    // returns posts to show at /profile page
    public function get_group_images($group_id)
    {
        return Post::with('owner', 'comments', 'comments.user')
        ->whereHas('groups', fn($query) => $query->where('groups.id', $group_id))
        ->get();
    }

    private function has_access($user_id): bool
    {
        if (!Auth::check())
        {
            return false;
        }

        $loggedUser = Auth::user();
        $friendIds = $loggedUser->friends->pluck('user2')->toArray();

        if ($loggedUser->id == $user_id
        || PostRetrievalService::get_friend_status($user_id) == FriendStatus::FRIENDSHIP
        || UserAuthenticationService::role_access(UserRole::ADMIN)
        )
        {
            return true;
        }

        return false;
    }

    public function get_friends($user_id)
    {
        $user = User::findOrFail($user_id);
        $friendIDs = $user->friends->map(function ($friend) use ($user) {
            return $friend->user1 == $user->id ? $friend->user2 : $friend->user1;
        });

        return User::whereIn('id', $friendIDs)->get();
    }

    public function get_friend_status($user_id): FriendStatus
    {
        if (!Auth::check())
        {
            return FriendStatus::NONE;
        }

        $loggedUser     = Auth::user();
        $friendIds      = $loggedUser->friends->pluck('pivot.user2')->toArray();
        $friendReqIds   = $loggedUser->friendRequests->pluck('pivot.user2')->toArray();

        if ($loggedUser->id == $user_id)       return FriendStatus::THATS_ME;
        if (in_array($user_id, $friendIds))    return FriendStatus::FRIENDSHIP;
        if (in_array($user_id, $friendReqIds)) return FriendStatus::REQUEST_PENDING;

        return FriendStatus::NONE;
    }
}
