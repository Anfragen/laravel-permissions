<?php

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Models\PermissionRole;
use Anfragen\Permission\Observers\PermissionRoleObserver;
use Illuminate\Support\Facades\Cache;

beforeEach(function () {
    $this->permissionRole = PermissionRole::inRandomOrder()->first();
});

test('clear role permissions cache when trigger create event', function () {
    Cache::spy();

    $observer = new PermissionRoleObserver();

    $observer->created($this->permissionRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::permissionRoles());
});

test('clear role permissions cache when trigger update event', function () {
    Cache::spy();

    $observer = new PermissionRoleObserver();

    $observer->updated($this->permissionRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::permissionRoles());
});

test('clear role permissions cache when trigger delete event', function () {
    Cache::spy();

    $observer = new PermissionRoleObserver();

    $observer->deleted($this->permissionRole);

    Cache::shouldHaveReceived('forget')->with(CacheKeys::permissionRoles());
});
