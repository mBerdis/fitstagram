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
use App\Services\UserAuthenticationService;

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

    public function remove_member(Request $request)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $group = Group::findOrFail($request->input('group_id'));
        $user  = User::findOrFail($request->input('user_id'));

        $group->members()->detach($user->id);

        return back()->with('success', 'User left the group.');
    }
}
