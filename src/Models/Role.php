<?php

namespace Anfragen\Permission\Models;

use Anfragen\Permission\Factories\RoleFactory;
use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\{Collection, Model};
use Illuminate\Database\Eloquent\Factories\{Factory, HasFactory};
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

    /**
     * Get the factory instance for the model.
     */
    protected static function newFactory(): Factory
    {
        return RoleFactory::new();
    }

    /**
     * Relationship with the Permission Model
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)
            ->using(PermissionRole::class)
            ->withTimestamps();
    }

    /**
     * Get all the data and add it to the cache
     */
    public static function getAllFromCache(): Collection
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
    public static function getRole(int|string $role): mixed
    {
        return self::getAllFromCache()->filter(
            fn ($value) => $value->id === $role || $value->uuid === $role || $value->name === $role || $value->slug === $role
        )->first();
    }
}
