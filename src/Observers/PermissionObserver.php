<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PermissionObserver
{
    /**
     * Handle the Permission "creating" event.
     */
    public function creating(Permission $permission): void
    {
        if (is_null($permission->slug)) {
            $permission->slug = Str::slug("{$permission->group} {$permission->name}");
        }
    }

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
