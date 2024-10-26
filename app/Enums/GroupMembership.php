<?php

namespace App\Enums;

enum GroupMembership: int
{
    case NONE               = 0;
    case REQUEST_PENDING    = 1;
    case MEMBER             = 2;
    case OWNER              = 3;

    public function label(): string
    {
        return match($this) {
            self::NONE              => 'Not member',
            self::REQUEST_PENDING   => 'Request sent',
            self::MEMBER            => 'Member',
            self::OWNER             => 'Owner',
        };
    }
}
