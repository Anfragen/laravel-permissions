<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

class ModelPermissionObserver
{
    /**
     * Handle the ModelPermission "created" event.
     */
    public function created(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelPermission "updated" event.
     */
    public function updated(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelPermission "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearCache();
    }

    /**
     * Delete Model Permissions cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::modelPermissions());
    }
}
