<?php

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\ModelPermission;
use Anfragen\Permission\Observers\ModelPermissionObserver;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->modelPermission = ModelPermission::inRandomOrder()->first();
});

test('clear user roles cache when trigger create event', function () {
    Cache::spy();

    $observer = new ModelPermissionObserver();

    $observer->created($this->modelPermission);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelPermissions());
});

test('clear user roles cache when trigger update event', function () {
    Cache::spy();

    $observer = new ModelPermissionObserver();

    $observer->updated($this->modelPermission);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelPermissions());
});

test('clear user roles cache when trigger delete event', function () {
    Cache::spy();

    $observer = new ModelPermissionObserver();

    $observer->deleted($this->modelPermission);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelPermissions());
});
