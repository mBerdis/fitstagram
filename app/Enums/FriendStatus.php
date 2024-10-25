<?php

namespace App\Enums;

enum FriendStatus: int
{
    case NONE               = 0;
    case REQUEST_PENDING    = 1;
    case FRIENDSHIP         = 2;
    case THATS_ME           = 3;    // used when viewing my own page

    public function label(): string
    {
        return match($this) {
            self::NONE              => 'Not friends',
            self::REQUEST_PENDING   => 'Request sent',
            self::FRIENDSHIP        => 'Friends',
            self::THATS_ME          => '',
        };
    }
}
