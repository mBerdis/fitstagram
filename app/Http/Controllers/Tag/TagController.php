<?php

namespace App\Http\Controllers\Tag;

use App\Models\Tag;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRole;
use App\Services\UserAuthenticationService;

class TagController extends Controller
{
    
    public function delete_one_tag(Request $request, UserAuthenticationService $authService)
    {
        
        $request->validate([
            'tags' => 'required|array|min:1|max:1',
            'tags.*' => 'string|exists:tags,name',
        ]);
        
        
        if (! $authService->role_access(UserRole::MODERATOR) && ! $authService->role_access(UserRole::ADMIN)) {
            return back()->with('error', 'User has insufficient edit rights.');
        }

        
        Tag::where('name', $request->tags[0])->delete();

        
        return Inertia::location(route('feed'));
    }

    
    public function delete_more_tags(Request $request, UserAuthenticationService $authService)
    {
       
        $request->validate([
            'tags' => 'required|array',
            'tags.*' => 'string', 
        ]);

        
        if (! $authService->role_access(UserRole::MODERATOR) && ! $authService->role_access(UserRole::ADMIN)) {
            return back()->with('error', 'User has insufficient edit rights.');
        }

        
        $existingTags = Tag::whereIn('name', $request->tags)->pluck('name')->toArray();

        
        Tag::whereIn('name', $existingTags)->delete();

       
        return Inertia::location(route('feed'));
    }

}
