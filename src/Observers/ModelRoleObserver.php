<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\ModelRole;
use Illuminate\Support\Facades\Cache;

class ModelRoleObserver
{
    /**
     * Handle the ModelRole "created" event.
     */
    public function created(ModelRole $modelRole): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelRole "updated" event.
     */
    public function updated(ModelRole $modelRole): void
    {
        $this->clearCache();
    }

    /**
     * Handle the ModelRole "deleted" event.
     */
    public function deleted(ModelRole $modelRole): void
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
