<?php

declare(strict_types=1);

namespace App\Enums;

enum UserRole: string
{
    case SUPER_ADMIN = 'superadmin';
    case ADMIN = 'admin';
    case MODERATOR = 'moderator';
    case USER = 'user';

    public function isSuperAdmin(): bool
    {
        return $this === self::SUPER_ADMIN;
    }

    public function isModerator(): bool
    {
        return str_contains(strtolower($this->value), 'moder');
    }
}
