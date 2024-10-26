<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Models\Group;
use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRole;
use App\Services\UserAuthenticationService;
use App\Services\GroupManagmentService;
use App\Enums\GroupMembership;


class GroupController extends Controller
{
    public function create(Request $request,UserAuthenticationService $authService): Response
    {
        $user = auth()->user();
        $groups = $user->groupsMember;

        if ($authService->role_access(UserRole::MODERATOR)) {
            $groups = Group::all();
        }
        if (!$authService->role_access(UserRole::SILENCED)) {
            $groups = null;
        }


        return Inertia::render('Groups', [
            'groups' => $groups
        ]);
    }

    public function detail(Request $request, PostRetrievalService $postService, GroupManagmentService $groupService): Response
    {
        $groupName  = $request->groupName;
        $group      = Group::firstWhere('name', $groupName);
        $members    = $group->members;
        $posts      = $postService->get_group_images($group->id);
        $status     = $groupService->get_membership_status($group->id);

        dd($status);

        return Inertia::render('GroupDetail', [
            'group' => $group,
            'members' => $members,
            'posts' => $posts,
        ]);
    }
}
