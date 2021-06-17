<?php

namespace App\Contracts\Auth;

interface RolesInterface
{
    public const ROLE_ADMIN = 'admin';
    public const ROLE_USER = 'user';
    public const ROLE_NO_ONE = 'no-one';
}
