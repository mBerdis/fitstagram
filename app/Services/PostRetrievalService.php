<?php

namespace App\Services;
use App\Services\UserAuthenticationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Group;
use App\Models\User;
use App\Models\Tag;
use App\Enums\UserRole;

use App\Enums\FriendStatus;

class PostRetrievalService
{

    public function get_personal_feed($sort = 'newest')
    {
        if (Auth::check()) {
            $user = Auth::user();
            $friendIds = $user->friends->pluck('user2');
            $groupIds = $user->groupsMember->pluck('id');

            // Start with the query for posts
            $query = Post::with('owner', 'comments', 'comments.user', 'tags')
                ->where('is_public', true)
                ->orWhereHas('owner', fn($query) => $query->whereIn('users.id', $friendIds))
                ->orWhereHas('groups', fn($query) => $query->whereIn('groups.id', $groupIds))
                ->orWhereHas('owner', fn($query) => $query->where('users.id', $user->id));

            // Apply sorting
            $query = match ($sort) {
                'rating' => $query->orderBy('like_count', 'desc'),
                default => $query->orderBy('created_at', 'desc'),
            };

            // Paginate the posts
            $posts = $query->paginate(8);

            // Map over the posts to add the `liked_by_user` attribute
            $posts->getCollection()->transform(function ($post) use ($user) {
                $post->liked_by_user = $post->liked_by()->where('user_id', $user->id)->exists();
                unset($post->liked_by); // Ensure liked_by relationship is not included in the response
                return $post;
            });

            return $posts;
        }

        // Return public posts when the user is not authenticated
        return Post::with('owner', 'comments', 'comments.user', 'tags')
            ->where('is_public', true)
            ->orderBy($sort === 'rating' ? 'like_count' : 'created_at', 'desc')
            ->paginate(8);
    }


    public function get_user_images($user_id, $sort = 'newest')
    {
        $user = Auth::user() ?? (object) ['id' => -1]; // Assign a dummy user object with id -1 for unsigned users

        $query = Post::with('owner', 'comments', 'comments.user','tags')
            ->whereHas('owner', fn($query) => $query->where('id', $user_id));

        if (!PostRetrievalService::has_access($user_id)) {
            $query->where('is_public', true);
        }

        // Dynamické triedenie
        if ($sort === 'rating') {
            $query->orderByDesc('like_count'); // Triedenie podľa počtu lajkov
        } else {
            $query->orderByDesc('created_at'); // Predvolené triedenie podľa dátumu
        }

        // Use pagination
        $paginatedPosts = $query->paginate(8);

        // Map through the paginated items to add additional data
        $paginatedPosts->getCollection()->transform(function ($post) use ($user) {
            $post->liked_by_user = $post->liked_by()->where('user_id', $user->id)->exists();
            unset($post->liked_by);
            return $post;
        });

        return $paginatedPosts;
    }

    public function get_group_images($group_id, $sort = 'newest')
    {
        $user = Auth::user() ?? (object) ['id' => -1];

        $query = Post::with('owner', 'comments', 'comments.user', 'tags')
            ->whereHas('groups', fn($query) => $query->where('groups.id', $group_id));

        if ($sort === 'rating') {
            $query->orderByDesc('like_count'); // Zoradenie podľa hodnotenia
        } else {
            $query->orderByDesc('created_at'); // Predvolené zoradenie
        }

        // Use pagination
        $paginatedPosts = $query->paginate(8);

        // Map through the paginated items to add additional data
        $paginatedPosts->getCollection()->transform(function ($post) use ($user) {
            $post->liked_by_user = $post->liked_by()->where('user_id', $user->id)->exists();
            unset($post->liked_by);
            return $post;
        });

        return $paginatedPosts;
    }

    public function get_tag_images(Tag $tag, $sort = 'newest')
    {
        $user = Auth::user() ?? (object) ['id' => -1];

        // Create the base query for the posts
        $query = $tag->posts()->with('owner', 'comments', 'tags', 'comments.user');

        // Apply sorting logic
        $query = match ($sort) {
            'rating' => $query->orderBy('like_count', 'desc'),
            default => $query->orderBy('created_at', 'desc'),
        };

        // Paginate the posts
        $posts = $query->paginate(8);

        // Transform the posts (add liked_by_user flag)
        $posts->getCollection()->transform(function ($post) use ($user) {
            $post->liked_by_user = $post->liked_by()->where('user_id', $user->id)->exists();
            unset($post->liked_by); // Ensure liked_by relationship is not included in the response
            return $post;
        });

        return $posts;
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
        $receivedReqIds = $loggedUser->receivedFriendRequests->pluck('pivot.user1')->toArray();

        if ($loggedUser->id == $user_id)            return FriendStatus::THATS_ME;
        if (in_array($user_id, $friendIds))         return FriendStatus::FRIENDSHIP;
        if (in_array($user_id, $friendReqIds))      return FriendStatus::REQUEST_PENDING;
        if (in_array($user_id, $receivedReqIds))    return FriendStatus::REQUEST_RECEIVED;

        return FriendStatus::NONE;
    }
}
