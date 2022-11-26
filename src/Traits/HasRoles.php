<?php

namespace Anfragen\Permission\Traits\Models;

use Anfragen\Permission\Models\{ModelRole, Permission, PermissionRole, Role};
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(ModelRole::class)
            ->withTimestamps();
    }

    public function getRoles(): EloquentCollection
    {
        $userRoles = ModelRole::getModelRoles($this);

        return $userRoles->map(
            fn ($userRole) => Role::getRole($userRole->role_id)
        )->values();
    }

    public function hasRoleTo(int|string $role): bool
    {
        $role = Role::getRole($role);

        $userRoles = ModelRole::getModelRoles($this);

        return $userRoles->where('role_id', $role?->id)->isNotEmpty();
    }

    public function hasAnyRole(Collection|array $roles): bool
    {
        return collect($roles)->map(
            fn ($role) => $this->hasRoleTo($role)
        )->contains(true);
    }

    public function hasAllRoles(Collection|array $roles): bool
    {
        return !collect($roles)->map(
            fn ($role) => $this->hasRoleTo($role)
        )->contains(false);
    }

    public function assignRoleTo(int|string $role): void
    {
        $role = Role::getRole($role);

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    public function revokeRoleTo(int|string $role): void
    {
        $role = Role::getRole($role);

        $this->roles()->detach($role->id);
    }

    public function syncRoles(Collection|array $roles): void
    {
        $roles = collect($roles)->map(
            fn ($role) => Role::getRole($role)
        )->pluck('id');

        $this->roles()->sync($roles);
    }

    /*
    |--------------------------------------------------------------------------
    | Permissions
    |--------------------------------------------------------------------------
    */

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->using(ModelPermission::class)
            ->withTimestamps();
    }

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

    /*
    |--------------------------------------------------------------------------
    | Permission via Roles
    |--------------------------------------------------------------------------
    */

    public function getRolePermissions(): EloquentCollection
    {
        $permissions = new EloquentCollection();

        $roles = $this->getRoles();

        $roles->each(function ($role) use (&$permissions) {
            $permissions = $permissions->merge($role->getPermissions());
        });

        return $permissions->unique()->values();
    }

    public function hasRolePermissionTo(int|string $permission): bool
    {
        $roles = $this->getRoles();

        return $roles->map(
            fn ($role) => $role->hasPermissionTo($permission)
        )->contains(true);
    }

    public function hasRoleAnyPermission(Collection|array $permissions): bool
    {
        $roles = $this->getRoles();

        return $roles->map(
            fn ($role) => $role->hasAnyPermission($permissions)
        )->contains(true);
    }

    public function hasRoleAllPermissions(Collection|array $permissions): bool
    {
        $roles = $this->getRoles();

        return !collect($permissions)->map(
            fn ($permission) => $roles->map(
                fn ($role) => $role->hasPermissionTo($permission)
            )->contains(true)
        )->contains(false);
    }
}
