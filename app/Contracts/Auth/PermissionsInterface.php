<?php

namespace App\Contracts\Auth;

interface PermissionsInterface
{
    public const REPORT_PAGE = 'Report page';
    public const WORKSPACES_PAGE = 'Workspaces page';
    public const ALL_PERMISSIONS = [
        self::REPORT_PAGE,
        self::WORKSPACES_PAGE,
    ];
}
