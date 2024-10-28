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

        // Fetch actual search results based on the query
        $results = [
            'users' => User::where('username', 'like', '%' . $query . '%')->get(), 
            'groups' => Group::where('name', 'like', '%' . $query . '%')->get(),
            'tags' => Tag::where('name', 'like', '%' . $query . '%')->get(),
        ];

        return Inertia::render('SearchResults', [
            'initialQuery' => $query,
            'results' => $results,
        ]);
    }

}
