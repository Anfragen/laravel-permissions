<?php

namespace Anfragen\Permission\Models;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class Permission extends Model
{
    use HasUuids;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'group',
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

    /**
     * Relationship with the Role Model
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)
            ->using(PermissionRole::class)
            ->withTimestamps();
    }

        /**
         * Get all the data and add it to the cache
         */
    public static function getAllFromCache()
    {
        return Cache::remember(
            CacheKeys::permissions(),
            Carbon::now()->addMinutes(config('permission.cache.expiration_time', 1440)),
            fn () => self::all()
        );
    }

    /**
     * Return specific data from cache
     */
    public static function getPermission(int|string $permission)
    {
        return self::getAllFromCache()->filter(
            fn ($value) => $value->id === $permission || $value->slug === $permission
        )->first();
    }
}
