<?php

namespace App\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\ModelPermission;
use Illuminate\Support\Facades\Cache;

class ModelPermissionObserver
{
    /**
     * Handle the ModelPermission "created" event.
     */
    public function created(ModelPermission $modelPermission): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelPermission "updated" event.
     */
    public function updated(ModelPermission $modelPermission): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelPermission "deleted" event.
     */
    public function deleted(ModelPermission $modelPermission): void
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
