<?php

namespace App\Services;
use App\Enums\UserRole;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class UserAuthenticationService
{

    public static function role_access($min_role): bool
    {
        $loggedUser = Auth::user();

        if (!$loggedUser)
            return false;

        if ($loggedUser->role->value >= $min_role->value) {
            return true;
        }
        return false;
    }

}
