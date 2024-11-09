<?php

namespace App\Http\Controllers\Searchbar;

use Inertia\Inertia;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Tag;

class SearchBarController extends Controller
{
    public function showResults(Request $request)
    {
        $query = $request->query('query', '');

        // Split the query into words
        $terms = explode(' ', $query);

        // Filter out terms that start with '#' and remove the '#' character
        $tagTerms = array_map(fn($term) => ltrim($term, '#'), array_filter($terms, fn($term) => str_starts_with($term, '#')));


        // Check if there are tag terms to search
        if (!empty($tagTerms)) {
            $tagQuery = Tag::query();
            
            foreach ($tagTerms as $term) {
                $tagQuery->orWhere('name', 'like', '%' . $term . '%');
            }

            $results = [
                'users' => [], 
                'groups' => [],
                'tags' => $tagQuery->get(),
            ];
        } else {
            // Perform a standard search if no tags are specified
            $results = [
                'users' => User::where('username', 'like', '%' . $query . '%')->get(), 
                'groups' => Group::where('name', 'like', '%' . $query . '%')->get(),
                'tags' => Tag::where('name', 'like', '%' . $query . '%')->get(),
            ];
        }


        return Inertia::render('SearchResults', [
            'initialQuery' => $query,
            'results' => $results,
        ]);
    }
}


