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
    public function create(Request $request, PostRetrievalService $postService)
    {
        $sort = $request->query('sort', 'newest');
        $posts = $postService->get_personal_feed($sort);

        return Inertia::render('Feed', [
            'posts' => $posts,
            'query' => ['sort' => $sort]
        ]);


    }


    public function toggle_like(Request $request, UserAuthenticationService $authService)
    {
        if (!$authService->role_access(UserRole::SILENCED)) {
            return;
        }

        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($validated['post_id']);
        $user = $request->user();

        if (!$post) {
            return;
        }

        if ($post->liked_by()->where('user_id', $user->id)->exists()) {
            $post->liked_by()->detach($user->id);
            $post->like_count--;
        } else {
            $post->liked_by()->attach($user->id);
            $post->like_count++;
        }
        $post->save();

        return;
    }

    public function toggle_is_public(Request $request, UserAuthenticationService $authService)
    {

        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($validated['post_id']);
        $user = auth()->user();

        // verify user has rights
        if ( !($post->owner->id === $user->id))
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        if ($post->is_public) {
            $post->is_public = false;
        } else {
            $post->is_public = true;
        }
        $post->save();

        return;
    }

    public function delete_tag(Request $request, UserAuthenticationService $authService)
    {

        $validated = $request->validate([
            'tag_id' => 'required|exists:tags,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($validated['post_id']);
        $user = auth()->user();

        // verify user has rights
        if ( !($post->owner->id === $user->id)                  // is not author and
             && !$authService->role_access(UserRole::MODERATOR)) // is not at least mod
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $post->tags()->detach($validated['tag_id']);
        $post->save();

        return;
    }

    public function add_tag(Request $request, UserAuthenticationService $authService)
    {

        $validated = $request->validate([
            'content' => 'nullable|string|max:255',
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($validated['post_id']);
        $user = auth()->user();

        // verify user has rights
        if ( !($post->owner->id === $user->id)                  // is not author and
             && !$authService->role_access(UserRole::MODERATOR)) // is not at least mod
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $content = $validated['content'];

        $tags = explode('#', $content);

        $tags = array_map(function($tag) {
            return str_replace(' ', '_',trim(str_replace('_',' ' , $tag)));
        }, $tags);

        $tags = array_filter($tags, function($tag) {
            return !empty(trim($tag));
        });

        // Get unique tags
        $tags = array_unique($tags);

        foreach ($tags as $tag_name) {
            $db_tag = Tag::where('name', $tag_name)->first();
            if ($db_tag === null) {
                $db_tag = Tag::create([
                    'name' => $tag_name,
                ]);
            }
            $db_tag->posts()->attach($post->id);
        }

        return;
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
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50'
        ]);

        $description = $request->description ?? "";
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
            'description' => $description,
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

        $tags = $request->tags ?? [];

        preg_match_all('/#(\w+)/', $description, $matches);
        $tagsFromDescription = $matches[1] ?? [];
        $tags = array_merge($tags, $tagsFromDescription);

        $tags = array_map(fn($tag) => str_replace(' ', '_', trim($tag)), $tags);
        $tags = array_filter($tags, fn($tag) => !empty($tag));
        $tags = array_unique($tags);

        foreach ($tags as $tag_name) {
            $db_tag = Tag::firstOrCreate(['name' => $tag_name]);
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

    public function edit_description(Request $request,UserAuthenticationService $authService)
    {
        $validated = $request->validate([
            'content' => 'nullable|string|max:255',
            'post_id' => 'required|exists:posts,id',
        ]);

        $post = Post::find($validated['post_id']);
        $user = auth()->user();

        if ( !($post->owner->id === $user->id) )
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $post->description = $validated['content'];
        $post->save();

        return back();
    }
}
