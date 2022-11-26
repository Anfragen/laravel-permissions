<?php

namespace App\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(): void
    {
        $this->clearCache();
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(): void
    {
        $this->clearCache();
    }

    /**
     * Delete Roles cache.
     */
    private function clearCache(): void
    {
        Cache::forget(CacheKeys::roles());
    }
}
