<?php

namespace Anfragen\Permission\Models;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, Pivot};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class PermissionRole extends Pivot
{
    use HasUuids;

    public $incrementing = true;

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Relationship with the Role Model
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Relationship with the Permission Model
     */
    public function permission(): BelongsTo
    {
        return $this->belongsTo(Permission::class);
    }

    /**
     * Get all the data and add it to the cache
     */
    public static function getAllFromCache(): Collection
    {
        return Cache::remember(
            CacheKeys::permissionRoles(),
            Carbon::now()->addMinutes(config('permission.cache.expiration_time', 1440)),
            fn () => self::all()
        );
    }

    /**
     * Return specific data from cache
     */
    public static function getRolePermissions(Role $role): mixed
    {
        return self::getAllFromCache()->where('role_id', $role->id);
    }
}
