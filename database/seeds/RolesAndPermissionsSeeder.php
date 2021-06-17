<?php

namespace Database\Seeders;

use App\Contracts\Auth\PermissionsInterface;
use App\Contracts\Auth\RolesPermissionsInterface;
use Illuminate\Database\Seeder;
use Spatie\Permission\Exceptions\PermissionAlreadyExists;
use Spatie\Permission\Exceptions\RoleDoesNotExist;
use Spatie\Permission\Guard;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $guardName = Guard::getDefaultName(Permission::class);
        foreach (PermissionsInterface::ALL_PERMISSIONS as $permission) {
            try {
                Permission::create(['name' => $permission, 'guard_name' => $guardName]);
            } catch (PermissionAlreadyExists) {
            }
        }
        foreach (RolesPermissionsInterface::ROLE_PERMISSIONS as $roleName => $permissions) {
            try {
                $role = Role::findByName($roleName);
            } catch (RoleDoesNotExist) {
                $role = Role::create(['name' => $roleName]);
            }
            foreach ($permissions as $permission) {
                if ($permission === RolesPermissionsInterface::ROLE_HAS_ALL_PERMISSIONS) {
                    $permission = Permission::all();
                }
                $role->givePermissionTo($permission);
            }
        }
    }
}
