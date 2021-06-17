<?php

namespace App\Contracts\Auth;

interface RolesPermissionsInterface
{
    public const ROLE_HAS_ALL_PERMISSIONS = 'all';

    public const ROLE_PERMISSIONS = [
        RolesInterface::ROLE_USER  => [
            PermissionsInterface::REPORT_PAGE
        ],
        RolesInterface::ROLE_ADMIN => [
            self::ROLE_HAS_ALL_PERMISSIONS
        ],
    ];
}
