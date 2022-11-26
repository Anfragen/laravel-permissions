<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\PermissionRole;
use Illuminate\Support\Facades\Cache;

class PermissionRoleObserver
{
    /**
     * Handle the PermissionRole "created" event.
     */
    public function created(PermissionRole $permissionRole): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PermissionRole "updated" event.
     */
    public function updated(PermissionRole $permissionRole): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PermissionRole "deleted" event.
     */
    public function deleted(PermissionRole $permissionRole): void
    {
        $this->clearCache();
    }

    /**
     * Delete Permission Roles cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::permissionsRoles());
    }
}
