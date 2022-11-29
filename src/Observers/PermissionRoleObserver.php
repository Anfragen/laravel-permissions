<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

class PermissionRoleObserver
{
    /**
     * Handle the PermissionRole "created" event.
     */
    public function created(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PermissionRole "updated" event.
     */
    public function updated(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the PermissionRole "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearCache();
    }

    /**
     * Delete Permission Roles cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::permissionRoles());
    }
}
