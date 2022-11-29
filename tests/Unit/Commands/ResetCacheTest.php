<?php

use Anfragen\Permission\Commands\ResetCache;
use Anfragen\Permission\Facades\CacheKeys;
use Illuminate\Support\Facades\Cache;

test('clear cache using the reset cache command', function () {
    Cache::spy();

    $observer = new ResetCache();

    $observer->handle();

    Cache::shouldHaveReceived('forget')->with(CacheKeys::roles());

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelRoles());

    Cache::shouldHaveReceived('forget')->with(CacheKeys::permissions());

    Cache::shouldHaveReceived('forget')->with(CacheKeys::permissionRoles());

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelPermissions());
});
