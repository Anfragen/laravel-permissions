<?php

namespace Anfragen\Permission\Traits;

use Anfragen\Permission\Models\{ModelPermission, ModelRole, Permission, Role};
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;

trait HasRoles
{
    /*
    |--------------------------------------------------------------------------
    | Roles
    |--------------------------------------------------------------------------
    */

    /**
     * Relationship with the Model Role Pivot.
     */
    public function roles(): MorphToMany
    {
        return $this->morphToMany(Role::class, 'model', 'model_role')
            ->using(ModelRole::class)
            ->withTimestamps();
    }

    /**
     * Get all roles of the model.
     */
    public function getRoles(): EloquentCollection
    {
        $userRoles = ModelRole::getModelRoles($this);

        return $userRoles->map(
            fn ($userRole) => Role::getRole($userRole->role_id)
        )->values();
    }

    /**
     * Check if the model has a specific role.
     */
    public function hasRoleTo(int|string $role): bool
    {
        $role = Role::getRole($role);

        $userRoles = ModelRole::getModelRoles($this);

        return $userRoles->where('role_id', $role?->id)->isNotEmpty();
    }

    /**
     * Check if the model has any of the given roles.
     */
    public function hasAnyRole(Collection|array $roles): bool
    {
        return collect($roles)->map(
            fn ($role) => $this->hasRoleTo($role)
        )->contains(true);
    }

    /**
     * Check if the model has all of the given roles.
     */
    public function hasAllRoles(Collection|array $roles): bool
    {
        return !collect($roles)->map(
            fn ($role) => $this->hasRoleTo($role)
        )->contains(false);
    }

    /**
     * Give the given role to the model.
     */
    public function assignRoleTo(int|string $role): void
    {
        $role = Role::getRole($role);

        $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Revoke the given role to the model.
     */
    public function revokeRoleTo(int|string $role): void
    {
        $role = Role::getRole($role);

        $this->roles()->detach($role->id);
    }

    /**
     * Sync the given roles to the model.
     */
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

    /**
     * Relationship with the Model Permission Pivot.
     */
    public function permissions(): MorphToMany
    {
        return $this->morphToMany(Permission::class, 'model', 'model_permission')
            ->using(ModelPermission::class)
            ->withTimestamps();
    }

    /**
     * Get all permissions of the model.
     */
    public function getPermissions(): EloquentCollection
    {
        $modelPermissions = ModelPermission::getModelPermissions($this);

        return $modelPermissions->map(
            fn ($modelPermission) => Permission::getPermission($modelPermission->permission_id)
        )->values();
    }

    /**
     * Check if the model has a specific permission.
     */
    public function hasPermissionTo(int|string $permission): bool
    {
        $permission = Permission::getPermission($permission);

        $modelPermissions = ModelPermission::getModelPermissions($this);

        return $modelPermissions->where('permission_id', $permission?->id)->isNotEmpty();
    }

    /**
     * Check if the model has any of the given permissions.
     */
    public function hasAnyPermission(Collection|array $permissions): bool
    {
        return collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(true);
    }

    /**
     * Check if the model has all of the given permissions.
     */
    public function hasAllPermissions(Collection|array $permissions): bool
    {
        return !collect($permissions)->map(
            fn ($permission) => $this->hasPermissionTo($permission)
        )->contains(false);
    }

    /**
     * Give the given permission to the model.
     */
    public function assignPermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->syncWithoutDetaching([$permission->id]);
    }

    /**
     * Revoke the given permission to the model.
     */
    public function revokePermissionTo(int|string $permission): void
    {
        $permission = Permission::getPermission($permission);

        $this->permissions()->detach($permission->id);
    }

    /**
     * Sync the given permissions to the model.
     */
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

    /**
     * Get all permissions of the model via roles.
     */
    public function getPermissionsByRole(): EloquentCollection
    {
        $permissions = new EloquentCollection();

        $roles = $this->getRoles();

        $roles->each(function ($role) use (&$permissions) {
            $permissions = $permissions->merge($role->getPermissions());
        });

        return $permissions->unique()->values();
    }

    /**
     * Check if the model has a specific permission via roles.
     */
    public function hasPermissionByRoleTo(int|string $permission): bool
    {
        $roles = $this->getRoles();

        return $roles->map(
            fn ($role) => $role->hasPermissionTo($permission)
        )->contains(true);
    }

    /**
     * Check if the model has any of the given permissions via roles.
     */
    public function hasAnyPermissionByRole(Collection|array $permissions): bool
    {
        $roles = $this->getRoles();

        return $roles->map(
            fn ($role) => $role->hasAnyPermission($permissions)
        )->contains(true);
    }

    /**
     * Check if the model has all of the given permissions via roles.
     */
    public function hasAllPermissionsByRole(Collection|array $permissions): bool
    {
        $roles = $this->getRoles();

        return !collect($permissions)->map(
            fn ($permission) => $roles->map(
                fn ($role) => $role->hasPermissionTo($permission)
            )->contains(true)
        )->contains(false);
    }
}
