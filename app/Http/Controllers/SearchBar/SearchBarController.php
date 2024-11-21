<?php

namespace App\Http\Controllers\Searchbar;

use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Tag;
use App\Models\Post;
use App\Services\PostRetrievalService;

class SearchBarController extends Controller
{
    

    public function showResults(Request $request)
    {
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

    public function showPostsByTag(Request $request, Tag $tag, PostRetrievalService $postService)
    {
       
        $sort = $request->query('sort', 'newest');

        
        $posts = $postService->get_tag_images($tag, $sort);

       
        return Inertia::render('TagPosts', [
            'tags' => [$tag->name],
            'posts' => $posts,
            'query' => ['sort' => $sort]
        ]);
    }

    public function showPostsByTags(Request $request, $tags, PostRetrievalService $postService)
    {
        $tagArray = explode('+', $tags);

        $sort = $request->query('sort', 'newest');
        $posts = $postService->get_tags_images($tagArray, $sort);

        
        return Inertia::render('TagPosts', [
            'tags' => $tagArray,
            'posts' => $posts,
            'query' => ['sort' => $sort]
        ]);
    }

}


