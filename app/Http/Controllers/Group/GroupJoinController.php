<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\PostRetrievalService;
use App\Models\Group;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;
use App\Enums\UserRole;
use App\Enums\GroupMembership;
use App\Services\UserAuthenticationService;
use App\Services\GroupManagmentService;

class GroupJoinController extends Controller
{
    public function accept(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $user  = User::findOrFail($request->input('user_id'));

        $members = $group->members->pluck('user_id')->toArray();

        if (in_array($user->id, $members))
        {
            return back()->with('error', 'Already member.');
        }

        // Add new member
        $group->members()->attach($user->id);

        // Remove join request
        $group->joinRequests()->detach($user->id);

        return back()->with([
            'success' => 'Group join request accepted.'
        ]);
    }

    public function decline(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $user  = User::findOrFail($request->input('user_id'));

        $group->joinRequests()->detach($user->id);

        return back()->with('success', 'Group join request declined.');
    }

    public function send_request(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $user  = auth()->user();

        $groupRequests = $group->joinRequests->pluck('id')->toArray();

        if (in_array($user->id, $groupRequests))
        {
            return back()->with('error', 'Group join request already sent.');
        }

        $group->joinRequests()->attach($user->id);

        return back()->with('success', 'Friend request sent.');
    }

    public function remove_member(Request $request, GroupManagmentService $groupService, UserAuthenticationService $authService)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $user  = User::findOrFail($request->input('user_id'));

        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;
        $status = $groupService->get_membership_status($group->id);

        // verify user has rights
        if ( !($status === GroupMembership::OWNER)              // is not owner and
            && !($loggedUserID === $user->id)                   // is not modifying himself
            && !$authService->role_access(UserRole::MODERATOR)) // is not atleast mod
        {
            return back()->with('error', 'User has unsufficient edit rights.');
        }

        $group->members()->detach($user->id);

        return back()->with('success', 'User left the group.');
    }
}
