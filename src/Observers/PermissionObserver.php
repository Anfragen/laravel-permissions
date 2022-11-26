<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updated(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearCache();
    }

    /**
     * Delete Permissions cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::permissions());
    }
}
