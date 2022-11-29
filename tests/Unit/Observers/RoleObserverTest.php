<?php

use Anfragen\Permission\Facades\CacheKeys;
use Anfragen\Permission\Observers\RoleObserver;
use Illuminate\Support\Facades\Cache;

test('clear roles cache when trigger create event', function () {
    Cache::spy();

    $observer = new RoleObserver();

    $observer->created();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::roles());
});

test('clear roles cache when trigger update event', function () {
    Cache::spy();

    $observer = new RoleObserver();

    $observer->updated();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::roles());
});

test('clear roles cache when trigger delete event', function () {
    Cache::spy();

    $observer = new RoleObserver();

    $observer->deleted();

    Cache::shouldHaveReceived('forget')->once()->with(CacheKeys::roles());
});
