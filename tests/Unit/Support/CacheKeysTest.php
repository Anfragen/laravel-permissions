<?php

use Anfragen\Permission\Support\CacheKeys;

beforeEach(function () {
    $this->cacheKeys = new CacheKeys();
});

test('return the key that stores the roles in the cache', function () {
    expect($this->cacheKeys->roles())->toBe('anfragen::permissions::roles');
});

test('return the key that stores the model roles in the cache', function () {
    expect($this->cacheKeys->modelRoles())->toBe('anfragen::permissions::model::roles');
});

test('return the key that stores the permissions in the cache', function () {
    expect($this->cacheKeys->permissions())->toBe('anfragen::permissions::permissions');
});

test('return the key that stores the permission roles in the cache', function () {
    expect($this->cacheKeys->permissionRoles())->toBe('anfragen::permissions::permission::roles');
});

test('return the key that stores the model permissions in the cache', function () {
    expect($this->cacheKeys->modelPermissions())->toBe('anfragen::permissions::model::permissions');
});
