<?php

namespace App\Enums;

enum FriendStatus: int
{
    case NONE               = 0;
    case REQUEST_PENDING    = 1;
    case FRIENDSHIP         = 2;
    case REQUEST_RECEIVED   = 3;
    case THATS_ME           = 4;    // used when viewing my own page

    public function label(): string
    {
        return match($this) {
            self::NONE              => 'Not friends',
            self::REQUEST_PENDING   => 'Request sent',
            self::FRIENDSHIP        => 'Friends',
            self::REQUEST_RECEIVED  => 'Request recieved',
            self::THATS_ME          => '',
        };
    }
}
