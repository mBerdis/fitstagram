<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\Group;

class PostRetrievalService
{
    // returns posts to show at /feed page
    public function get_personal_feed()
    {
        $user = Auth::user();

        return Post::with('owner', 'comments', 'comments.user') // include comments and comments author
        //->where('is_public', '==', true)    // get public posts
        //->orWhere()                         // get posts from friends
        //->orWhere()                         // get posts from a group which user is a member of
        ->orderBy('created_at')
        ->get();
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
        // TODO:
        return Post::with('owner', 'comments', 'comments.user')
        ->whereIn('groups', $group_id)
        ->get();
    }

    private function has_access($user_id): bool
    {
        $loggedUser = Auth::user();

        if ($loggedUser->id == $user_id)
        {
            return true;
        }

        // TODO: check if he is a friend
    }
}
