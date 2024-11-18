<?php

namespace App\Http\Controllers\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
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

    public function render_create_post(Request $request, UserAuthenticationService $authService): Response|RedirectResponse
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
            'description' => 'nullable|string|max:255',
            'photoUrl' => 'nullable|url',
            'group_ids' => 'nullable|array',
            'group_ids.*' => 'integer',
            'is_public' => 'boolean',
        ]);

        if ($request->description == null) {
            $request->description = "";
        }

        $imagePath = null;

        if ($request->filled('photoUrl') && filter_var($request->photoUrl, FILTER_VALIDATE_URL)) {
            $imagePath = $request->photoUrl;
        }

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imageName = uniqid();
            Storage::disk('public')->put('uploads/' . $imageName, file_get_contents($photo));
            $imagePath = '/storage/uploads/' . $imageName;
        }

        $post = Post::create([
            'user_id' => $user->id,
            'photo' => $imagePath,
            'is_public' => $request->is_public,
            'description' => $request->description,
            'like_count' => 0
        ]);

        if ($request->group_ids != null) {
            foreach ($request->group_ids as $group_id) {
                $group = Group::find($group_id);
                if ($group) {
                    $group->posts()->attach($post);
                }
            }
        }


        preg_match_all('/#(\w+)/', $request->description, $matches);
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

        return redirect()->route('MyPage');
    }

    public function delete_post(Request $request, UserAuthenticationService $authService)
    {
        $user = auth()->user();

        $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::findOrFail($request->post_id);

        // verify user has rights
        if ( !($post->owner->id === $user->id)                  // is not author and
             && !$authService->role_access(UserRole::MODERATOR)) // is not at least mod
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $post->delete();

        return back()->with('success', 'Post removed.');
    }
}
