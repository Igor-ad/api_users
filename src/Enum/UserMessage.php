<?php

declare(strict_types=1);

namespace App\Enum;

enum UserMessage
{
    public const ALL = 'All users.';
    public const SHOW = 'View user information.';
    public const CREATE = 'New user created.';
    public const UPDATE = 'User information has been updated.';
    public const DELETE = 'The user has been removed from the system.';
}
