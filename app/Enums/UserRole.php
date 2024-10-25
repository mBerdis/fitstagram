<?php

namespace App\Enums;

enum UserRole: int
{
    case BANNED = 0;
    case SILENCED = 1;
    case USER = 2;
    case MODERATOR = 3;
    case ADMIN = 4;

    public function label(): string
    {
        return match($this) {
            self::BANNED => 'Banned',
            self::SILENCED => 'Silenced',
            self::USER => 'User',
            self::MODERATOR => 'Moderator',
            self::ADMIN => 'Administrator'
        };
    }
}
