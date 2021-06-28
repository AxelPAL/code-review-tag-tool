<?php

namespace App\Contracts\Auth;

interface PermissionsInterface
{
    public const REPORT_PAGE = 'Report page';
    public const WORKSPACES_PAGE = 'Workspaces page';
    public const ADMIN_PANEL_ACCESS = 'Admin panel access';
    public const ALL_PERMISSIONS = [
        self::REPORT_PAGE,
        self::WORKSPACES_PAGE,
        self::ADMIN_PANEL_ACCESS,
    ];
}
