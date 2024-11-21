<?php

namespace App\Http\Controllers\Group;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\PostRetrievalService;
use App\Models\Group;
use App\Models\Post;
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

    public function detail( Request $request, string $name, PostRetrievalService $postService, GroupManagmentService $groupService, UserAuthenticationService $authService)
    {
        $groupName  = $request->groupName;
        $sort       = $request->get('sort', 'newest');

        $validator = Validator::make(['name' => $name], [
            'name' => 'required|exists:groups,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors('Wrong group name');
        }

        $groupName  = $name;
        $group      = Group::firstWhere('name', $groupName);
        $posts      = $postService->get_group_images($group->id, $sort); // Prenášame typ zoradenia
        $status     = $groupService->get_membership_status($group->id);
        $loggedUserID = Auth()->check() ? Auth()->user()->id : -1;

        $members      = null;   // dont grab members for all users
        $joinRequests = null;   // dont grab requests for all users

        if ($status === GroupMembership::OWNER ||
            $authService->role_access(UserRole::MODERATOR))
        {
            $joinRequests = $group->joinRequests;
        }

        if ($status->value >= GroupMembership::MEMBER->value ||
            $authService->role_access(UserRole::MODERATOR))
        {
            $members = $group->members;
        }


        return Inertia::render('GroupDetail', [
            'group' => $group,
            'members' => $members,
            'posts' => $posts,
            'membership_status' => $status->value,
            'logged_user_id' => $loggedUserID,
            'join_requests' => $joinRequests,
            'query' => ['sort' => $sort]
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

    public function remove_post(Request $request, UserAuthenticationService $authService, GroupManagmentService $groupService)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'post_id' => 'required|exists:posts,id',
        ]);

        $group          = Group::findOrFail($request->group_id);
        $post           = Post::findOrFail($request->post_id);

        $status         = $groupService->get_membership_status($group->id);
        $loggedUserID   = Auth()->check() ? Auth()->user()->id : -1;

        if (! ($status->value === GroupMembership::OWNER->value) &&
            ! ($post->owner->id === $loggedUserID) &&
            !$authService->role_access(UserRole::MODERATOR))
            return back()->with('error', 'Insufficient rights.');

        $post->groups()->detach($group);
        $group->posts()->detach($post);

        return back()->with('success', 'Post removed from the group.');
    }

    public function update(Request $request, UserAuthenticationService $authService, GroupManagmentService $groupService)
    {
        $request->validate([
            'group_id' => 'required|exists:groups,id',
            'name' => 'required|string|max:255|unique:groups,name,' . $request->group_id, // Ignore current group ID
            'description' => 'nullable|string|max:255',
        ]);

        $group          = Group::findOrFail($request->group_id);

        $status         = $groupService->get_membership_status($group->id);
        $loggedUserID   = Auth()->check() ? Auth()->user()->id : -1;

        if (! ($status->value === GroupMembership::OWNER->value) &&
            !$authService->role_access(UserRole::MODERATOR))
            return back()->with('error', 'Insufficient rights.');

        $imagePath = null;
        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $imageName = uniqid();
            Storage::disk('public')->put('uploads/' . $imageName, file_get_contents($photo));
            $imagePath = '/storage/uploads/' . $imageName;
        }

        if($imagePath)
            $group->profile_picture = $imagePath;

        $group->name            = $request->name;
        $group->description     = $request->description;

        $group->update();

        return redirect()->route('group', ['name' => $group->name])
        ->with('success', 'Group updated.');
    }
}
