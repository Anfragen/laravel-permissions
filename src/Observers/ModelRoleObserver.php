<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

class ModelRoleObserver
{
    /**
     * Handle the ModelRole "created" event.
     */
    public function created(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelRole "updated" event.
     */
    public function updated(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelRole "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearCache();
    }

    /**
     * Delete Model Roles cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::modelRoles());
    }
}
