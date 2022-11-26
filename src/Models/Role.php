<?php

namespace Anfragen\Permission\Models;

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Traits\Models\HasPermissions;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Role extends Model
{
    use HasUuids;
    use HasFactory;
    use HasPermissions;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->using(PermissionRole::class)
            ->withTimestamps();
    }

        /**
         * Get all the data and add it to the cache
         */
    public static function getAllFromCache()
    {
        return Cache::remember(
            CacheKeys::roles(),
            Carbon::now()->addMinutes(config('permission.cache.expiration_time', 1440)),
            fn () => self::all()
        );
    }

    /**
     * Return specific data from cache
     */
    public static function getRole(int|string $role)
    {
        return self::getAllFromCache()->filter(
            fn ($value) => $value->id === $role || $value->name === $role || $value->slug === $role
        )->first();
    }
}
