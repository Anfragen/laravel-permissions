<?php

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Observers\PermissionObserver;
use Illuminate\Support\Facades\Cache;

test('clear permissions cache when trigger create event', function () {
    Cache::spy();

    $observer = new PermissionObserver();

    $observer->created();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::permissions());
});

test('clear permissions cache when trigger update event', function () {
    Cache::spy();

    $observer = new PermissionObserver();

    $observer->updated();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::permissions());
});

test('clear permissions cache when trigger delete event', function () {
    Cache::spy();

    $observer = new PermissionObserver();

    $observer->deleted();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::permissions());
});
