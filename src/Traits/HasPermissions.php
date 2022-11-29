<?php

namespace Anfragen\Permission\Traits;

use Anfragen\Permission\Models\{Permission, PermissionRole};
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

trait HasPermissions
{
    /**
     * Get all permissions of the role.
     */
    public function getPermissions(): EloquentCollection
    {
        $rolePermissions = PermissionRole::getRolePermissions($this);

        return $rolePermissions->map(
            fn ($rolePermission) => Permission::getPermission($rolePermission->permission_id)
        )->values();
    }

    /**
     * Check if the role has a specific permission.
     */
    public function hasPermissionTo(int|string $permission): bool
    {
        $permission = Permission::getPermission($permission);

        $rolePermissions = PermissionRole::getRolePermissions($this);

        return $rolePermissions->where('permission_id', $permission?->id)->isNotEmpty();
    }

    /**
     * Check if the role has any of the given permissions.
     */
    public function hasAnyPermission(Collection|array $permissions): bool
    {
        return collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(true);
    }

    /**
     * Check if the role has all of the given permissions.
     */
    public function hasAllPermissions(Collection|array $permissions): bool
    {
        return !collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(false);
    }

    /**
     * Give the given permission to the role.
     */
    public function assignPermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revoke the given permission to the role.
     */
    public function revokePermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->detach($permission->id);
    }

    /**
     * Sync the given permissions to the role.
     */
    public function syncPermissions(Collection|array $permissions): void
    {
        $permissions = collect($permissions)->map(
            fn ($permission) => Permission::getPermission($permission)
        )->pluck('id');

        $this->permissions()->sync($permissions);
    }
}
