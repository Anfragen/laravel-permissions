<?php

namespace Anfragen\Permission\Observers;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RoleObserver
{
    /**
     * Handle the Role "creating" event.
     */
    public function creating(Role $role): void
    {
        if (is_null($role->slug)) {
            $role->slug = Str::slug("{$role->name}");
        }
    }

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
