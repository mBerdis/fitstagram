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

    public function showPostsByTag(Request $request, Tag $tag)
    {
        $user = Auth::user() ?? (object) ['id' => -1];

        // Retrieve posts related to the tag
        $posts = $tag->posts()->with('owner', 'comments', 'tags', 'comments.user')->paginate(10);

        // Map over the posts collection after retrieving the paginated results
        $posts->getCollection()->transform(function ($post) use ($user) {
            // Add an attribute to each post indicating if the user liked it
            $post->liked_by_user = $post->liked_by()->where('user_id', $user->id)->exists();
            unset($post->liked_by); // Ensure liked_by relationship is not included in the response
            return $post;
        });

        return Inertia::render('TagPosts', [
            'tag' => $tag->name,
            'posts' => $posts,
        ]);
    }

}


