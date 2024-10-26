<?php

namespace App\Http\Controllers\Post;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use Illuminate\Support\Facades\Auth;
use App\Models\Post;
use App\Models\User;
use App\Models\Group;
use App\Models\Tag;
use App\Models\Comment;
use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRole;
use App\Services\UserAuthenticationService;


class PostController extends Controller
{
    public function create(Request $request, PostRetrievalService $postService): Response
    {
        $posts = $postService->get_personal_feed();

        return Inertia::render('Feed', [
            'posts' => $posts
        ]);
    }

    public function render_create_post(Request $request,UserAuthenticationService $authService): Response
    {
        if (!$authService->role_access(UserRole::USER)) {
            return back();
        }

        $user = auth()->user();
        $groups = $user->groupsMember;

        if ($authService->role_access(UserRole::MODERATOR)) {
            $groups = Group::all();
        }

        return Inertia::render('NewPost', [
            'groups' => $groups
        ]);
    }

    public function store_post(Request $request,UserAuthenticationService $authService)
    {


        if (!$authService->role_access(UserRole::USER)) {
            return back();
        }
        $user = auth()->user();

        $request->validate([
            'description' => 'string|max:255'
        ]);

        $post = Post::create([
            'user_id' => $user->id,
            'photo' => $request->photoUrl,
            'is_public' => $request->is_public,
        ]);

        if (strlen($request->description) > 0) {
            $comment = Comment::create([
                'message' => $request->description,
                'post_id' => $post->id,
                'user_id' => $user->id,
            ]);
        }

        foreach ($request->group_ids as $group_id) {
            $group = Group::find($group_id);
            if ($group) {
                $group->posts()->attach($post);
            }
        }

        preg_match_all('/#(\w+)/', $request->description, $matches);
        // Get unique
        $tags = array_unique($matches[1]);
        $tags = array_map('trim', $tags);

        foreach ($tags as $tag_name) {
            $db_tag = Tag::where('name', $tag_name)->first();
            if ($db_tag === null) {
                $db_tag = Tag::create([
                    'name' => $tag_name,
                ]);
            }
            $db_tag->posts()->attach($post->id);
        }

        return back();
    }
}
