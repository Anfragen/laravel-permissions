<?php

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\ModelRole;
use Anfragen\Permission\Observers\ModelRoleObserver;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->modelRole = ModelRole::inRandomOrder()->first();
});

test('clear user roles cache when trigger create event', function () {
    Cache::spy();

    $observer = new ModelRoleObserver();

    $observer->created($this->modelRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelRoles());
});

test('clear user roles cache when trigger update event', function () {
    Cache::spy();

    $observer = new ModelRoleObserver();

    $observer->updated($this->modelRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelRoles());
});

test('clear user roles cache when trigger delete event', function () {
    Cache::spy();

    $observer = new ModelRoleObserver();

    $observer->deleted($this->modelRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::modelRoles());
});
