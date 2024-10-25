<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Models\Group;
use Inertia\Inertia;
use Inertia\Response;

class GroupController extends Controller
{
    public function create(Request $request): Response
    {
        $user = auth()->user();
        $groups = $user->groupsMember;

        return Inertia::render('Groups', [
            'groups' => $groups
        ]);
    }

    public function detail(Request $request, PostRetrievalService $postService): Response
    {
        $groupName  = $request->groupName;
        $group      = Group::firstWhere('name', $groupName);
        $members    = $group->members;
        $posts      = $postService->get_group_images($group->id);

        return Inertia::render('GroupDetail', [
            'group' => $group,
            'members' => $members,
            'posts' => $posts
        ]);
    }
}
