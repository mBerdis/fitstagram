<?php

namespace App\Services;
use App\Services\UserAuthenticationService;
use Illuminate\Support\Facades\Auth;
use App\Models\Group;
use App\Models\User;
use App\Enums\UserRole;
use App\Enums\GroupMembership;

class GroupManagmentService
{
    public function get_membership_status($group_id): GroupMembership
    {
        if (!Auth::check())
        {
            return GroupMembership::NONE;
        }

        $group          = Group::findOrFail($group_id);
        $loggedUser     = Auth::user();

        $memberIds      = $group->members->pluck('id')->toArray();
        $memberReqIds   = $group->joinRequests->pluck('user_id')->toArray();

        if ($loggedUser->id == $group->owner->id)       return GroupMembership::OWNER;
        if (in_array($loggedUser->id, $memberIds))      return GroupMembership::MEMBER;
        if (in_array($loggedUser->id, $memberReqIds))   return GroupMembership::REQUEST_PENDING;

        return GroupMembership::NONE;
    }
}
