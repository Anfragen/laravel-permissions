<?php

namespace Anfragen\Permission\Traits\Models;

use Anfragen\Permission\Models\{Permission, PermissionRole};
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

trait HasPermissions
{
    public function getPermissions(): EloquentCollection
    {
        $rolePermissions = PermissionRole::getRolePermissions($this);

        return $rolePermissions->map(
            fn ($rolePermission) => Permission::getPermission($rolePermission->permission_id)
        )->values();
    }

    public function hasPermissionTo(int|string $permission): bool
    {
        $permission = Permission::getPermission($permission);

        $rolePermissions = PermissionRole::getRolePermissions($this);

        return $rolePermissions->where('permission_id', $permission?->id)->isNotEmpty();
    }

    public function hasAnyPermission(Collection|array $permissions): bool
    {
        return collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(true);
    }

    public function hasAllPermissions(Collection|array $permissions): bool
    {
        return !collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(false);
    }

    public function assignPermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    public function revokePermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->detach($permission->id);
    }

    public function syncPermissions(Collection|array $permissions): void
    {
        $permissions = collect($permissions)->map(
            fn ($permission) => Permission::getPermission($permission)
        )->pluck('id');

        $this->permissions()->sync($permissions);
    }
}
