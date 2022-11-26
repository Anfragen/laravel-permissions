<?php

namespace Anfragen\Permission\Facades;

use Anfragen\Permission\Support\CacheKeys as SupportCacheKeys;
use Illuminate\Support\Facades\Facade;

class CacheKeys extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SupportCacheKeys::class;
    }
}
