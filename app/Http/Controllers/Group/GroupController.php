<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
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
        $posts      = $postService->get_group_images($group->id);
        $status     = $groupService->get_membership_status($group->id);
        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;

        $members      = null;   // dont grab members for all users
        $joinRequests = null;   // dont grab requests for all users

        if ($status === GroupMembership::OWNER)
            $joinRequests = $group->joinRequests;

        if ($status->value >= GroupMembership::MEMBER->value)
            $members = $group->members;


        return Inertia::render('GroupDetail', [
            'group' => $group,
            'members' => $members,
            'posts' => $posts,
            'membership_status' => $status->value,
            'logged_user_id' => $loggedUserID,
            'join_requests' => $joinRequests
        ]);
    }


    public function create_group(Request $request, UserAuthenticationService $authService)
    {
        if (!$authService->role_access(UserRole::USER)) {
            return back();
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:'.Group::class,
            'description' => 'nullable|string|max:255',
        ]);

        $imagePath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imageName = uniqid();
            Storage::disk('public')->put('uploads/' . $imageName, file_get_contents($photo));
            $imagePath = '/storage/uploads/' . $imageName;
        }

        $user = auth()->user();

        $group = Group::create([
            'name' => $request->name,
            'user_id' => $user->id,
            'profile_picture' => $imagePath,
            'description' => $request->description,
        ]);

        $group->members()->attach($user);

        return Inertia::location(route('group', ['groupName' => $group->name]));
    }

    public function delete_group(Request $request, UserAuthenticationService $authService, GroupManagmentService $groupService)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $group          = Group::findOrFail($request->group_id);
        $status         = $groupService->get_membership_status($group->id);
        $loggedUserID   = Auth()->check() ? Auth()->user()->id : -1;

        if (! ($status->value === GroupMembership::OWNER->value) &&
            !$authService->role_access(UserRole::MODERATOR))
            return back()->with('error', 'Insufficient rights.');

        $group->delete();

        return Inertia::location(route('groups'));
    }
}
