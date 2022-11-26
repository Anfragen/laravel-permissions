<?php

namespace Anfragen\Permission\Models;

use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, MorphTo, Pivot};
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class ModelRole extends Pivot
{
    use HasUuids;
    use HasFactory;

    public $incrementing = true;

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['uuid'];
    }

    /**
     * Relationship with the Polymorphic Model
     */
    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Relationship with the Role Model
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

        /**
         * Get all the data and add it to the cache
         */
    public static function getAllFromCache()
    {
        return Cache::remember(
            CacheKeys::modelRoles(),
            Carbon::now()->addMinutes(config('permission.cache.expiration_time', 1440)),
            fn () => self::all()
        );
    }

    /**
     * Return specific data from cache
     */
    public static function getModelRoles(Model $model)
    {
        return self::getAllFromCache()->where('model_type', $model)->where('model_id', $model->id);
    }
}
