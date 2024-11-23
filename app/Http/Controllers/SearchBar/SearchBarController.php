<?php

namespace App\Http\Controllers\SearchBar;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Tag;
use App\Models\Post;
use App\Services\PostRetrievalService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\RedirectResponse;
use App\Models\Comment;
use Inertia\Response;
use App\Enums\UserRole;
use App\Services\UserAuthenticationService;

class SearchBarController extends Controller
{


    public function showResults(Request $request)
    {

        $request->validate([
            'query' => 'required|string|max:255',
        ]);

        $user = auth()->user();
        if ($user) {
            $user->searchHistory()->create(['query' => $request->input('query')]);
        }

        $query = $request->query('query', '');

        $terms = explode(' ', $query);


        $results = [
            'users' => User::where('username', 'like', '%' . $query . '%')->get(),
            'groups' => Group::where('name', 'like', '%' . $query . '%')->get(),
            'tags' => Tag::where('name', 'like', '%' . $query . '%')->with('posts')->get()->map(function ($tag) {
                $post = $tag->posts()->first();
                $tag->picture = $post?->photo ?? '/default-tag-image.png';
                return $tag;
            }),
        ];


        return Inertia::render('SearchResults', [
            'initialQuery' => $query,
            'results' => $results,
        ]);
    }

    public function showPostsByTag(Request $request, $name, PostRetrievalService $postService)
    {
        $tag = Tag::where('name', $name)->first();
    
        if (!$tag) {
            return Inertia::render('TagPosts', [
                'tags' => [$name],
                'posts' => [],
                'query' => ['sort' => $request->query('sort', 'newest')],
                'errorMessage' => "No results found for the tag '{$name}'."
            ]);
        }
    
        $query = $request->query('query', null);
    
        $user = auth()->user();
        if ($user && $query) {
            $user->searchHistory()->create(['query' => $query]);
        }
    
        $sort = $request->query('sort', 'newest');
        $posts = $postService->get_tag_images($tag, $sort);
    
        return Inertia::render('TagPosts', [
            'tags' => [$tag->name],
            'posts' => $posts,
            'query' => ['sort' => $sort],
            'errorMessage' => null
        ]);
    }
    

    public function showPostsByTags(Request $request, $tags, PostRetrievalService $postService)
    {
        $request->validate([
            'query' => 'required|string|max:255',
        ]);
    
        $user = auth()->user();
        if ($user) {
            $user->searchHistory()->create(['query' => $request->input('query')]);
        }
    
        $tagArray = explode('+', $tags);
        $existingTags = Tag::whereIn('name', $tagArray)->pluck('name')->toArray();
    
        if (empty($existingTags)) {
            return Inertia::render('TagPosts', [
                'tags' => $tagArray,
                'posts' => [],
                'query' => ['sort' => $request->query('sort', 'newest')],
                'errorMessage' => "No results found for the tags: " . implode(', ', $tagArray) . "."
            ]);
        }
    
        $sort = $request->query('sort', 'newest');
        $posts = $postService->get_tags_images($existingTags, $sort);
    
        return Inertia::render('TagPosts', [
            'tags' => $existingTags,
            'posts' => $posts,
            'query' => ['sort' => $sort],
            'errorMessage' => null
        ]);
    }
    

    public function showSearchHistory(Request $request)
    {
        $searchHistory = $request->user()->searchHistory()->latest()->take(8)->get();

        return response()->json($searchHistory);
    }

    public function removeSearchHistory($id)
    {
        $history = auth()->user()->searchHistory()->find($id);

        if ($history) {
            $history->delete();
            return response()->json(['message' => 'Search history removed successfully']);
        }

        return response()->json(['message' => 'History not found'], 404);
    }

}


