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

        $tagTerms = array_map(fn($term) => ltrim($term, '#'), array_filter($terms, fn($term) => str_contains($term, '#')));

        if (!empty($tagTerms)) {
            $postsQuery = Post::query();
        
            foreach ($tagTerms as $term) {
                $postsQuery->whereHas('tags', fn($q) => $q->where('name', 'like',  $term ));
            }
        
            $posts = $postsQuery->with('owner', 'comments', 'tags')->paginate(10);
        
            return Inertia::render('TagPosts', [
                'tag' => implode(', ', $tagTerms), // Zobrazí hľadané tagy
                'posts' => $posts,
            ]);
        }
         else {
            $results = [
                'users' => User::where('username', 'like', '%' . $query . '%')->get(),
                'groups' => Group::where('name', 'like', '%' . $query . '%')->get(),
                'tags' => Tag::where('name', 'like', '%' . $query . '%')->with('posts')->get()->map(function ($tag) {
                    $post = $tag->posts()->first();
                    $tag->picture = $post?->photo ?? '/default-tag-image.png';
                    return $tag;
                }),
            ];
        }

        return Inertia::render('SearchResults', [
            'initialQuery' => $query,
            'results' => $results,
        ]);
    }

    public function showPostsByTag(Request $request, Tag $tag, PostRetrievalService $postService)
    {
        // Get the sorting option from the request, default to 'newest'
        $sort = $request->query('sort', 'newest');

        // Get the posts using the TagPostRetrievalService
        $posts = $postService->get_tag_images($tag, $sort);

        // Return the Inertia response
        return Inertia::render('TagPosts', [
            'tag' => $tag->name,
            'posts' => $posts,
            'query' => ['sort' => $sort] // Pass the sorting option to the frontend
        ]);
    }

}


